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
 * Log merchant ID verification with full diagnostic output.
 * Always logs everything so we can diagnose 502 issues.
 *
 * @param array  $config  Config from boniface_cybersource_config().
 * @param array  $headers Headers being sent.
 * @param string $context Label e.g. 'CAPTURE_CTX' or 'PAYMENT'.
 */
function boniface_cybersource_log_merchant_verification( $config, $headers, $context ) {
	$expected_mid = $config ? $config['merchant_id'] : '(no config)';
	$expected_kid = $config ? $config['key_id'] : '(no config)';
	$header_mid   = isset( $headers['v-c-merchant-id'] ) ? $headers['v-c-merchant-id'] : 'MISSING';
	$sig_header   = isset( $headers['Signature'] ) ? $headers['Signature'] : 'MISSING';

	$mid_ok = ( $header_mid === $expected_mid );
	$kid_ok = ( strpos( $sig_header, 'keyid="' . $expected_kid . '"' ) !== false );

	error_log( '[CYBERSOURCE][' . $context . '] === MERCHANT VERIFICATION ===' );
	error_log( '[CYBERSOURCE][' . $context . '] merchant_id=' . $expected_mid . ' (header=' . $header_mid . ') ' . ( $mid_ok ? 'OK' : 'MISMATCH!' ) );
	error_log( '[CYBERSOURCE][' . $context . '] key_id=' . $expected_kid . ' ' . ( $kid_ok ? 'OK' : 'MISMATCH!' ) );
	error_log( '[CYBERSOURCE][' . $context . '] env=' . ( $config ? $config['env'] : 'NONE' ) );
	error_log( '[CYBERSOURCE][' . $context . '] secret_length=' . ( $config ? strlen( $config['shared_secret'] ) : 0 ) );
	error_log( '[CYBERSOURCE][' . $context . '] host_header=' . ( isset( $headers['Host'] ) ? $headers['Host'] : 'MISSING' ) );
	error_log( '[CYBERSOURCE][' . $context . '] v-c-date=' . ( isset( $headers['v-c-date'] ) ? $headers['v-c-date'] : 'MISSING' ) );
	error_log( '[CYBERSOURCE][' . $context . '] digest=' . ( isset( $headers['Digest'] ) ? substr( $headers['Digest'], 0, 40 ) . '...' : 'MISSING' ) );
	error_log( '[CYBERSOURCE][' . $context . '] signature=' . substr( $sig_header, 0, 80 ) . '...' );
	if ( ! $mid_ok || ! $kid_ok ) {
		error_log( '[CYBERSOURCE][' . $context . '] !!! CREDENTIAL MISMATCH DETECTED — this WILL cause failures !!!' );
	}
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
 * Normalize a region/province name to a short code suitable for CyberSource.
 * Handles US states, Canadian provinces, Australian states, and others.
 *
 * @param string $region  Full region name from ipinfo.io (e.g. "British Columbia").
 * @param string $country 2-letter country code.
 * @return string Short region code (e.g. "BC") or empty string.
 */
function boniface_cybersource_normalize_region( $region, $country ) {
	if ( empty( $region ) ) {
		return '';
	}

	$region_trimmed = trim( $region );

	// Already a short code (2-3 chars) — return as-is
	if ( strlen( $region_trimmed ) <= 3 ) {
		return strtoupper( $region_trimmed );
	}

	// US states
	if ( $country === 'US' ) {
		return boniface_cybersource_normalize_state( $region_trimmed );
	}

	// Canadian provinces
	$ca_map = array(
		'ALBERTA' => 'AB', 'BRITISH COLUMBIA' => 'BC', 'MANITOBA' => 'MB',
		'NEW BRUNSWICK' => 'NB', 'NEWFOUNDLAND AND LABRADOR' => 'NL',
		'NORTHWEST TERRITORIES' => 'NT', 'NOVA SCOTIA' => 'NS', 'NUNAVUT' => 'NU',
		'ONTARIO' => 'ON', 'PRINCE EDWARD ISLAND' => 'PE', 'QUEBEC' => 'QC',
		'SASKATCHEWAN' => 'SK', 'YUKON' => 'YT',
	);

	// Australian states
	$au_map = array(
		'NEW SOUTH WALES' => 'NSW', 'VICTORIA' => 'VIC', 'QUEENSLAND' => 'QLD',
		'SOUTH AUSTRALIA' => 'SA', 'WESTERN AUSTRALIA' => 'WA', 'TASMANIA' => 'TAS',
		'NORTHERN TERRITORY' => 'NT', 'AUSTRALIAN CAPITAL TERRITORY' => 'ACT',
	);

	// Indian states (common ones)
	$in_map = array(
		'MAHARASHTRA' => 'MH', 'KARNATAKA' => 'KA', 'TAMIL NADU' => 'TN',
		'DELHI' => 'DL', 'UTTAR PRADESH' => 'UP', 'WEST BENGAL' => 'WB',
		'TELANGANA' => 'TG', 'RAJASTHAN' => 'RJ', 'GUJARAT' => 'GJ',
		'KERALA' => 'KL', 'ANDHRA PRADESH' => 'AP', 'MADHYA PRADESH' => 'MP',
		'PUNJAB' => 'PB', 'HARYANA' => 'HR', 'BIHAR' => 'BH',
	);

	$upper = strtoupper( $region_trimmed );
	$maps = array( 'CA' => $ca_map, 'AU' => $au_map, 'IN' => $in_map );

	if ( isset( $maps[ $country ][ $upper ] ) ) {
		return $maps[ $country ][ $upper ];
	}

	// GB regions — England, Scotland, Wales, Northern Ireland
	if ( $country === 'GB' ) {
		$gb_map = array(
			'ENGLAND' => 'ENG', 'SCOTLAND' => 'SCT', 'WALES' => 'WLS',
			'NORTHERN IRELAND' => 'NIR',
		);
		if ( isset( $gb_map[ $upper ] ) ) {
			return $gb_map[ $upper ];
		}
	}

	// Fallback: take first 2 chars uppercased
	return strtoupper( substr( $region_trimmed, 0, 2 ) );
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

	$client_ref = 'donation-' . substr( uniqid( '', true ), -8 );

	$order_info = array(
		'amountDetails' => array(
			'totalAmount' => $total_amount,
			'currency'    => $currency,
		),
	);
	if ( ! empty( $bill_to ) ) {
		$order_info['billTo'] = $bill_to;
	}

	$payload = array(
		'targetOrigins'       => array( $origin ),
		'clientVersion'       => '0.34',
		'allowedCardNetworks' => array( 'VISA', 'MASTERCARD' ),
		'allowedPaymentTypes' => array( 'PANENTRY', 'CLICKTOPAY', 'GOOGLEPAY' ),
		'country'             => 'KE',
		'locale'              => 'en_US',
		'captureMandate'      => array(
			'billingType'              => 'FULL',
			'requestEmail'             => true,
			'requestPhone'             => true,
			'requestShipping'          => false,
			'showAcceptedNetworkIcons' => true,
		),
		'completeMandate' => array(
			'type'                     => 'CAPTURE',
			'decisionManager'          => true,
			'consumerAuthentication'    => true,
		),
		'orderInformation' => $order_info,
	);

	$body    = wp_json_encode( $payload );
	$headers = boniface_cybersource_signature_headers( 'POST', $resource, $body );

	error_log( '[CYBERSOURCE][CAPTURE_CTX] === CAPTURE CONTEXT REQUEST (completeMandate) ===' );
	error_log( '[CYBERSOURCE][CAPTURE_CTX] origin=' . $origin . ' amount=' . $total_amount . ' currency=' . $currency . ' ref=' . $client_ref );
	error_log( '[CYBERSOURCE][CAPTURE_CTX] MODE: completeMandate type=CAPTURE (CyberSource processes payment in widget)' );
	error_log( '[CYBERSOURCE][CAPTURE_CTX] payload_size=' . strlen( $body ) . ' bytes' );
	boniface_cybersource_log_merchant_verification( $config, $headers, 'CAPTURE_CTX' );

	error_log( '[CYBERSOURCE][CAPTURE_CTX] === POSTMAN: COPY EVERYTHING BELOW ===' );
	error_log( '[CYBERSOURCE][CAPTURE_CTX] POST ' . $base_url . $resource );
	error_log( '[CYBERSOURCE][CAPTURE_CTX] --- HEADERS ---' );
	error_log( '[CYBERSOURCE][CAPTURE_CTX] Content-Type: application/json' );
	error_log( '[CYBERSOURCE][CAPTURE_CTX] v-c-merchant-id: ' . $headers['v-c-merchant-id'] );
	error_log( '[CYBERSOURCE][CAPTURE_CTX] v-c-date: ' . $headers['v-c-date'] );
	error_log( '[CYBERSOURCE][CAPTURE_CTX] Host: ' . $headers['Host'] );
	error_log( '[CYBERSOURCE][CAPTURE_CTX] Digest: ' . ( isset( $headers['Digest'] ) ? $headers['Digest'] : '' ) );
	error_log( '[CYBERSOURCE][CAPTURE_CTX] Signature: ' . $headers['Signature'] );
	error_log( '[CYBERSOURCE][CAPTURE_CTX] --- FULL BODY (paste as raw JSON) ---' );
	error_log( '[CYBERSOURCE][CAPTURE_CTX] ' . $body );
	error_log( '[CYBERSOURCE][CAPTURE_CTX] --- END POSTMAN ---' );
	error_log( '[CYBERSOURCE][CAPTURE_CTX] NOTE: Signature/Digest headers are time-sensitive. For Postman, use CyberSource auth with key_id=' . $config['key_id'] );

	$response = wp_remote_post( $base_url . $resource, array(
		'timeout' => 15,
		'headers' => $headers,
		'body'    => $body,
	) );

	$code          = wp_remote_retrieve_response_code( $response );
	$body_response = wp_remote_retrieve_body( $response );

	if ( is_wp_error( $response ) ) {
		error_log( '[CYBERSOURCE][CAPTURE_CTX] WP_Error: ' . $response->get_error_message() );
		error_log( '[CYBERSOURCE][CAPTURE_CTX] WP transport: ' . get_class( $response ) );
		return array( 'success' => false, 'error' => $response->get_error_message() );
	}

	error_log( '[CYBERSOURCE][CAPTURE_CTX] HTTP ' . $code . ' response_size=' . strlen( $body_response ) . ' bytes' );

	if ( $code !== 200 && $code !== 201 ) {
		error_log( '[CYBERSOURCE][CAPTURE_CTX] FAILED body: ' . substr( $body_response, 0, 500 ) );
		error_log( '[CYBERSOURCE][CAPTURE_CTX] response_headers: ' . wp_json_encode( wp_remote_retrieve_headers( $response )->getAll() ) );
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

	error_log( '[CYBERSOURCE][CAPTURE_CTX] SUCCESS jwt_length=' . strlen( $capture_context_jwt ) . ' client_lib=' . ( $client_library ? 'YES' : 'MISSING' ) );
	error_log( '[CYBERSOURCE][CAPTURE_CTX] jwt_first80: ' . substr( $capture_context_jwt, 0, 80 ) );

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
	$config = boniface_cybersource_config();
	if ( ! $config ) {
		return array( 'success' => false, 'error' => 'CyberSource is not configured.' );
	}

	$total_amount = number_format( (float) $amount, 2, '.', '' );
	$resource     = '/pts/v2/payments';
	$base_url     = boniface_cybersource_base_url();
	$client_ref   = 'donation-' . substr( uniqid( '', true ), -8 );

	$token_parts = explode( '.', $transient_token_jwt );
	error_log( '[CYBERSOURCE][PAYMENT] === PRE-FLIGHT DIAGNOSTICS ===' );
	error_log( '[CYBERSOURCE][PAYMENT] PHP=' . PHP_VERSION . ' WP=' . get_bloginfo( 'version' ) . ' time=' . gmdate( 'c' ) );
	error_log( '[CYBERSOURCE][PAYMENT] token_parts=' . count( $token_parts ) . ' token_length=' . strlen( $transient_token_jwt ) );
	error_log( '[CYBERSOURCE][PAYMENT] amount=' . $total_amount . ' currency=' . $currency . ' ref=' . $client_ref );
	error_log( '[CYBERSOURCE][PAYMENT] billTo: ' . wp_json_encode( $bill_to ) );

	if ( count( $token_parts ) === 3 ) {
		$token_payload_b64 = strtr( $token_parts[1], '-_', '+/' );
		$token_payload_json = base64_decode( $token_payload_b64, true );
		if ( $token_payload_json ) {
			$token_payload = json_decode( $token_payload_json, true );
			if ( is_array( $token_payload ) ) {
				error_log( '[CYBERSOURCE][PAYMENT] token_iss=' . ( isset( $token_payload['iss'] ) ? $token_payload['iss'] : 'N/A' ) );
				error_log( '[CYBERSOURCE][PAYMENT] token_exp=' . ( isset( $token_payload['exp'] ) ? $token_payload['exp'] . ' (' . gmdate( 'c', $token_payload['exp'] ) . ')' : 'N/A' ) );
				error_log( '[CYBERSOURCE][PAYMENT] token_iat=' . ( isset( $token_payload['iat'] ) ? $token_payload['iat'] . ' (' . gmdate( 'c', $token_payload['iat'] ) . ')' : 'N/A' ) );
				$now = time();
				if ( isset( $token_payload['exp'] ) && $now > $token_payload['exp'] ) {
					error_log( '[CYBERSOURCE][PAYMENT] !!! TOKEN EXPIRED !!! now=' . $now . ' exp=' . $token_payload['exp'] . ' expired_ago=' . ( $now - $token_payload['exp'] ) . 's' );
				}
				error_log( '[CYBERSOURCE][PAYMENT] token_keys=' . implode( ',', array_keys( $token_payload ) ) );
			}
		}
	}

	$payload = array(
		'clientReferenceInformation' => array(
			'code' => $client_ref,
		),
		'processingInformation' => array(
			'capture' => true,
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

	$body    = wp_json_encode( $payload );
	$headers = boniface_cybersource_signature_headers( 'POST', $resource, $body );

	boniface_cybersource_log_merchant_verification( $config, $headers, 'PAYMENT' );

	error_log( '[CYBERSOURCE][PAYMENT] === POSTMAN: COPY EVERYTHING BELOW ===' );
	error_log( '[CYBERSOURCE][PAYMENT] POST ' . $base_url . $resource );
	error_log( '[CYBERSOURCE][PAYMENT] --- HEADERS ---' );
	error_log( '[CYBERSOURCE][PAYMENT] Content-Type: application/json' );
	error_log( '[CYBERSOURCE][PAYMENT] v-c-merchant-id: ' . $headers['v-c-merchant-id'] );
	error_log( '[CYBERSOURCE][PAYMENT] v-c-date: ' . $headers['v-c-date'] );
	error_log( '[CYBERSOURCE][PAYMENT] Host: ' . $headers['Host'] );
	error_log( '[CYBERSOURCE][PAYMENT] Digest: ' . ( isset( $headers['Digest'] ) ? $headers['Digest'] : '' ) );
	error_log( '[CYBERSOURCE][PAYMENT] Signature: ' . $headers['Signature'] );
	error_log( '[CYBERSOURCE][PAYMENT] --- FULL BODY (paste as raw JSON) ---' );
	error_log( '[CYBERSOURCE][PAYMENT] ' . $body );
	error_log( '[CYBERSOURCE][PAYMENT] --- TRANSIENT TOKEN (full, for API console) ---' );
	error_log( '[CYBERSOURCE][PAYMENT] ' . $transient_token_jwt );
	error_log( '[CYBERSOURCE][PAYMENT] --- END POSTMAN ---' );
	error_log( '[CYBERSOURCE][PAYMENT] NOTE: Signature/Digest headers are time-sensitive. For Postman, use CyberSource auth with your key_id=' . $config['key_id'] . ' and shared_secret (you have it in wp-config).' );

	$start_time = microtime( true );
	$response = wp_remote_post( $base_url . $resource, array(
		'timeout' => 60,
		'headers' => $headers,
		'body'    => $body,
	) );
	$elapsed = round( ( microtime( true ) - $start_time ) * 1000 );

	if ( is_wp_error( $response ) ) {
		error_log( '[CYBERSOURCE][PAYMENT] WP_Error after ' . $elapsed . 'ms: ' . $response->get_error_message() );
		error_log( '[CYBERSOURCE][PAYMENT] WP_Error codes: ' . implode( ', ', $response->get_error_codes() ) );
		return array(
			'success'     => false,
			'error'       => $response->get_error_message(),
			'reason_code' => 'WP_ERROR',
		);
	}

	$code          = wp_remote_retrieve_response_code( $response );
	$body_response = wp_remote_retrieve_body( $response );
	$data          = json_decode( $body_response, true );
	$resp_headers  = wp_remote_retrieve_headers( $response );

	error_log( '[CYBERSOURCE][PAYMENT] === RESPONSE ===' );
	error_log( '[CYBERSOURCE][PAYMENT] HTTP ' . $code . ' in ' . $elapsed . 'ms' );
	error_log( '[CYBERSOURCE][PAYMENT] response_size=' . strlen( $body_response ) . ' bytes' );
	$correlation_id = isset( $resp_headers['v-c-correlation-id'] ) ? $resp_headers['v-c-correlation-id'] : 'N/A';
	error_log( '[CYBERSOURCE][PAYMENT] v-c-correlation-id=' . $correlation_id );
	error_log( '[CYBERSOURCE][PAYMENT] Full response body: ' . $body_response );

	$status     = isset( $data['status'] ) ? $data['status'] : '';
	$is_success = ( $code >= 200 && $code < 300 )
		&& ( empty( $status ) || in_array( $status, array( 'AUTHORIZED', 'CAPTURED', 'PENDING' ), true ) );

	if ( $is_success ) {
		error_log( '[CYBERSOURCE][PAYMENT] SUCCESS status=' . $status . ' id=' . ( isset( $data['id'] ) ? $data['id'] : '' ) );
		return array( 'success' => true, 'id' => isset( $data['id'] ) ? $data['id'] : '' );
	}

	$reason_code = '';
	$message     = '';

	if ( isset( $data['errorInformation']['reasonCode'] ) ) {
		$reason_code = $data['errorInformation']['reasonCode'];
	}
	if ( isset( $data['errorInformation']['message'] ) ) {
		$message = $data['errorInformation']['message'];
	}
	if ( empty( $reason_code ) && isset( $data['reason'] ) ) {
		$reason_code = $data['reason'];
	}
	if ( empty( $message ) && isset( $data['message'] ) ) {
		$message = $data['message'];
	}
	if ( ! empty( $status ) && in_array( $status, array( 'SERVER_ERROR', 'DECLINED', 'INVALID_REQUEST' ), true ) && empty( $message ) ) {
		$message = 'Payment ' . strtolower( str_replace( '_', ' ', $status ) );
	}

	error_log( '[CYBERSOURCE][PAYMENT] FAILED status=' . $status . ' reason=' . $reason_code . ' msg=' . $message );
	if ( $code === 502 && $reason_code === 'SYSTEM_ERROR' ) {
		error_log( '[CYBERSOURCE][PAYMENT] !!! 502 SYSTEM_ERROR — THIS IS A CYBERSOURCE SERVER/CONFIG ISSUE !!!' );
		error_log( '[CYBERSOURCE][PAYMENT] Possible causes:' );
		error_log( '[CYBERSOURCE][PAYMENT]   1. No payment processor assigned to merchant ID' );
		error_log( '[CYBERSOURCE][PAYMENT]   2. Merchant account not activated for card-not-present' );
		error_log( '[CYBERSOURCE][PAYMENT]   3. Currency (USD) not enabled for merchant' );
		error_log( '[CYBERSOURCE][PAYMENT]   4. Country/region restriction on merchant account' );
		error_log( '[CYBERSOURCE][PAYMENT]   5. Test account not fully provisioned' );
		error_log( '[CYBERSOURCE][PAYMENT] Contact CyberSource support with v-c-correlation-id above.' );
	}

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
		'correlation_id' => $correlation_id !== 'N/A' ? $correlation_id : '',
	);
}

/**
 * Build a billTo array from user-submitted form fields + name/email.
 *
 * Per CyberSource docs, both Capture Context and Payments API accept phoneNumber.
 * Only the Capture Context requires buildingNumber.
 *
 * @param string $name            Full name from form.
 * @param string $email           Email from form.
 * @param bool   $capture_context True = include buildingNumber (Capture Context only).
 * @return array CyberSource-compatible billTo.
 */
function boniface_cybersource_build_bill_to( $name, $email, $capture_context = false ) {
	$country  = isset( $_POST['billing_country'] ) ? strtoupper( sanitize_text_field( wp_unslash( $_POST['billing_country'] ) ) ) : '';
	$address1 = isset( $_POST['billing_address'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_address'] ) ) : '';
	$city     = isset( $_POST['billing_city'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_city'] ) ) : '';
	$state    = isset( $_POST['billing_state'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_state'] ) ) : '';
	$postal   = isset( $_POST['billing_postal'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_postal'] ) ) : '';
	$phone    = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';

	if ( ! empty( $state ) ) {
		$state = boniface_cybersource_normalize_region( $state, $country ?: 'US' );
	}

	$first = '';
	$last  = '';
	if ( $name ) {
		$parts = preg_split( '/\s+/', trim( $name ), 2 );
		$first = $parts[0];
		$last  = isset( $parts[1] ) ? $parts[1] : $parts[0];
	}

	if ( empty( $state ) ) {
		$state = $city ?: 'NA';
	}

	$phone_digits = preg_replace( '/[^0-9]/', '', $phone );
	if ( strlen( $phone_digits ) < 7 ) {
		$phone_digits = '';
	}

	$missing = array();
	if ( empty( $first ) )    $missing[] = 'firstName';
	if ( empty( $email ) )    $missing[] = 'email';
	if ( empty( $address1 ) ) $missing[] = 'address1';
	if ( empty( $city ) )     $missing[] = 'locality';
	if ( empty( $postal ) )   $missing[] = 'postalCode';
	if ( empty( $country ) )  $missing[] = 'country';
	if ( ! empty( $missing ) ) {
		error_log( '[CYBERSOURCE][BILL_TO] WARNING missing fields: ' . implode( ', ', $missing ) . ' (context=' . ( $capture_context ? 'capture' : 'payment' ) . ')' );
	}

	$bill_to = array(
		'firstName'          => $first ?: 'Donor',
		'lastName'           => $last ?: 'Donor',
		'address1'           => $address1 ?: '1 Main Street',
		'locality'           => $city ?: 'Nairobi',
		'administrativeArea' => $state,
		'postalCode'         => $postal ?: '00100',
		'country'            => $country ?: 'KE',
		'email'              => $email ?: 'donor@example.com',
		'phoneNumber'        => $phone_digits ?: '0000000000',
	);

	if ( $capture_context ) {
		$bill_to['buildingNumber'] = '1';
	}

	error_log( '[CYBERSOURCE][BILL_TO] Built: ' . wp_json_encode( $bill_to ) . ' context=' . ( $capture_context ? 'capture' : 'payment' ) );
	return $bill_to;
}

/**
 * AJAX: Get capture context for Unified Checkout.
 */
function boniface_cybersource_ajax_capture_context() {
	check_ajax_referer( 'boniface_cybersource', 'nonce' );

	$amount   = isset( $_POST['amount'] ) ? floatval( $_POST['amount'] ) : 0;
	$currency = isset( $_POST['currency'] ) ? sanitize_text_field( wp_unslash( $_POST['currency'] ) ) : 'USD';
	$origin   = isset( $_POST['origin'] ) ? esc_url_raw( wp_unslash( $_POST['origin'] ) ) : '';
	$name     = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$phone    = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';

	error_log( '[CYBERSOURCE][AJAX] capture_context called: amount=' . $amount . ' currency=' . $currency . ' origin=' . $origin . ' name=' . $name . ' email=' . $email );

	$bill_to = array();
	if ( $name || $email ) {
		$first = '';
		$last  = '';
		if ( $name ) {
			$parts = preg_split( '/\s+/', trim( $name ), 2 );
			$first = $parts[0];
			$last  = isset( $parts[1] ) ? $parts[1] : $parts[0];
		}
		$phone_digits = preg_replace( '/[^0-9]/', '', $phone );

		$bill_to = array(
			'firstName'          => $first ?: 'Donor',
			'lastName'           => $last ?: 'Donor',
			'email'              => $email ?: 'donor@example.com',
			'phoneNumber'        => strlen( $phone_digits ) >= 7 ? $phone_digits : '0000000000',
			'address1'           => '1 Main Street',
			'locality'           => 'Nairobi',
			'administrativeArea' => 'Nairobi',
			'postalCode'         => '00100',
			'country'            => 'KE',
			'buildingNumber'     => '1',
		);
		error_log( '[CYBERSOURCE][AJAX] billTo for capture context: ' . wp_json_encode( $bill_to ) );
	}

	$result = boniface_cybersource_get_capture_context( $amount, $currency, $origin, $bill_to );
	if ( ! $result['success'] ) {
		error_log( '[CYBERSOURCE][AJAX] Capture context FAILED: ' . ( isset( $result['error'] ) ? $result['error'] : 'unknown' ) );
	} else {
		error_log( '[CYBERSOURCE][AJAX] Capture context OK (completeMandate mode), jwt_len=' . strlen( isset( $result['capture_context'] ) ? $result['capture_context'] : '' ) );
	}
	wp_send_json( $result );
	return;
}

/**
 * AJAX: Record payment result from completeMandate (widget-processed payment).
 */
function boniface_cybersource_ajax_record_payment() {
	check_ajax_referer( 'boniface_cybersource', 'nonce' );

	$name       = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email      = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$phone      = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$amount     = isset( $_POST['amount'] ) ? floatval( $_POST['amount'] ) : 0;
	$currency   = isset( $_POST['currency'] ) ? sanitize_text_field( wp_unslash( $_POST['currency'] ) ) : 'USD';
	$message    = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
	$payment_id = isset( $_POST['payment_id'] ) ? sanitize_text_field( wp_unslash( $_POST['payment_id'] ) ) : '';
	$status     = isset( $_POST['payment_status'] ) ? sanitize_text_field( wp_unslash( $_POST['payment_status'] ) ) : '';
	$raw_result = isset( $_POST['raw_result'] ) ? sanitize_textarea_field( wp_unslash( $_POST['raw_result'] ) ) : '';

	error_log( '[CYBERSOURCE][RECORD] === completeMandate PAYMENT RESULT ===' );
	error_log( '[CYBERSOURCE][RECORD] status=' . $status . ' payment_id=' . $payment_id );
	error_log( '[CYBERSOURCE][RECORD] amount=' . $amount . ' currency=' . $currency );
	error_log( '[CYBERSOURCE][RECORD] name=' . $name . ' email=' . $email . ' phone=' . $phone );
	error_log( '[CYBERSOURCE][RECORD] raw_result=' . $raw_result );

	do_action( 'boniface_donation_payment_success', array(
		'amount'      => $amount,
		'currency'    => $currency,
		'name'        => $name,
		'email'       => $email,
		'phone'       => $phone,
		'message'     => $message,
		'payment_id'  => $payment_id,
	) );

	wp_send_json( array( 'success' => true, 'recorded' => true ) );
	return;
}

/**
 * AJAX: Process donation payment with transient token.
 */
function boniface_cybersource_ajax_process_payment() {
	try {
		check_ajax_referer( 'boniface_cybersource', 'nonce' );

		$limit = (int) ini_get( 'max_execution_time' );
		if ( $limit > 0 && $limit < 60 ) {
			@set_time_limit( 60 );
		}

		$raw_token = isset( $_POST['transient_token'] ) ? wp_unslash( $_POST['transient_token'] ) : '';
		$token     = sanitize_text_field( $raw_token );
		$amount    = isset( $_POST['amount'] ) ? floatval( $_POST['amount'] ) : 0;
		$currency  = isset( $_POST['currency'] ) ? sanitize_text_field( wp_unslash( $_POST['currency'] ) ) : 'USD';
		$name      = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$email     = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$phone     = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';

		error_log( '[CYBERSOURCE][AJAX] process_payment called: amount=' . $amount . ' currency=' . $currency . ' token_len=' . strlen( $token ) . ' name=' . $name . ' email=' . $email );
		if ( $raw_token !== $token ) {
			error_log( '[CYBERSOURCE][AJAX] !!! TOKEN MODIFIED BY sanitize_text_field() !!! raw_len=' . strlen( $raw_token ) . ' sanitized_len=' . strlen( $token ) );
			error_log( '[CYBERSOURCE][AJAX] Using RAW token instead to preserve integrity.' );
			$token = $raw_token;
		}

		if ( ! $token ) {
			error_log( '[CYBERSOURCE][AJAX] REJECTED: missing transient token' );
			wp_send_json( array( 'success' => false, 'error' => 'Missing payment token. Please try again.' ) );
			return;
		}
		if ( $amount < 10 ) {
			error_log( '[CYBERSOURCE][AJAX] REJECTED: amount too low (' . $amount . ')' );
			wp_send_json( array( 'success' => false, 'error' => 'Minimum donation is $10.' ) );
			return;
		}

		$bill_to = boniface_cybersource_build_bill_to( $name, $email, false );

		$result = boniface_cybersource_process_payment( $token, $amount, $currency, $bill_to );

		if ( ! $result['success'] ) {
			error_log( '[CYBERSOURCE][AJAX] Payment FAILED: ' . ( isset( $result['error'] ) ? $result['error'] : 'unknown' ) . ' reason=' . ( isset( $result['reason_code'] ) ? $result['reason_code'] : '' ) . ' correlation=' . ( isset( $result['correlation_id'] ) ? $result['correlation_id'] : '' ) );
		} else {
			error_log( '[CYBERSOURCE][AJAX] Payment SUCCESS id=' . ( isset( $result['id'] ) ? $result['id'] : '' ) );
		}

		if ( $result['success'] ) {
			do_action( 'boniface_donation_payment_success', array(
				'amount'      => $amount,
				'currency'    => $currency,
				'name'        => $name,
				'email'       => $email,
				'phone'       => $phone,
				'message'     => isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '',
				'payment_id'  => isset( $result['id'] ) ? $result['id'] : '',
				'billing'     => array(
					'country'  => isset( $_POST['billing_country'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_country'] ) ) : '',
					'address'  => isset( $_POST['billing_address'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_address'] ) ) : '',
					'city'     => isset( $_POST['billing_city'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_city'] ) ) : '',
					'state'    => isset( $_POST['billing_state'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_state'] ) ) : '',
					'postal'   => isset( $_POST['billing_postal'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_postal'] ) ) : '',
				),
			) );
		}

		wp_send_json( $result );
		return;

	} catch ( Exception $e ) {
		error_log( '[CYBERSOURCE][AJAX] Payment exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() );
		wp_send_json( array( 'success' => false, 'error' => 'Server error: ' . $e->getMessage() ) );
		return;
	} catch ( Error $e ) {
		error_log( '[CYBERSOURCE][AJAX] Payment fatal: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() );
		wp_send_json( array( 'success' => false, 'error' => 'Fatal error: ' . $e->getMessage() ) );
		return;
	}
}

/* ── Donation progress tracker ──────────────────────────── */

/**
 * Record a successful donation in wp_options for the progress counter.
 * Listens on the boniface_donation_payment_success hook.
 *
 * @param array $data Payment data: amount, currency, name, email, phone, message, payment_id.
 */
function boniface_donation_track( $data ) {
	$amount = isset( $data['amount'] ) ? (float) $data['amount'] : 0;
	if ( $amount <= 0 ) {
		return;
	}

	$currency   = isset( $data['currency'] ) ? strtoupper( $data['currency'] ) : 'USD';
	$payment_id = isset( $data['payment_id'] ) ? $data['payment_id'] : '';

	// Prevent duplicate recording of the same payment_id.
	if ( $payment_id ) {
		$recorded_ids = get_option( 'boniface_donation_ids', array() );
		if ( in_array( $payment_id, $recorded_ids, true ) ) {
			error_log( '[DONATION_TRACKER] Duplicate payment_id=' . $payment_id . ' — skipping.' );
			return;
		}
		$recorded_ids[] = $payment_id;
		if ( count( $recorded_ids ) > 500 ) {
			$recorded_ids = array_slice( $recorded_ids, -500 );
		}
		update_option( 'boniface_donation_ids', $recorded_ids, false );
	}

	// Convert to USD for the running total if needed.
	$usd_amount = $amount;
	if ( $currency === 'KES' ) {
		$rate       = (float) get_option( 'boniface_donation_kes_to_usd', 0.0077 );
		$usd_amount = $amount * $rate;
	}

	$total = (float) get_option( 'boniface_donation_total_usd', 0 );
	$count = (int) get_option( 'boniface_donation_count', 0 );

	$total += $usd_amount;
	$count += 1;

	update_option( 'boniface_donation_total_usd', $total, false );
	update_option( 'boniface_donation_count', $count, false );

	error_log( '[DONATION_TRACKER] Recorded: +$' . number_format( $usd_amount, 2 ) . ' (was ' . $currency . ' ' . $amount . ') — new total=$' . number_format( $total, 2 ) . ' count=' . $count );
}
add_action( 'boniface_donation_payment_success', 'boniface_donation_track' );

/**
 * Get donation progress stats.
 *
 * @return array{total: float, count: int, total_formatted: string}
 */
function boniface_donation_stats() {
	$total = (float) get_option( 'boniface_donation_total_usd', 0 );
	$count = (int) get_option( 'boniface_donation_count', 0 );

	return array(
		'total'           => $total,
		'count'           => $count,
		'total_formatted' => '$' . number_format( $total, 0, '.', ',' ),
	);
}

/**
 * Register AJAX and enqueue CyberSource only on donate page.
 */
function boniface_cybersource_init() {
	add_action( 'wp_ajax_boniface_cybersource_capture_context', 'boniface_cybersource_ajax_capture_context' );
	add_action( 'wp_ajax_nopriv_boniface_cybersource_capture_context', 'boniface_cybersource_ajax_capture_context' );
	add_action( 'wp_ajax_boniface_cybersource_process_payment', 'boniface_cybersource_ajax_process_payment' );
	add_action( 'wp_ajax_nopriv_boniface_cybersource_process_payment', 'boniface_cybersource_ajax_process_payment' );
	add_action( 'wp_ajax_boniface_cybersource_record_payment', 'boniface_cybersource_ajax_record_payment' );
	add_action( 'wp_ajax_nopriv_boniface_cybersource_record_payment', 'boniface_cybersource_ajax_record_payment' );
}
add_action( 'init', 'boniface_cybersource_init' );

/**
 * Enqueue CyberSource checkout scripts on donate and product pages.
 */
function boniface_cybersource_scripts() {
	$localize_data = array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'boniface_cybersource' ),
		'origin'  => home_url( '', 'https' ),
	);

	if ( is_page_template( 'pages/donate-2.php' ) ) {
		$script_path = get_template_directory() . '/assets/js/donation-uc.js';
		$script_uri  = get_template_directory_uri() . '/assets/js/donation-uc.js';
		$version     = file_exists( $script_path ) ? filemtime( $script_path ) : '1.0.0';
		wp_enqueue_script( 'boniface-donation-uc', $script_uri, array( 'jquery' ), $version, true );
		wp_localize_script( 'boniface-donation-uc', 'bonifaceCybersource', $localize_data );
	}

	if ( is_singular( 'product' ) ) {
		$script_path = get_template_directory() . '/assets/js/product-checkout.js';
		$script_uri  = get_template_directory_uri() . '/assets/js/product-checkout.js';
		$version     = file_exists( $script_path ) ? filemtime( $script_path ) : '1.0.0';
		wp_enqueue_script( 'boniface-product-checkout', $script_uri, array( 'jquery' ), $version, true );
		wp_localize_script( 'boniface-product-checkout', 'bonifaceCybersource', $localize_data );
	}
}
add_action( 'wp_enqueue_scripts', 'boniface_cybersource_scripts', 20 );
