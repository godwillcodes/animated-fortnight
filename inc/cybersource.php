<?php
/**
 * CyberSource Unified Checkout integration for donations.
 *
 * Requires: merchant ID, API key serial number (keyid), and shared secret from Business Centre.
 * Configure via filter 'boniface_cybersource_config' or constants in wp-config.php.
 *
 * @package PiedmontGlobal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get CyberSource config. Prefer constants, then Theme Customizer (options), then filter.
 *
 * @return array{merchant_id: string, key_id: string, shared_secret: string, env: string}|null
 */
function boniface_cybersource_config() {
	$default = array(
		'merchant_id'   => defined( 'CYBERSOURCE_MERCHANT_ID' ) ? CYBERSOURCE_MERCHANT_ID : get_theme_mod( 'cybersource_merchant_id', '' ),
		'key_id'        => defined( 'CYBERSOURCE_KEY_ID' ) ? CYBERSOURCE_KEY_ID : get_theme_mod( 'cybersource_key_id', '' ),
		'shared_secret' => defined( 'CYBERSOURCE_SHARED_SECRET' ) ? CYBERSOURCE_SHARED_SECRET : get_theme_mod( 'cybersource_shared_secret', '' ),
		'env'           => defined( 'CYBERSOURCE_ENV' ) ? CYBERSOURCE_ENV : get_theme_mod( 'cybersource_env', 'test' ),
	);
	$config = apply_filters( 'boniface_cybersource_config', $default );
	return ( ! empty( $config['merchant_id'] ) && ! empty( $config['key_id'] ) && ! empty( $config['shared_secret'] ) ) ? $config : null;
}

/**
 * Get CyberSource API base URL for the configured environment.
 *
 * @return string
 */
function boniface_cybersource_base_url() {
	$config = boniface_cybersource_config();
	$host   = ( $config && $config['env'] === 'production' ) ? 'https://api.cybersource.com' : 'https://apitest.cybersource.com';
	return $host;
}

/**
 * Build HTTP Signature headers for CyberSource REST.
 * Uses v-c-date (not Date) and header order: host, v-c-date, request-target, digest, v-c-merchant-id.
 * Shared secret is used as raw bytes; if CyberSource returns it Base64-encoded, decode first.
 *
 * @param string $method   GET or POST.
 * @param string $resource Path e.g. /up/v1/capture-contexts or /pts/v2/payments.
 * @param string $body     Request body (JSON) or empty for GET.
 * @return array Headers including Digest, v-c-date, v-c-merchant-id, Signature.
 */
function boniface_cybersource_signature_headers( $method, $resource, $body = '' ) {
	$config = boniface_cybersource_config();
	if ( ! $config ) {
		return array();
	}

	$host = wp_parse_url( boniface_cybersource_base_url(), PHP_URL_HOST );
	$date = gmdate( 'D, d M Y H:i:s \G\M\T' );
	$request_target = strtolower( $method ) . ' ' . $resource;
	$digest = '';
	if ( $body !== '' ) {
		$digest = 'SHA-256=' . base64_encode( hash( 'sha256', $body, true ) );
	}

	// CyberSource signature header order: digest, host, request-target, v-c-date, v-c-merchant-id (per docs).
	$signature_params = array(
		'host'             => $host,
		'request-target'   => $request_target,
		'v-c-date'         => $date,
		'v-c-merchant-id'  => $config['merchant_id'],
	);
	if ( $digest ) {
		$signature_params = array_merge( array( 'digest' => $digest ), $signature_params );
	}

	$header_names = array_keys( $signature_params );
	$signature_string = implode( "\n", array_map( function ( $k ) use ( $signature_params ) {
		return $k . ': ' . $signature_params[ $k ];
	}, $header_names ) );

	// Shared secret from Business Centre may be Base64-encoded; try decoded first, fallback to raw.
	$secret = $config['shared_secret'];
	$decoded = base64_decode( $secret, true );
	if ( $decoded !== false && strlen( $decoded ) > 0 ) {
		$secret = $decoded;
	}
	$signature_hash = base64_encode( hash_hmac( 'sha256', $signature_string, $secret, true ) );
	$headers_list = implode( ' ', $header_names );
	$signature_header = sprintf(
		'keyid="%s", algorithm="HmacSHA256", headers="%s", signature="%s"',
		$config['key_id'],
		$headers_list,
		$signature_hash
	);

	$out = array(
		'Content-Type'     => 'application/json',
		'Host'             => $host,
		'v-c-date'         => $date,
		'v-c-merchant-id'  => $config['merchant_id'],
		'Signature'        => $signature_header,
	);
	if ( $digest ) {
		$out['Digest'] = $digest;
	}
	return $out;
}

/**
 * Normalize US state code to 2-letter format required by CyberSource.
 * Converts full state names to codes (e.g. "California" -> "CA") and validates.
 *
 * @param string $state State name or code.
 * @return string 2-letter state code or empty if invalid.
 */
