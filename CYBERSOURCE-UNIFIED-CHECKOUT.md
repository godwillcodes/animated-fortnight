# CyberSource Unified Checkout – Donation Integration

This document describes the CyberSource Unified Checkout integration used for donations on the **Donate 2** page. It covers setup, configuration, flow, and file reference for context.

---

## Overview

- **What it does:** A single, secure checkout widget (cards + digital wallets like Apple Pay, Google Pay) with minimal PCI scope. Payment data is captured by CyberSource; our server only receives a short-lived **transient token** and calls the Payments API to authorize and capture.
- **Where it runs:** Only on pages that use the **Donate 2** template (`pages/donate-2.php`). The donation form is in `components/payment/payment-form.php`.

---

## Integration Flow (High Level)

1. **Server:** Generate a **Capture Context (JWT)** via CyberSource’s API (amount, currency, allowed payment methods, target origin).
2. **Client:** Load the Unified Checkout JavaScript from the URL returned in the Capture Context; pass the JWT to render the secure payment widget.
3. **Customer:** Enters card or wallet details in the widget; CyberSource returns a **transient token** to the front end.
4. **Server:** Front end sends the transient token + donor details to our backend; we call CyberSource’s **Payments API** to authorize and capture the payment.

---

## Files Involved

| File | Purpose |
|------|--------|
| `inc/cybersource.php` | Config, HTTP Signature auth, Capture Context request, Payments API call, AJAX handlers, script enqueue. |
| `assets/js/donation-uc.js` | Form submit → get capture context → load UC script → show widget → send token to backend → show success/error. |
| `components/payment/payment-form.php` | Donation form (amount, name, email, message), step containers for details → payment widget → success/error. |
| `pages/donate-2.php` | Donate 2 template; includes the payment form via `get_template_part('components/payment/payment', 'form')`. |

---

## Configuration

Credentials come from **CyberSource Business Centre** (Key Management). Use one of the following.

### Option A: Theme Customizer (easiest)

1. In WordPress admin go to **Appearance → Customize**.
2. Open the **CyberSource (Donations)** section.
3. Enter **Merchant ID**, **Key ID** (API key serial number), and **Shared Secret** from Business Centre → Key Management.
4. Set **Environment** to **Test (sandbox)** or **Production**.
5. Click **Publish**.

Config is stored in theme mods. Constants (below) override these values if set.

### Option B: `wp-config.php`

```php
define( 'CYBERSOURCE_MERCHANT_ID', 'your_merchant_id' );
define( 'CYBERSOURCE_KEY_ID', 'your_key_serial_number' );   // Serial number of the key pair
define( 'CYBERSOURCE_SHARED_SECRET', 'your_shared_secret' );
define( 'CYBERSOURCE_ENV', 'test' );   // 'test' or 'production'
```

- **Merchant ID:** From Business Centre (account/merchant ID).
- **Key ID:** Serial number of the API key pair (Key Management).
- **Shared Secret:** The shared secret for that key pair.
- **Env:** `test` → `apitest.cybersource.com`; `production` → `api.cybersource.com`.

### Option C: Filter in theme or plugin

```php
add_filter( 'boniface_cybersource_config', function ( $config ) {
    $config['merchant_id']   = 'your_merchant_id';
    $config['key_id']        = 'your_key_serial_number';
    $config['shared_secret'] = 'your_shared_secret';
    $config['env']           = 'test';  // or 'production'
    return $config;
} );
```

**Priority:** Constants > Theme Customizer > filter. If any of `merchant_id`, `key_id`, or `shared_secret` is empty, you get “CyberSource is not configured” and the integration is disabled.

---

## Server-Side (`inc/cybersource.php`)

### Config

- **`boniface_cybersource_config()`**  
  Returns config array or `null` if not fully configured. Reads constants first, then filter `boniface_cybersource_config`.

### Authentication

- **`boniface_cybersource_signature_headers( $method, $resource, $body )`**  
  Builds HTTP Signature headers for CyberSource REST: Digest (SHA-256 of body), Date, Host, `v-c-merchant-id`, Signature (HmacSHA256 over the signed header string). Used for both Capture Context and Payments API.

### Capture Context

- **`boniface_cybersource_get_capture_context( $amount, $currency, $origin, $bill_to )`**  
  - POSTs to `/up/v1/capture-contexts` with:
    - Amount, currency (default USD), target origin (default `home_url( '', 'https' )`).
    - Allowed payment types: PANENTRY, CLICKTOPAY, APPLEPAY, GOOGLEPAY.
    - Card networks: VISA, MASTERCARD.
    - `completeMandate.type` = CAPTURE.
  - Returns: `success`, `capture_context` (JWT), `client_library`, `client_library_integrity` (from JWT payload for loading the UC script).  
  - Minimum amount enforced: 10.

### Payments (authorize + capture)

- **`boniface_cybersource_process_payment( $transient_token_jwt, $amount, $currency, $bill_to )`**  
  - POSTs to `/pts/v2/payments` with:
    - `tokenInformation.transientTokenJwt`
    - `orderInformation.amountDetails` (totalAmount, currency)
    - `orderInformation.billTo` (firstName, lastName, email, etc.)
    - `processingInformation.capture` = true  
  - Returns: `success`, `id` (payment id) or `error`, `reason_code`.

