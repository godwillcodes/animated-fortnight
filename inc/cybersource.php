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
				'billTo' => array_merge( array(
					'firstName'          => '',
					'lastName'           => '',
					'address1'           => '',
					'address2'           => '',
					'buildingNumber'     => 'N/A',
					'locality'           => '',
					'administrativeArea' => '',
					'postalCode'         => '',
					'country'            => 'US',
					'email'              => '',
				), $bill_to ),
			),
			'clientReferenceInformation' => array(
				'code' => 'donation-' . substr( uniqid( '', true ), -8 ),
			),
		),
	);

	$body = wp_json_encode( $payload );
	$headers = boniface_cybersource_signature_headers( 'POST', $resource, $body );

	$response = wp_remote_post( $base_url . $resource, array(
		'timeout' => 15,
		'headers' => $headers,
		'body'    => $body,
	) );

	$code = wp_remote_retrieve_response_code( $response );
	$body_response = wp_remote_retrieve_body( $response );

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
	$config = boniface_cybersource_config();
	if ( ! $config ) {
		return array( 'success' => false, 'error' => 'CyberSource is not configured.' );
	}

	$total_amount = number_format( (float) $amount, 2, '.', '' );
	$resource = '/pts/v2/payments';
	$base_url = boniface_cybersource_base_url();

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
			'billTo' => array_merge( array(
				'firstName'          => '',
				'lastName'           => '',
				'address1'           => 'N/A',
				'address2'           => '',
				'buildingNumber'     => 'N/A',
				'locality'           => 'N/A',
				'administrativeArea' => 'N/A',
				'postalCode'         => '00000',
				'country'             => 'US',
				'email'              => '',
				'phoneNumber'        => '0000000000',
			), $bill_to ),
		),
	);

	$body = wp_json_encode( $payload );
	$headers = boniface_cybersource_signature_headers( 'POST', $resource, $body );

	$response = wp_remote_post( $base_url . $resource, array(
		'timeout' => 30,
		'headers' => $headers,
		'body'    => $body,
	) );

	$code = wp_remote_retrieve_response_code( $response );
	$body_response = wp_remote_retrieve_body( $response );

	if ( is_wp_error( $response ) ) {
		return array( 'success' => false, 'error' => $response->get_error_message() );
	}

	$data = json_decode( $body_response, true );
	$reason_code = isset( $data['errorInformation']['reasonCode'] ) ? $data['errorInformation']['reasonCode'] : '';
	$message = isset( $data['errorInformation']['message'] ) ? $data['errorInformation']['message'] : '';

	if ( $code >= 200 && $code < 300 ) {
		$id = isset( $data['id'] ) ? $data['id'] : '';
		return array( 'success' => true, 'id' => $id );
	}

	return array(
		'success'     => false,
		'error'       => $message ?: ( 'Payment failed (' . $code . ')' ),
		'reason_code' => $reason_code,
	);
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

	$bill_to = array();
	if ( $name ) {
		$parts = preg_split( '/\s+/', trim( $name ), 2 );
		$bill_to['firstName'] = $parts[0];
		$bill_to['lastName']  = isset( $parts[1] ) ? $parts[1] : '';
	}
	if ( $email ) {
		$bill_to['email'] = $email;
	}
	$bill_to['address1']           = isset( $_POST['billing_address1'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_address1'] ) ) : '';
	$bill_to['address2']           = isset( $_POST['billing_address2'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_address2'] ) ) : '';
	$bill_to['locality']           = isset( $_POST['billing_locality'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_locality'] ) ) : '';
	$bill_to['administrativeArea'] = isset( $_POST['billing_administrative_area'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_administrative_area'] ) ) : '';
	$bill_to['postalCode']         = isset( $_POST['billing_postal_code'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_postal_code'] ) ) : '';
	$country                       = isset( $_POST['billing_country'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_country'] ) ) : 'US';
	if ( $country === 'OTHER' ) {
		$country = 'US';
	}
	$bill_to['country'] = $country;

	$result = boniface_cybersource_get_capture_context( $amount, $currency, $origin, $bill_to );
	wp_send_json( $result );
}

/**
 * AJAX: Process donation payment with transient token.
 */
function boniface_cybersource_ajax_process_payment() {
	check_ajax_referer( 'boniface_cybersource', 'nonce' );

	$token   = isset( $_POST['transient_token'] ) ? sanitize_text_field( wp_unslash( $_POST['transient_token'] ) ) : '';
	$amount  = isset( $_POST['amount'] ) ? floatval( $_POST['amount'] ) : 0;
	$currency = isset( $_POST['currency'] ) ? sanitize_text_field( wp_unslash( $_POST['currency'] ) ) : 'USD';
	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

	if ( ! $token || $amount < 10 ) {
		wp_send_json( array( 'success' => false, 'error' => 'Invalid request.' ) );
	}

	$bill_to = array( 'email' => $email );
	if ( $name ) {
		$parts = preg_split( '/\s+/', trim( $name ), 2 );
		$bill_to['firstName'] = $parts[0];
		$bill_to['lastName']  = isset( $parts[1] ) ? $parts[1] : '';
	}
	$bill_to['address1']           = isset( $_POST['billing_address1'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_address1'] ) ) : '';
	$bill_to['address2']           = isset( $_POST['billing_address2'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_address2'] ) ) : '';
	$bill_to['locality']           = isset( $_POST['billing_locality'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_locality'] ) ) : '';
	$bill_to['administrativeArea'] = isset( $_POST['billing_administrative_area'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_administrative_area'] ) ) : '';
	$bill_to['postalCode']         = isset( $_POST['billing_postal_code'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_postal_code'] ) ) : '';
	$country                       = isset( $_POST['billing_country'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_country'] ) ) : 'US';
	if ( $country === 'OTHER' ) {
		$country = 'US';
	}
	$bill_to['country'] = $country;

	$result = boniface_cybersource_process_payment( $token, $amount, $currency, $bill_to );

	if ( $result['success'] ) {
		// Optional: save donation record (e.g. custom post type or table).
		do_action( 'boniface_donation_payment_success', array(
			'amount'    => $amount,
			'currency'  => $currency,
			'name'      => $name,
			'email'     => $email,
			'message'   => isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '',
			'payment_id' => isset( $result['id'] ) ? $result['id'] : '',
			'billing'   => array(
				'address1' => $bill_to['address1'] ?? '',
				'address2' => $bill_to['address2'] ?? '',
				'locality' => $bill_to['locality'] ?? '',
				'administrativeArea' => $bill_to['administrativeArea'] ?? '',
				'postalCode' => $bill_to['postalCode'] ?? '',
				'country'   => $bill_to['country'] ?? 'US',
			),
		) );
	}

	wp_send_json( $result );
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