function boniface_cybersource_normalize_state( $state ) {
	if ( empty( $state ) ) {
		return '';
	}
	$state = trim( strtoupper( $state ) );
	// If already 2 letters and looks like a code, return as-is.
	if ( strlen( $state ) === 2 && ctype_alpha( $state ) ) {
		return $state;
	}
	// Map common full state names to 2-letter codes.
	$state_map = array(
		'ALABAMA' => 'AL', 'ALASKA' => 'AK', 'ARIZONA' => 'AZ', 'ARKANSAS' => 'AR',
		'CALIFORNIA' => 'CA', 'COLORADO' => 'CO', 'CONNECTICUT' => 'CT', 'DELAWARE' => 'DE',
		'FLORIDA' => 'FL', 'GEORGIA' => 'GA', 'HAWAII' => 'HI', 'IDAHO' => 'ID',
		'ILLINOIS' => 'IL', 'INDIANA' => 'IN', 'IOWA' => 'IA', 'KANSAS' => 'KS',
		'KENTUCKY' => 'KY', 'LOUISIANA' => 'LA', 'MAINE' => 'ME', 'MARYLAND' => 'MD',
		'MASSACHUSETTS' => 'MA', 'MICHIGAN' => 'MI', 'MINNESOTA' => 'MN', 'MISSISSIPPI' => 'MS',
		'MISSOURI' => 'MO', 'MONTANA' => 'MT', 'NEBRASKA' => 'NE', 'NEVADA' => 'NV',
		'NEW HAMPSHIRE' => 'NH', 'NEW JERSEY' => 'NJ', 'NEW MEXICO' => 'NM', 'NEW YORK' => 'NY',
		'NORTH CAROLINA' => 'NC', 'NORTH DAKOTA' => 'ND', 'OHIO' => 'OH', 'OKLAHOMA' => 'OK',
		'OREGON' => 'OR', 'PENNSYLVANIA' => 'PA', 'RHODE ISLAND' => 'RI', 'SOUTH CAROLINA' => 'SC',
		'SOUTH DAKOTA' => 'SD', 'TENNESSEE' => 'TN', 'TEXAS' => 'TX', 'UTAH' => 'UT',
		'VERMONT' => 'VT', 'VIRGINIA' => 'VA', 'WASHINGTON' => 'WA', 'WEST VIRGINIA' => 'WV',
		'WISCONSIN' => 'WI', 'WYOMING' => 'WY', 'DISTRICT OF COLUMBIA' => 'DC',
	);
	return isset( $state_map[ $state ] ) ? $state_map[ $state ] : '';
}

/**
 * Request Capture Context from CyberSource; returns JWT and client library info for frontend.
 *
 * @param float  $amount   Order amount (e.g. 25.00).
 * @param string $currency Currency code (e.g. USD).
 * @param string $origin   Allowed target origin (e.g. https://yoursite.com).
 * @param array  $bill_to  Optional billTo for capture context (firstName, lastName, email).
 * @return array{success: bool, capture_context?: string, client_library?: string, client_library_integrity?: string, error?: string}
 */
