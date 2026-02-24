/**
 * CyberSource Unified Checkout – donation form integration.
 * 3-step flow: Details → Billing Address → Payment.
 */
(function ($) {
	'use strict';

	var MIN_AMOUNT = 10;
	var config = window.bonifaceCybersource || {};
	var $form = $('#donation-form');
	var ucAccept = null;
	var isSubmitting = false;

	if (!$form.length) return;

	console.log('[CYBERSOURCE] Donation form initialized', {
		ajaxUrl: config.ajaxUrl ? 'SET' : 'MISSING',
		nonce: config.nonce ? 'SET' : 'MISSING',
		origin: config.origin || window.location.origin,
		timestamp: new Date().toISOString()
	});

	/* ── Progress indicator ───────────────────────────────── */

	function setProgress(step) {
		step = parseInt(step, 10) || 1;
		$('.donation-progress-dot').each(function (i) {
			$(this).toggleClass('active', i + 1 === step);
		});
		$('.donation-progress-line').each(function (i) {
			$(this).toggleClass('completed', i < step);
		});
	}

	/* ── Step transitions ─────────────────────────────────── */

	function hideAllSteps() {
		$('.donation-form-step').addClass('hidden donation-step-hidden').removeClass('donation-step-visible');
	}

	function showStep($el, progressNum) {
		hideAllSteps();
		$el.removeClass('hidden donation-step-hidden').addClass('donation-step-visible');
		setProgress(progressNum);
		$el[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
	}

	function showDetailsStep()  { showStep($('#donation-details-step'), 1); }
	function showBillingStep()  { showStep($('#donation-billing-step'), 2); }
	function showPaymentStep()  {
		showStep($('#donation-payment-step'), 3);
		$('#donation-payment-skeleton').removeClass('hidden');
	}
	function showSuccessStep(msg) {
		showStep($('#donation-success-step'), 3);
		$('#donation-success-step .success-message').text(msg || 'Thank you. Your donation has been processed.');
	}
	function showErrorStep(msg) {
		showStep($('#donation-error-step'), 3);
		$('#donation-error-step .error-message').text(msg || 'Something went wrong.');
	}

	/* ── Button state helper ──────────────────────────────── */

	function setSubmitState(loading) {
		var $btn = $('#donate-submit');
		$btn.prop('disabled', !!loading).toggleClass('is-loading', !!loading);
		$btn.find('.donate-btn-text').text(loading ? 'Preparing…' : 'Proceed to payment');
		$btn.find('.donate-btn-icon').toggleClass('hidden', !!loading);
	}

	/* ── Validation helpers ───────────────────────────────── */

	function isValidEmail(email) {
		return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
	}

	function isValidPhone(phone) {
		var digits = phone.replace(/[^0-9]/g, '');
		return digits.length >= 7;
	}

	function validateDetailsStep() {
		var amount = parseFloat($('#amount').val()) || 0;
		if (amount < MIN_AMOUNT) { $('#amount').focus(); return 'Minimum donation is $' + MIN_AMOUNT + '.'; }
		if (!$('#name').val().trim()) { $('#name').focus(); return 'Please enter your name.'; }
		var email = $('#email').val().trim();
		if (!email) { $('#email').focus(); return 'Please enter your email.'; }
		if (!isValidEmail(email)) { $('#email').focus(); return 'Please enter a valid email address.'; }
		var phone = $('#phone').val().trim();
		if (!phone) { $('#phone').focus(); return 'Please enter your phone number.'; }
		if (!isValidPhone(phone)) { $('#phone').focus(); return 'Please enter a valid phone number (at least 7 digits).'; }
		return null;
	}

	function validateBillingStep() {
		if (!$('#billing-country').val()) { $('#billing-country').focus(); return 'Please select your country.'; }
		if (!$('#billing-address').val().trim()) { $('#billing-address').focus(); return 'Please enter your street address.'; }
		if (!$('#billing-city').val().trim()) { $('#billing-city').focus(); return 'Please enter your city.'; }
		if (!$('#billing-postal').val().trim()) { $('#billing-postal').focus(); return 'Please enter your postal / ZIP code.'; }
		return null;
	}

	function showBillingError(msg) {
		$('#billing-error').removeClass('hidden').text(msg);
	}
	function clearBillingError() {
		$('#billing-error').addClass('hidden').text('');
	}

	/* ── Collect billing data ─────────────────────────────── */

	function getBillingData() {
		return {
			country:  $('#billing-country').val(),
			address1: $('#billing-address').val().trim(),
			city:     $('#billing-city').val().trim(),
			state:    $('#billing-state').val().trim(),
			postal:   $('#billing-postal').val().trim()
		};
	}

	/* ── CyberSource API calls ────────────────────────────── */

	function getCaptureContext() {
		var billing = getBillingData();
		var data = {
			action: 'boniface_cybersource_capture_context',
			nonce: config.nonce,
			amount: parseFloat($('#amount').val()) || 0,
			currency: 'USD',
			origin: window.location.origin,
			name: $('#name').val(),
			email: $('#email').val(),
			phone: $('#phone').val(),
			billing_country:  billing.country,
			billing_address:  billing.address1,
			billing_city:     billing.city,
			billing_state:    billing.state,
			billing_postal:   billing.postal
		};
		console.log('[CYBERSOURCE] Requesting capture context...', {
			amount: data.amount,
			currency: data.currency,
			origin: data.origin,
			country: data.billing_country,
			city: data.billing_city
		});
		var startTime = Date.now();
		return $.ajax({
			url: config.ajaxUrl,
			type: 'POST',
			data: data
		}).then(function (res) {
			console.log('[CYBERSOURCE] Capture context response in ' + (Date.now() - startTime) + 'ms', {
				success: res.success,
				hasJwt: !!(res.capture_context),
				jwtLength: res.capture_context ? res.capture_context.length : 0,
				hasClientLib: !!(res.client_library),
				clientLib: res.client_library || 'MISSING',
				error: res.error || null
			});
			return res;
		});
	}

	function processPayment(transientToken) {
		var billing = getBillingData();
		var data = {
			action: 'boniface_cybersource_process_payment',
			nonce: config.nonce,
			transient_token: transientToken,
			amount: parseFloat($('#amount').val()) || 0,
			currency: 'USD',
			name: $('#name').val(),
			email: $('#email').val(),
			phone: $('#phone').val(),
			message: $('#message').val(),
			billing_country:  billing.country,
			billing_address:  billing.address1,
			billing_city:     billing.city,
			billing_state:    billing.state,
			billing_postal:   billing.postal
		};
		console.log('[CYBERSOURCE] Sending payment request...', {
			amount: data.amount,
			currency: data.currency,
			tokenLength: transientToken ? transientToken.length : 0,
			tokenParts: transientToken ? transientToken.split('.').length : 0,
			tokenFirst50: transientToken ? transientToken.substring(0, 50) + '...' : 'MISSING',
			country: data.billing_country,
			name: data.name,
			email: data.email
		});
		var startTime = Date.now();
		return $.ajax({
			url: config.ajaxUrl,
			type: 'POST',
			timeout: 60000,
			data: data
		}).then(function (res) {
			console.log('[CYBERSOURCE] Payment response in ' + (Date.now() - startTime) + 'ms', res);
			return res;
		});
	}

	/* ── Script loader ────────────────────────────────────── */

	function loadScript(src, integrity) {
		return new Promise(function (resolve, reject) {
			if (!src) return reject(new Error('Payment script URL is missing.'));
			if (document.querySelector('script[src="' + src + '"]')) {
				console.log('[CYBERSOURCE] Client library already loaded:', src);
				return resolve();
			}
			console.log('[CYBERSOURCE] Loading client library:', src);
			var s = document.createElement('script');
			s.src = src; s.async = true; s.crossOrigin = 'anonymous';
			if (integrity) s.integrity = integrity;
			s.onload = function () {
				console.log('[CYBERSOURCE] Client library loaded OK. Accept function:', typeof window.Accept);
				resolve();
			};
			s.onerror = function () { reject(new Error('Failed to load payment script from: ' + src)); };
			document.body.appendChild(s);
		});
	}

	/* ── Payment error helpers ────────────────────────────── */

	function clearPaymentStepError() { $('#donation-payment-error').addClass('hidden').empty(); }
	function showPaymentStepError(msg) { $('#donation-payment-error').removeClass('hidden').text(msg || 'Something went wrong.'); }

	/* ── Unified Checkout runner ──────────────────────────── */

	function runUnifiedCheckout(captureContext, clientLibrary, clientLibraryIntegrity) {
		clearPaymentStepError();
		var sel = document.getElementById('buttonPaymentListContainer');
		var screen = document.getElementById('embeddedPaymentContainer');
		if (sel) sel.innerHTML = '';
		if (screen) screen.innerHTML = '';

		console.log('[CYBERSOURCE] Running Unified Checkout...', {
			captureContextLength: captureContext ? captureContext.length : 0,
			clientLibrary: clientLibrary,
			hasIntegrity: !!clientLibraryIntegrity
		});

		return loadScript(clientLibrary, clientLibraryIntegrity || '')
			.then(function () {
				if (typeof window.Accept !== 'function') throw new Error('Payment library did not load correctly (Accept not a function).');
				var jwt = (typeof captureContext === 'string') ? captureContext.trim() : '';
				if (!jwt || jwt.split('.').length !== 3) throw new Error('Invalid capture context (not a valid JWT).');
				console.log('[CYBERSOURCE] Calling Accept() with JWT...');
				return window.Accept(jwt);
			})
			.then(function (accept) {
				ucAccept = accept;
				console.log('[CYBERSOURCE] Accept() returned, calling unifiedPayments()...');
				return accept.unifiedPayments(false);
			})
			.then(function (up) {
				console.log('[CYBERSOURCE] unifiedPayments() returned, calling show()...');
				return new Promise(function (resolve, reject) {
					var resolved = false;
					function done(v) {
						if (!resolved) {
							resolved = true;
							clearTimeout(tid);
							$('#donation-payment-skeleton').addClass('hidden');
							console.log('[CYBERSOURCE] up.show() resolved with token:', v ? (typeof v === 'string' ? v.substring(0, 60) + '...' : typeof v) : 'EMPTY');
							resolve(v);
						}
					}
					function fail(e) {
						if (!resolved) {
							resolved = true;
							clearTimeout(tid);
							$('#donation-payment-skeleton').addClass('hidden');
							console.error('[CYBERSOURCE] up.show() failed:', e);
							reject(e);
						}
					}
					var tid = setTimeout(function () {
						var has = (sel && (sel.querySelector('iframe') || sel.children.length)) ||
								  (screen && (screen.querySelector('iframe') || screen.children.length));
						if (!has) fail(new Error('Payment form did not load within 5 seconds. Please refresh and try again.'));
						else {
							console.log('[CYBERSOURCE] Payment form containers populated (iframes loaded).');
							$('#donation-payment-skeleton').addClass('hidden');
						}
					}, 5000);
					setTimeout(function () {
						up.show({ containers: { paymentSelection: '#buttonPaymentListContainer', paymentScreen: '#embeddedPaymentContainer' } })
							.then(done)
							.catch(function (err) {
								console.error('[CYBERSOURCE] up.show() error:', err);
								fail(new Error((err && err.message) || 'Payment form could not be loaded.'));
							});
					}, 150);
				});
			});
	}

	/* ── Step 1 → Step 2: "Continue to billing" button ────── */

	$('#to-billing-step').on('click', function () {
		var err = validateDetailsStep();
		if (err) {
			if (!$('#step1-error').length) {
				$('#to-billing-step').before('<p id="step1-error" class="text-red-600 text-sm mb-3 hidden"></p>');
			}
			$('#step1-error').text(err).removeClass('hidden');
			return;
		}
		$('#step1-error').addClass('hidden');
		console.log('[CYBERSOURCE] Step 1 validated, moving to billing step.');
		showBillingStep();
	});

	/* ── Step 2 → Step 3: Form submit ─────────────────────── */

	$form.on('submit', function (e) {
		e.preventDefault();

		if (isSubmitting) {
			console.log('[CYBERSOURCE] Submit ignored — already in progress.');
			return;
		}

		clearBillingError();

		var err = validateBillingStep();
		if (err) { showBillingError(err); return; }

		isSubmitting = true;
		setSubmitState(true);
		console.log('[CYBERSOURCE] === PAYMENT FLOW STARTED ===');
		console.log('[CYBERSOURCE] Form data:', {
			amount: $('#amount').val(),
			name: $('#name').val(),
			email: $('#email').val(),
			phone: $('#phone').val(),
			billing: getBillingData()
		});

		getCaptureContext()
			.then(function (res) {
				if (!res.success) throw new Error(res.error || 'Could not start payment');
				if (!res.client_library) throw new Error('Payment form could not be loaded (no client library). Please refresh.');
				showPaymentStep();
				setSubmitState(false);
				var ctx = (res.capture_context || '').trim();
				if (!ctx || ctx.split('.').length !== 3) throw new Error('Invalid capture context (bad JWT). Please refresh.');
				return runUnifiedCheckout(ctx, res.client_library, res.client_library_integrity);
			})
			.then(function (transientToken) {
				if (!transientToken) throw new Error('No payment token received from checkout widget.');
				console.log('[CYBERSOURCE] === TRANSIENT TOKEN RECEIVED ===');
				console.log('[CYBERSOURCE] Token type:', typeof transientToken);
				console.log('[CYBERSOURCE] Token length:', transientToken.length);
				console.log('[CYBERSOURCE] Token parts:', transientToken.split('.').length);
				console.log('[CYBERSOURCE] Token (first 100):', transientToken.substring(0, 100));
				console.log('[CYBERSOURCE] Full transient token (copy for Postman):', transientToken);
				setSubmitState(true);
				$('#donate-submit .donate-btn-text').text('Processing…');
				return processPayment(transientToken);
			})
			.then(function (res) {
				isSubmitting = false;
				setSubmitState(false);
				if (res.success) {
					console.log('[CYBERSOURCE] === PAYMENT SUCCESS ===', res);
					showSuccessStep('Thank you. Your donation has been processed successfully.');
				} else {
					console.error('[CYBERSOURCE] === PAYMENT FAILED ===', res);
					showErrorStep(res.error || 'Payment could not be completed.');
				}
			})
			.catch(function (err) {
				isSubmitting = false;
				setSubmitState(false);
				console.error('[CYBERSOURCE] === PAYMENT FLOW ERROR ===', err);
				var msg = (err && err.error) ? err.error : (err && err.message) ? err.message : 'Something went wrong.';
				if (err && (err.status === 502 || err.status === 503 || err.status === 504)) {
					msg = 'Payment failed (server ' + err.status + '). This is usually a CyberSource configuration issue. Please contact support.';
				} else if (err && err.status === 0 && err.statusText === 'timeout') {
					msg = 'Request timed out after 60 seconds. Please check your connection and try again.';
				}
				if (err && err.responseJSON) {
					console.error('[CYBERSOURCE] Server response:', err.responseJSON);
				}
				if ($('#donation-payment-step').is(':visible')) showPaymentStepError(msg);
				else showBillingError(msg);
			});
	});

	/* ── Back / Retry buttons ─────────────────────────────── */

	$('#billing-back-to-details').on('click', showDetailsStep);
	$('#donation-back-to-billing').on('click', function () {
		isSubmitting = false;
		showBillingStep();
	});
	$('#donation-retry-btn').on('click', function () {
		console.log('[CYBERSOURCE] Retry clicked — resetting to billing step for fresh attempt.');
		isSubmitting = false;
		showBillingStep();
	});

	/* ── Preset amount buttons ────────────────────────────── */

	var amountInput = document.getElementById('amount');
	var presets = $form[0].querySelectorAll('.preset-amount');

	function setPresetActive(btn) {
		for (var i = 0; i < presets.length; i++) {
			var b = presets[i];
			b.setAttribute('aria-pressed', b === btn ? 'true' : 'false');
			b.classList.remove('border-neutral-900', 'bg-neutral-900', 'text-white');
			b.classList.add('border-neutral-200', 'bg-white', 'text-neutral-700');
		}
		if (btn) {
			btn.setAttribute('aria-pressed', 'true');
			btn.classList.remove('border-neutral-200', 'bg-white', 'text-neutral-700');
			btn.classList.add('border-neutral-900', 'bg-neutral-900', 'text-white');
		}
	}

	for (var j = 0; j < presets.length; j++) {
		presets[j].addEventListener('click', function () {
			amountInput.value = parseInt(this.getAttribute('data-amount'), 10);
			setPresetActive(this);
		});
	}

	if (amountInput) {
		amountInput.addEventListener('input', function () {
			var val = parseInt(amountInput.value, 10);
			var match = null;
			for (var k = 0; k < presets.length; k++) {
				if (parseInt(presets[k].getAttribute('data-amount'), 10) === val) match = presets[k];
			}
			setPresetActive(match);
		});
		amountInput.addEventListener('change', function () {
			if (isNaN(parseInt(amountInput.value, 10)) || parseInt(amountInput.value, 10) < MIN_AMOUNT) amountInput.value = MIN_AMOUNT;
		});
	}

})(jQuery);