### AJAX actions

- **`boniface_cybersource_capture_context`**  
  - Expects: `nonce`, `amount`, `currency`, `origin`, `name`, `email`.  
  - Returns JSON: `success`, `capture_context`, `client_library`, `client_library_integrity` or `error`.  
  - Nonce: `boniface_cybersource`.

- **`boniface_cybersource_process_payment`**  
  - Expects: `nonce`, `transient_token`, `amount`, `currency`, `name`, `email`, `message`.  
  - Calls `boniface_cybersource_process_payment()`; on success runs `do_action( 'boniface_donation_payment_success', $data )` with amount, currency, name, email, message, payment_id.  
  - Returns JSON: `success`, `id` or `error`, `reason_code`.

### Script loading

- **`boniface_cybersource_scripts()`**  
  - Runs only when `is_page_template( 'pages/donate-2.php' )`.  
  - Enqueues `assets/js/donation-uc.js` with localized `bonifaceCybersource`: `ajaxUrl`, `nonce`, `origin`.

---

## Client-Side (`assets/js/donation-uc.js`)

- Depends on jQuery and global `window.bonifaceCybersource` (ajaxUrl, nonce, origin).
- Targets `#donation-form`; does nothing if the form is not present.

### Flow

1. **Submit**  
   - Prevent default. Validate amount (min $10), name, email.  
   - Set button to “Preparing…”, disabled.

2. **Capture context**  
   - POST to `boniface_cybersource_capture_context` with amount, currency, origin, name, email.  
   - On failure: show error under form, re-enable button.

3. **Show payment step**  
   - Hide `#donation-details-step`, show `#donation-payment-step` (contains `#uc-button-list` and `#cybersource-container`).

4. **Load UC script**  
   - Load script from `client_library` with optional `client_library_integrity`.  
   - Then: `Accept(captureContext)` → `accept.unifiedPayments({ sidebar: false })` → `up.show({ containers: { paymentSelection: '#uc-button-list', paymentScreen: '#cybersource-container' } })`.  
   - `up.show()` resolves with the **transient token** when the user completes payment.

5. **Process payment**  
   - POST to `boniface_cybersource_process_payment` with transient token, amount, currency, name, email, message.  
   - On success: show `#donation-success-step` with success message.  
   - On failure: show `#donation-error-step` with error message.

### Preset amounts

- Preset buttons and “custom amount” behavior (including min $10) are handled in this script; no separate inline script in the form.

---

## Payment Form Markup (`components/payment/payment-form.php`)

- **Step 1 – Donor details:** `#donation-details-step`: amount (presets + input, min $10 USD), full name, email, optional message, “Proceed to payment” button (with `.btn-text` for JS), “Secure donation · CyberSource Unified Checkout” line.
- **Step 2 – Payment:** `#donation-payment-step` (hidden initially): short copy, `#uc-button-list`, `#cybersource-container` for the Unified Checkout widget.
- **Step 3 – Result:** `#donation-success-step`, `#donation-error-step` (hidden initially), with `.success-message` and `.error-message` for text.

Form ID: `donation-form`. Submit button ID: `donate-submit`.

---

## CyberSource API Endpoints Used

| Environment | Capture Context | Payments |
|-------------|-----------------|----------|
| Test | `POST https://apitest.cybersource.com/up/v1/capture-contexts` | `POST https://apitest.cybersource.com/pts/v2/payments` |
| Production | `POST https://api.cybersource.com/up/v1/capture-contexts` | `POST https://api.cybersource.com/pts/v2/payments` |

Authentication for both: HTTP Signature (Digest, Date, Host, v-c-merchant-id, Signature with HmacSHA256 and keyid).

---

## Saving Donations / Receipts

On successful payment, the backend fires:

```php
do_action( 'boniface_donation_payment_success', array(
    'amount'    => $amount,
    'currency'  => $currency,
    'name'      => $name,
    'email'     => $email,
    'message'   => $message,
    'payment_id' => $payment_id,  // CyberSource payment ID
) );
```

You can hook here to save a donation record (e.g. custom post type or table) or send a receipt email.

Example:

```php
add_action( 'boniface_donation_payment_success', function ( $data ) {
    // e.g. create a Donation post, send email, log to CRM
} );
```

---

## Testing

- Use **test** credentials and `CYBERSOURCE_ENV = 'test'`.  
- CyberSource provides test card numbers and scenarios in their docs (Unified Checkout test cards, auth, etc.).  
- Ensure the site URL used for “Proceed to payment” is in the **target origin** sent in the Capture Context (we use `home_url( '', 'https' )`); for local dev use HTTPS if CyberSource requires it.

---

## Reference: What Unified Checkout Gives You

- Single secure checkout widget: cards + digital wallets (Apple Pay, Google Pay, etc.).
- Reduced PCI scope: sensitive payment data is captured by CyberSource, not your server.
- Simple integration: Capture Context → load script → show widget → receive transient token → call Payments API.

For full API details, see CyberSource’s Unified Checkout and Payments REST documentation.