function boniface_cybersource_get_capture_context( $amount, $currency = 'USD', $origin = '', $bill_to = array() ) {
	$config = boniface_cybersource_config();
	if ( ! $config ) {
		return array( 'success' => false, 'error' => 'CyberSource is not configured.' );
	}

	if ( $amount < 10 ) {
		return array( 'success' => false, 'error' => 'Minimum amount is 10.' );
	}

	$base_url = boniface_cybersource_base_url();
	$resource = '/up/v1/capture-contexts';

	$total_amount = number_format( (float) $amount, 2, '.', '' );
	if ( empty( $origin ) ) {
		$origin = home_url( '', 'https' );
		if ( strpos( $origin, 'https://' ) !== 0 ) {
			$origin = 'https://' . ( isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : 'localhost' );
		}
	}

	$payload = array(
		'targetOrigins'    => array( $origin ),
		'clientVersion'    => '0.34',
		'buttonType'       => 'CHECKOUT_AND_CONTINUE',
		'allowedCardNetworks' => array( 'VISA', 'MASTERCARD' ),
		'allowedPaymentTypes' => array( 'PANENTRY', 'CLICKTOPAY', 'APPLEPAY', 'GOOGLEPAY' ),
		'completeMandate'  => array(
			'type' => 'CAPTURE',
		),
		'country'          => 'US',
		'locale'           => 'en_US',
		'captureMandate'   => array(
			'billingType'               => 'FULL',
			'requestEmail'              => true,
			'requestPhone'              => false,
			'requestShipping'           => false,
			'showAcceptedNetworkIcons'  => true,
		),
		'data' => array(
			'orderInformation' => array(
				'amountDetails' => array(
					'totalAmount' => $total_amount,
					'currency'    => $currency,
				),
			'billTo' => $bill_to,
			),
			'clientReferenceInformation' => array(
				'code' => 'donation-' . substr( uniqid( '', true ), -8 ),
			),
		),
	);

	$body = wp_json_encode( $payload );
	$has_building = ( strpos( $body, 'buildingNumber' ) !== false );
	error_log( '[CYBERSOURCE_CAPTURE_CTX] OUTGOING BODY CONTAINS buildingNumber: ' . ( $has_building ? 'YES - THIS IS THE BUG' : 'NO - clean' ) );
	error_log( '[CYBERSOURCE_CAPTURE_CTX] FULL OUTGOING BODY: ' . $body );
	error_log( '[CYBERSOURCE_CAPTURE_CTX] Request URL: ' . $base_url . $resource );
	$headers = boniface_cybersource_signature_headers( 'POST', $resource, $body );

	$response = wp_remote_post( $base_url . $resource, array(
		'timeout' => 15,
		'headers' => $headers,
		'body'    => $body,
	) );

	$code = wp_remote_retrieve_response_code( $response );
	$body_response = wp_remote_retrieve_body( $response );
	error_log( '[CYBERSOURCE_CAPTURE_CTX] Response code: ' . $code );
	error_log( '[CYBERSOURCE_CAPTURE_CTX] Response body: ' . substr( $body_response, 0, 500 ) );

	if ( is_wp_error( $response ) ) {
		return array( 'success' => false, 'error' => $response->get_error_message() );
	}

	if ( $code !== 200 && $code !== 201 ) {
		return array(
			'success' => false,
			'error'   => 'Capture context request failed (' . $code . '): ' . substr( $body_response, 0, 200 ),
		);
	}

	// Response is the raw JWT string (or JSON with token and/or data).
	$capture_context_jwt = trim( $body_response );
	$client_library = '';
	$client_library_integrity = '';

	if ( strpos( $capture_context_jwt, '{' ) === 0 ) {
		$json = json_decode( $body_response, true );
		if ( is_array( $json ) ) {
			$capture_context_jwt = '';
			foreach ( array( 'captureContext', 'capture_context', 'token', 'jwt' ) as $key ) {
				if ( ! empty( $json[ $key ] ) && is_string( $json[ $key ] ) ) {
					$capture_context_jwt = trim( $json[ $key ] );
					break;
				}
			}
			if ( empty( $capture_context_jwt ) && ! empty( $json['data']['captureContext'] ) && is_string( $json['data']['captureContext'] ) ) {
				$capture_context_jwt = trim( $json['data']['captureContext'] );
			}
			// Some responses include clientLibrary in the JSON body.
			if ( ! empty( $json['data']['clientLibrary'] ) ) {
				$client_library = $json['data']['clientLibrary'];
			}
			if ( ! empty( $json['data']['clientLibraryIntegrity'] ) ) {
				$client_library_integrity = $json['data']['clientLibraryIntegrity'];
			}
		}
	}

	// Ensure we have a valid-looking JWT (header.payload.signature).
	if ( empty( $capture_context_jwt ) || count( explode( '.', $capture_context_jwt ) ) !== 3 ) {
		return array( 'success' => false, 'error' => 'Empty or invalid capture context response.' );
	}

	// Decode JWT payload (middle part) to get clientLibrary and clientLibraryIntegrity.
	$parts = explode( '.', $capture_context_jwt );
	if ( count( $parts ) >= 2 ) {
		$payload_b64 = $parts[1];
		$payload_b64 = strtr( $payload_b64, '-_', '+/' );
		$payload_json = base64_decode( $payload_b64, true );
		if ( $payload_json ) {
			$payload_decoded = json_decode( $payload_json, true );
			if ( ! is_array( $payload_decoded ) ) {
				$payload_decoded = array();
			}
			// Try multiple possible paths used by CyberSource in the JWT payload.
			$paths = array(
				array( 'data', 'clientLibrary' ),
				array( 'ctx', 0, 'data', 'clientLibrary' ),
				array( 'flx', 'data', 'clientLibrary' ),
			);
			foreach ( $paths as $path ) {
				$val = $payload_decoded;
				foreach ( $path as $key ) {
					$val = isset( $val[ $key ] ) ? $val[ $key ] : null;
					if ( $val === null ) {
						break;
					}
				}
				if ( ! empty( $val ) && is_string( $val ) ) {
					$client_library = $val;
					break;
				}
			}
			$paths_integrity = array(
				array( 'data', 'clientLibraryIntegrity' ),
				array( 'ctx', 0, 'data', 'clientLibraryIntegrity' ),
				array( 'flx', 'data', 'clientLibraryIntegrity' ),
			);
			foreach ( $paths_integrity as $path ) {
				$val = $payload_decoded;
				foreach ( $path as $key ) {
					$val = isset( $val[ $key ] ) ? $val[ $key ] : null;
					if ( $val === null ) {
						break;
					}
				}
				if ( ! empty( $val ) && is_string( $val ) ) {
					$client_library_integrity = $val;
					break;
				}
			}
		}
	}

	return array(
		'success'                 => true,
		'capture_context'         => $capture_context_jwt,
		'client_library'          => $client_library,
		'client_library_integrity' => $client_library_integrity,
	);
}

/**
 * Process a payment using a Unified Checkout transient token (authorize + capture / sale).
 *
 * @param string $transient_token_jwt Token from Unified Checkout.
 * @param float  $amount              Total amount.
 * @param string $currency            Currency code.
 * @param array  $bill_to             firstName, lastName, email at minimum.
 * @return array{success: bool, id?: string, error?: string, reason_code?: string}
 */
function boniface_cybersource_process_payment( $transient_token_jwt, $amount, $currency = 'USD', $bill_to = array() ) {
	$log_prefix = '[CYBERSOURCE_PAYMENT_API] ';
	$api_start = microtime( true );
	
	error_log( $log_prefix . '=== START PROCESS PAYMENT FUNCTION ===' );
		error_log( $log_prefix . 'Amount: ' . $amount . ' ' . $currency );
	error_log( $log_prefix . 'Token length: ' . strlen( $transient_token_jwt ) );
	error_log( $log_prefix . 'Bill_to keys: ' . implode( ', ', array_keys( $bill_to ) ) );
	error_log( $log_prefix . 'Bill_to values (sanitized): ' . json_encode( array_map( function( $v ) {
		if ( is_string( $v ) && strlen( $v ) > 20 ) {
			return substr( $v, 0, 10 ) . '...[REDACTED]';
		}
		return $v;
	}, $bill_to ) ) );
	
	error_log( $log_prefix . 'Step A: Getting config...' );
	$config = boniface_cybersource_config();
	if ( ! $config ) {
		error_log( $log_prefix . 'ERROR: CyberSource not configured' );
		return array( 'success' => false, 'error' => 'CyberSource is not configured.' );
	}
	error_log( $log_prefix . 'Step A: Config loaded, merchant_id: ' . substr( $config['merchant_id'], 0, 8 ) . '...' );

	$total_amount = number_format( (float) $amount, 2, '.', '' );
	$resource = '/pts/v2/payments';
	$base_url = boniface_cybersource_base_url();
	error_log( $log_prefix . 'Step B: Base URL: ' . $base_url . $resource . ' amount: ' . $total_amount );

	error_log( $log_prefix . 'Step C: Building payload with bill_to: ' . wp_json_encode( array_keys( $bill_to ) ) );

	$payload = array(
		'clientReferenceInformation' => array(
			'code' => 'donation-' . substr( uniqid( '', true ), -8 ),
		),
		'processingInformation' => array(
			'commerceIndicator' => 'internet',
			'capture'           => true,
		),
		'tokenInformation' => array(
			'transientTokenJwt' => $transient_token_jwt,
		),
		'orderInformation' => array(
			'amountDetails' => array(
				'totalAmount' => $total_amount,
				'currency'    => $currency,
			),
			'billTo' => $bill_to,
		),
	);

	error_log( $log_prefix . 'Step C: Analyzing transient token...' );
	$token_parts = explode( '.', $transient_token_jwt );
	error_log( $log_prefix . 'Step C: Token parts count: ' . count( $token_parts ) );
	if ( count( $token_parts ) >= 2 ) {
		$token_header_b64 = $token_parts[0];
		$token_payload_b64 = $token_parts[1];
		$token_header_b64_decoded = strtr( $token_header_b64, '-_', '+/' );
		$token_payload_b64_decoded = strtr( $token_payload_b64, '-_', '+/' );
		$token_header_json = base64_decode( $token_header_b64_decoded, true );
		$token_payload_json = base64_decode( $token_payload_b64_decoded, true );
		if ( $token_header_json ) {
			$token_header = json_decode( $token_header_json, true );
			error_log( $log_prefix . 'Step C: Token header: ' . json_encode( $token_header ) );
		}
		if ( $token_payload_json ) {
			$token_payload = json_decode( $token_payload_json, true );
			if ( is_array( $token_payload ) ) {
				$token_iss = isset( $token_payload['iss'] ) ? $token_payload['iss'] : 'N/A';
				$token_exp = isset( $token_payload['exp'] ) ? $token_payload['exp'] : null;
				$token_iat = isset( $token_payload['iat'] ) ? $token_payload['iat'] : null;
				$token_type = isset( $token_payload['type'] ) ? $token_payload['type'] : 'N/A';
				error_log( $log_prefix . 'Step C: Token issuer: ' . $token_iss );
				error_log( $log_prefix . 'Step C: Token type: ' . $token_type );
				if ( $token_exp ) {
					$now = time();
					$token_age = $now - ( $token_iat ?: $now );
					$token_ttl = $token_exp - $now;
					error_log( $log_prefix . 'Step C: Token issued at: ' . date( 'Y-m-d H:i:s', $token_iat ?: $now ) );
					error_log( $log_prefix . 'Step C: Token expires at: ' . date( 'Y-m-d H:i:s', $token_exp ) );
					error_log( $log_prefix . 'Step C: Token age: ' . round( $token_age / 60, 2 ) . ' minutes' );
					error_log( $log_prefix . 'Step C: Token TTL: ' . round( $token_ttl / 60, 2 ) . ' minutes' );
					if ( $token_ttl < 0 ) {
						error_log( $log_prefix . 'WARNING: Token is EXPIRED!' );
					} elseif ( $token_ttl < 60 ) {
						error_log( $log_prefix . 'WARNING: Token expires in less than 1 minute!' );
					}
				}
			}
		}
		error_log( $log_prefix . 'Step C: Token preview: ' . substr( $transient_token_jwt, 0, 50 ) . '...' . substr( $transient_token_jwt, -50 ) );
	} else {
		error_log( $log_prefix . 'WARNING: Token does not appear to be a valid JWT (not 3 parts)' );
	}

	error_log( $log_prefix . 'Step C: Payload built, encoding JSON...' );
	
	// Validate payload structure before encoding
	$payload_errors = array();
	if ( empty( $payload['tokenInformation']['transientTokenJwt'] ) ) {
		$payload_errors[] = 'Missing transientTokenJwt';
	}
	if ( empty( $payload['orderInformation']['amountDetails']['totalAmount'] ) ) {
		$payload_errors[] = 'Missing totalAmount';
	}
	if ( empty( $payload['orderInformation']['amountDetails']['currency'] ) ) {
		$payload_errors[] = 'Missing currency';
	}
	if ( ! empty( $payload_errors ) ) {
		error_log( $log_prefix . 'ERROR: Payload validation failed: ' . implode( ', ', $payload_errors ) );
	}
	
	$body = wp_json_encode( $payload );
	$body_size = strlen( $body );
	error_log( $log_prefix . 'Step C: Body size: ' . $body_size . ' bytes' );
	
	if ( json_last_error() !== JSON_ERROR_NONE ) {
		error_log( $log_prefix . 'ERROR: JSON encode failed: ' . json_last_error_msg() );
	}
	
	// Log full payload (sanitized - remove sensitive token data)
	$payload_log = $payload;
	if ( isset( $payload_log['tokenInformation']['transientTokenJwt'] ) ) {
		$token_preview = substr( $payload_log['tokenInformation']['transientTokenJwt'], 0, 30 ) . '...[REDACTED]...' . substr( $payload_log['tokenInformation']['transientTokenJwt'], -30 );
		$payload_log['tokenInformation']['transientTokenJwt'] = $token_preview;
	}
	error_log( $log_prefix . 'Step C: Full payload (sanitized): ' . json_encode( $payload_log, JSON_PRETTY_PRINT ) );
	
	// Log actual request body structure (first 500 chars)
	error_log( $log_prefix . 'Step C: Request body preview (first 500 chars): ' . substr( $body, 0, 500 ) );
	
	error_log( $log_prefix . 'Step D: Generating signature headers...' );
	$headers = boniface_cybersource_signature_headers( 'POST', $resource, $body );
	error_log( $log_prefix . 'Step D: Headers generated, count: ' . count( $headers ) );
	
	// Log headers (sanitized - no shared secret)
	$headers_log = $headers;
	if ( isset( $headers_log['Signature'] ) ) {
		$sig_preview = substr( $headers_log['Signature'], 0, 50 ) . '...[REDACTED]';
		$headers_log['Signature'] = $sig_preview;
	}
	error_log( $log_prefix . 'Step D: Request headers (sanitized): ' . json_encode( $headers_log, JSON_PRETTY_PRINT ) );
	if ( isset( $headers['v-c-merchant-id'] ) ) {
		error_log( $log_prefix . 'Step D: Merchant ID in headers: ' . $headers['v-c-merchant-id'] );
	}
	if ( isset( $headers['v-c-date'] ) ) {
		error_log( $log_prefix . 'Step D: v-c-date: ' . $headers['v-c-date'] );
	}

	$request_url = $base_url . $resource;
	error_log( $log_prefix . 'Step E: OUTGOING REQUEST BODY: ' . $body );
	error_log( $log_prefix . 'Step E: Making wp_remote_post request...' );
	error_log( $log_prefix . 'Step E: URL: ' . $request_url );
	error_log( $log_prefix . 'Step E: Timeout: 60 seconds' );
	error_log( $log_prefix . 'Step E: Memory before request: ' . round( memory_get_usage( true ) / 1024 / 1024, 2 ) . ' MB' );
	$request_start = microtime( true );
	
	$response = wp_remote_post( $request_url, array(
		'timeout' => 60,
		'headers' => $headers,
		'body'    => $body,
	) );
	
	$request_duration = microtime( true ) - $request_start;
	error_log( $log_prefix . 'Step E: wp_remote_post completed' );
	error_log( $log_prefix . 'Step E: Request duration: ' . round( $request_duration * 1000, 2 ) . ' ms' );
	error_log( $log_prefix . 'Step E: Memory after request: ' . round( memory_get_usage( true ) / 1024 / 1024, 2 ) . ' MB' );

	error_log( $log_prefix . 'Step F: Processing response...' );
	$code = wp_remote_retrieve_response_code( $response );
	$body_response = wp_remote_retrieve_body( $response );
	$response_size = strlen( $body_response );
	
	error_log( $log_prefix . 'Step F: Response code: ' . ( $code ? $code : 'NULL' ) );
	error_log( $log_prefix . 'Step F: Response body size: ' . $response_size . ' bytes' );
	
	// Log response headers for debugging
	$response_headers = wp_remote_retrieve_headers( $response );
	if ( $response_headers ) {
		$response_headers_array = $response_headers->getAll();
		error_log( $log_prefix . 'Step F: Response headers: ' . json_encode( $response_headers_array ) );
		if ( isset( $response_headers_array['x-request-id'] ) ) {
			error_log( $log_prefix . 'Step F: CyberSource Request ID: ' . $response_headers_array['x-request-id'] );
		}
		if ( isset( $response_headers_array['v-c-correlation-id'] ) ) {
			error_log( $log_prefix . 'Step F: CyberSource Correlation ID: ' . $response_headers_array['v-c-correlation-id'] );
		}
	}
	
	if ( is_wp_error( $response ) ) {
		$msg = $response->get_error_message();
		$code_err = $response->get_error_code();
		error_log( $log_prefix . 'ERROR: WP_Error detected' );
		error_log( $log_prefix . 'ERROR: Error code: ' . $code_err );
		error_log( $log_prefix . 'ERROR: Error message: ' . $msg );
		error_log( $log_prefix . 'ERROR: Request duration before error: ' . round( $request_duration * 1000, 2 ) . ' ms' );
		return array(
			'success'    => false,
			'error'      => $msg,
			'reason_code' => $code_err ?: 'WP_ERROR',
		);
	}
	
	error_log( $log_prefix . 'Step F: Response is valid (not WP_Error)' );
	if ( $response_size > 0 ) {
		$body_preview = substr( $body_response, 0, 200 );
		error_log( $log_prefix . 'Step F: Response body preview: ' . $body_preview );
	} else {
		error_log( $log_prefix . 'WARNING: Response body is empty' );
	}

	error_log( $log_prefix . 'Step G: Decoding JSON response...' );
	$data = json_decode( $body_response, true );
	if ( json_last_error() !== JSON_ERROR_NONE ) {
		error_log( $log_prefix . 'ERROR: JSON decode failed: ' . json_last_error_msg() );
		error_log( $log_prefix . 'ERROR: Response body: ' . substr( $body_response, 0, 500 ) );
	}
	
	// Check for success: HTTP 2xx AND status field is not an error status
	$status = isset( $data['status'] ) ? $data['status'] : '';
	$is_success = ( $code >= 200 && $code < 300 ) && ( empty( $status ) || $status === 'AUTHORIZED' || $status === 'CAPTURED' || $status === 'PENDING' );
	
	if ( $is_success ) {
		$id = isset( $data['id'] ) ? $data['id'] : '';
		$api_duration = microtime( true ) - $api_start;
		error_log( $log_prefix . 'SUCCESS: Payment processed' );
		error_log( $log_prefix . 'SUCCESS: Payment ID: ' . $id );
		error_log( $log_prefix . 'SUCCESS: Status: ' . $status );
		error_log( $log_prefix . 'SUCCESS: Total function time: ' . round( $api_duration * 1000, 2 ) . ' ms' );
		error_log( $log_prefix . '=== END PROCESS PAYMENT FUNCTION (SUCCESS) ===' );
		return array( 'success' => true, 'id' => $id );
	}

	// Extract error message from multiple possible response structures
	$reason_code = '';
	$message = '';
	
	// Structure 1: errorInformation.reasonCode / errorInformation.message (standard)
	if ( isset( $data['errorInformation']['reasonCode'] ) ) {
		$reason_code = $data['errorInformation']['reasonCode'];
	}
	if ( isset( $data['errorInformation']['message'] ) ) {
		$message = $data['errorInformation']['message'];
	}
	
	// Structure 2: Direct reason / message fields (e.g. SYSTEM_ERROR responses)
	if ( empty( $reason_code ) && isset( $data['reason'] ) ) {
		$reason_code = $data['reason'];
	}
	if ( empty( $message ) && isset( $data['message'] ) ) {
		$message = $data['message'];
	}
	
	// Structure 3: status field indicates error
	if ( ! empty( $status ) && ( $status === 'SERVER_ERROR' || $status === 'DECLINED' || $status === 'INVALID_REQUEST' ) ) {
		if ( empty( $message ) ) {
			$message = 'Payment ' . strtolower( str_replace( '_', ' ', $status ) );
		}
	}

	$api_duration = microtime( true ) - $api_start;
	error_log( $log_prefix . 'ERROR: Payment failed' );
	error_log( $log_prefix . 'ERROR: HTTP code: ' . $code );
	error_log( $log_prefix . 'ERROR: Status: ' . ( $status ? $status : 'N/A' ) );
	error_log( $log_prefix . 'ERROR: Reason code: ' . ( $reason_code ? $reason_code : 'N/A' ) );
	error_log( $log_prefix . 'ERROR: Message: ' . ( $message ? $message : 'N/A' ) );
	if ( isset( $data['errorInformation']['details'] ) ) {
		error_log( $log_prefix . 'ERROR: Details: ' . json_encode( $data['errorInformation']['details'] ) );
	}
	if ( isset( $data['details'] ) ) {
		error_log( $log_prefix . 'ERROR: Details (direct): ' . json_encode( $data['details'] ) );
	}
	error_log( $log_prefix . 'ERROR: Full response: ' . json_encode( $data ) );
	
	// Additional debugging for SYSTEM_ERROR
	if ( $reason_code === 'SYSTEM_ERROR' || $status === 'SERVER_ERROR' ) {
		error_log( $log_prefix . 'DEBUG: SYSTEM_ERROR detected - checking possible causes...' );
		error_log( $log_prefix . 'DEBUG: HTTP status code: ' . $code );
		error_log( $log_prefix . 'DEBUG: Response has id field: ' . ( isset( $data['id'] ) ? 'yes (' . $data['id'] . ')' : 'no' ) );
		error_log( $log_prefix . 'DEBUG: Response has submitTimeUtc: ' . ( isset( $data['submitTimeUtc'] ) ? $data['submitTimeUtc'] : 'no' ) );
		if ( isset( $data['id'] ) ) {
			error_log( $log_prefix . 'DEBUG: CyberSource accepted the request (has ID) but returned SYSTEM_ERROR - likely account/config issue' );
		} else {
			error_log( $log_prefix . 'DEBUG: CyberSource rejected request before processing (no ID) - likely request format issue' );
		}
	}
	
	error_log( $log_prefix . 'ERROR: Total function time: ' . round( $api_duration * 1000, 2 ) . ' ms' );
	error_log( $log_prefix . '=== END PROCESS PAYMENT FUNCTION (FAILED) ===' );
	
	// Build user-friendly error message
	$error_msg = $message;
	if ( empty( $error_msg ) ) {
		$error_msg = 'Payment could not be processed';
		if ( $reason_code ) {
			$error_msg .= ' (' . $reason_code . ')';
		} elseif ( $code ) {
			$error_msg .= ' (HTTP ' . $code . ')';
		}
	}
	
	return array(
		'success'     => false,
		'error'       => $error_msg,
		'reason_code' => $reason_code ?: ( $status ?: (string) $code ),
	);
}

/**
 * AJAX: Get capture context for Unified Checkout.
 */
function boniface_cybersource_ajax_capture_context() {
	$_cs_version = 'v2-2026-02-21-no-buildingNumber';
	error_log( '[CYBERSOURCE_CAPTURE_CTX] === CAPTURE CONTEXT START ===' );
	error_log( '[CYBERSOURCE_CAPTURE_CTX] CODE VERSION: ' . $_cs_version );
	error_log( '[CYBERSOURCE_CAPTURE_CTX] FILE: ' . __FILE__ );
	error_log( '[CYBERSOURCE_CAPTURE_CTX] FILE MODIFIED: ' . date( 'Y-m-d H:i:s', filemtime( __FILE__ ) ) );
	error_log( '[CYBERSOURCE_CAPTURE_CTX] FILE SIZE: ' . filesize( __FILE__ ) . ' bytes' );
	error_log( '[CYBERSOURCE_CAPTURE_CTX] FILE MD5: ' . md5_file( __FILE__ ) );

	check_ajax_referer( 'boniface_cybersource', 'nonce' );

	$amount   = isset( $_POST['amount'] ) ? floatval( $_POST['amount'] ) : 0;
	$currency = isset( $_POST['currency'] ) ? sanitize_text_field( wp_unslash( $_POST['currency'] ) ) : 'USD';
	$origin   = isset( $_POST['origin'] ) ? esc_url_raw( wp_unslash( $_POST['origin'] ) ) : '';
	$name     = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$phone    = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';

	$bill_to = array(
		'firstName'          => 'Donor',
		'lastName'           => 'Donor',
		'email'              => $email ?: 'donor@example.com',
		'address1'           => '1 Market St',
		'locality'           => 'San Francisco',
		'administrativeArea' => 'CA',
		'postalCode'         => '94105',
		'country'            => 'US',
	);
	if ( $name ) {
		$parts = preg_split( '/\s+/', trim( $name ), 2 );
		$bill_to['firstName'] = $parts[0];
		$bill_to['lastName']  = isset( $parts[1] ) ? $parts[1] : $parts[0];
	}

	error_log( '[CYBERSOURCE_CAPTURE_CTX] bill_to keys: ' . wp_json_encode( array_keys( $bill_to ) ) );
	error_log( '[CYBERSOURCE_CAPTURE_CTX] bill_to values: ' . wp_json_encode( $bill_to ) );
	error_log( '[CYBERSOURCE_CAPTURE_CTX] has buildingNumber key: ' . ( array_key_exists( 'buildingNumber', $bill_to ) ? 'YES - BUG!' : 'NO - correct' ) );

	$result = boniface_cybersource_get_capture_context( $amount, $currency, $origin, $bill_to );

	error_log( '[CYBERSOURCE_CAPTURE_CTX] Result success: ' . ( $result['success'] ? 'true' : 'false' ) );
	if ( ! $result['success'] ) {
		error_log( '[CYBERSOURCE_CAPTURE_CTX] Result error: ' . ( isset( $result['error'] ) ? $result['error'] : 'N/A' ) );
	}
	error_log( '[CYBERSOURCE_CAPTURE_CTX] === CAPTURE CONTEXT END ===' );

	wp_send_json( $result );
}

/**
 * AJAX: Process donation payment with transient token.
 */
function boniface_cybersource_ajax_process_payment() {
	$start_time = microtime( true );
	$start_memory = memory_get_usage( true );
	$log_prefix = '[CYBERSOURCE_PAYMENT] ';
	
	error_log( $log_prefix . '=== START AJAX PROCESS PAYMENT ===' );
	error_log( $log_prefix . 'Time: ' . date( 'Y-m-d H:i:s' ) );
	error_log( $log_prefix . 'Memory: ' . round( $start_memory / 1024 / 1024, 2 ) . ' MB' );
	error_log( $log_prefix . 'Max execution time: ' . ini_get( 'max_execution_time' ) );
	error_log( $log_prefix . 'Memory limit: ' . ini_get( 'memory_limit' ) );
	
	try {
		error_log( $log_prefix . 'Step 1: Checking nonce...' );
		check_ajax_referer( 'boniface_cybersource', 'nonce' );
		error_log( $log_prefix . 'Step 1: Nonce check passed' );

		// Give this request more time so proxy/server does not return 502 before CyberSource responds.
		$limit = (int) ini_get( 'max_execution_time' );
		error_log( $log_prefix . 'Current max_execution_time: ' . $limit );
		if ( $limit > 0 && $limit < 60 ) {
			@set_time_limit( 60 );
			error_log( $log_prefix . 'Set max_execution_time to 60' );
		}

		error_log( $log_prefix . 'Step 2: Parsing POST data...' );
		$token   = isset( $_POST['transient_token'] ) ? sanitize_text_field( wp_unslash( $_POST['transient_token'] ) ) : '';
		$amount  = isset( $_POST['amount'] ) ? floatval( $_POST['amount'] ) : 0;
		$currency = isset( $_POST['currency'] ) ? sanitize_text_field( wp_unslash( $_POST['currency'] ) ) : 'USD';
		$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		
		error_log( $log_prefix . 'Token length: ' . strlen( $token ) );
		error_log( $log_prefix . 'Token preview (first 50): ' . substr( $token, 0, 50 ) );
		error_log( $log_prefix . 'Token preview (last 50): ' . substr( $token, -50 ) );
		error_log( $log_prefix . 'Amount: ' . $amount );
		error_log( $log_prefix . 'Currency: ' . $currency );
		error_log( $log_prefix . 'Name: ' . substr( $name, 0, 30 ) );
		error_log( $log_prefix . 'Email: ' . substr( $email, 0, 30 ) );
		
		// Quick token validation
		if ( $token ) {
			$token_parts_check = explode( '.', $token );
			error_log( $log_prefix . 'Token JWT parts: ' . count( $token_parts_check ) );
			if ( count( $token_parts_check ) !== 3 ) {
				error_log( $log_prefix . 'WARNING: Token does not have 3 JWT parts!' );
			}
		}

		if ( ! $token || $amount < 10 ) {
			error_log( $log_prefix . 'ERROR: Invalid request - token: ' . ( $token ? 'present' : 'missing' ) . ', amount: ' . $amount );
			wp_send_json( array( 'success' => false, 'error' => 'Invalid request.' ) );
		}

		$phone = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';

		error_log( $log_prefix . 'Step 3: Building bill_to array with safe defaults...' );
		$bill_to = array(
			'firstName'          => 'Donor',
			'lastName'           => 'Donor',
			'email'              => $email ?: 'donor@example.com',
			'phoneNumber'        => $phone,
			'address1'           => '1 Market St',
			'locality'           => 'San Francisco',
			'administrativeArea' => 'CA',
			'postalCode'         => '94105',
			'country'            => 'US',
		);
		if ( $name ) {
			$parts = preg_split( '/\s+/', trim( $name ), 2 );
			$bill_to['firstName'] = $parts[0];
			$bill_to['lastName']  = isset( $parts[1] ) ? $parts[1] : $parts[0];
		}
		if ( empty( $phone ) ) {
			unset( $bill_to['phoneNumber'] );
		}
		error_log( $log_prefix . 'Bill_to prepared: ' . wp_json_encode( array_keys( $bill_to ) ) );

		$before_process = microtime( true );
		error_log( $log_prefix . 'Step 4: Calling boniface_cybersource_process_payment...' );
		error_log( $log_prefix . 'Elapsed time so far: ' . round( ( $before_process - $start_time ) * 1000, 2 ) . ' ms' );
		error_log( $log_prefix . 'Memory usage: ' . round( memory_get_usage( true ) / 1024 / 1024, 2 ) . ' MB' );
		
		$result = boniface_cybersource_process_payment( $token, $amount, $currency, $bill_to );
		
		$after_process = microtime( true );
		error_log( $log_prefix . 'Step 4: Returned from boniface_cybersource_process_payment' );
		error_log( $log_prefix . 'Process payment took: ' . round( ( $after_process - $before_process ) * 1000, 2 ) . ' ms' );
		error_log( $log_prefix . 'Result success: ' . ( $result['success'] ? 'true' : 'false' ) );
		if ( ! $result['success'] ) {
			error_log( $log_prefix . 'Result error: ' . ( isset( $result['error'] ) ? $result['error'] : 'N/A' ) );
			error_log( $log_prefix . 'Result reason_code: ' . ( isset( $result['reason_code'] ) ? $result['reason_code'] : 'N/A' ) );
		}

		if ( $result['success'] ) {
			error_log( $log_prefix . 'Step 5: Payment successful, firing action hook...' );
			// Optional: save donation record (e.g. custom post type or table).
			do_action( 'boniface_donation_payment_success', array(
				'amount'     => $amount,
				'currency'   => $currency,
				'name'       => $name,
				'email'      => $email,
				'phone'      => $phone,
				'message'    => isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '',
				'payment_id' => isset( $result['id'] ) ? $result['id'] : '',
			) );
			error_log( $log_prefix . 'Step 5: Action hook completed' );
		}

		$total_time = microtime( true ) - $start_time;
		$total_memory = memory_get_usage( true ) - $start_memory;
		error_log( $log_prefix . 'Step 6: Sending JSON response' );
		error_log( $log_prefix . 'Total elapsed time: ' . round( $total_time * 1000, 2 ) . ' ms' );
		error_log( $log_prefix . 'Total memory used: ' . round( $total_memory / 1024 / 1024, 2 ) . ' MB' );
		error_log( $log_prefix . 'Peak memory: ' . round( memory_get_peak_usage( true ) / 1024 / 1024, 2 ) . ' MB' );
		error_log( $log_prefix . '=== END AJAX PROCESS PAYMENT ===' );

		wp_send_json( $result );
		
	} catch ( Exception $e ) {
		$error_time = microtime( true ) - $start_time;
		error_log( $log_prefix . 'EXCEPTION CAUGHT: ' . $e->getMessage() );
		error_log( $log_prefix . 'Exception file: ' . $e->getFile() . ':' . $e->getLine() );
		error_log( $log_prefix . 'Exception trace: ' . $e->getTraceAsString() );
		error_log( $log_prefix . 'Time before exception: ' . round( $error_time * 1000, 2 ) . ' ms' );
		error_log( $log_prefix . 'Memory at exception: ' . round( memory_get_usage( true ) / 1024 / 1024, 2 ) . ' MB' );
		wp_send_json( array( 'success' => false, 'error' => 'Server error: ' . $e->getMessage() ) );
	} catch ( Error $e ) {
		$error_time = microtime( true ) - $start_time;
		error_log( $log_prefix . 'FATAL ERROR CAUGHT: ' . $e->getMessage() );
		error_log( $log_prefix . 'Error file: ' . $e->getFile() . ':' . $e->getLine() );
		error_log( $log_prefix . 'Error trace: ' . $e->getTraceAsString() );
		error_log( $log_prefix . 'Time before error: ' . round( $error_time * 1000, 2 ) . ' ms' );
		error_log( $log_prefix . 'Memory at error: ' . round( memory_get_usage( true ) / 1024 / 1024, 2 ) . ' MB' );
		wp_send_json( array( 'success' => false, 'error' => 'Fatal error: ' . $e->getMessage() ) );
	}
}

/**
 * Register AJAX and enqueue CyberSource only on donate page.
 */
function boniface_cybersource_init() {
	add_action( 'wp_ajax_boniface_cybersource_capture_context', 'boniface_cybersource_ajax_capture_context' );
	add_action( 'wp_ajax_nopriv_boniface_cybersource_capture_context', 'boniface_cybersource_ajax_capture_context' );
	add_action( 'wp_ajax_boniface_cybersource_process_payment', 'boniface_cybersource_ajax_process_payment' );
	add_action( 'wp_ajax_nopriv_boniface_cybersource_process_payment', 'boniface_cybersource_ajax_process_payment' );
}
add_action( 'init', 'boniface_cybersource_init' );

/**
 * Enqueue Unified Checkout script and donation form handler on donate template.
 */
function boniface_cybersource_scripts() {
	if ( ! is_page_template( 'pages/donate-2.php' ) ) {
		return;
	}

	$script_path = get_template_directory() . '/assets/js/donation-uc.js';
	$script_uri  = get_template_directory_uri() . '/assets/js/donation-uc.js';
	$version     = file_exists( $script_path ) ? filemtime( $script_path ) : ( defined( '_S_VERSION' ) ? _S_VERSION : '1.0.0' );
	wp_enqueue_script(
		'boniface-donation-uc',
		$script_uri,
		array( 'jquery' ),
		$version,
		true
	);

	wp_localize_script( 'boniface-donation-uc', 'bonifaceCybersource', array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'boniface_cybersource' ),
		'origin'  => home_url( '', 'https' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'boniface_cybersource_scripts', 20 );
