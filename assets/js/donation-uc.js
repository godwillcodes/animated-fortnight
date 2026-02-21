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

	if (!$form.length) return;

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

	function validateDetailsStep() {
		var amount = parseFloat($('#amount').val()) || 0;
		if (amount < MIN_AMOUNT) { $('#amount').focus(); return 'Minimum donation is $' + MIN_AMOUNT + '.'; }
		if (!$('#name').val().trim()) { $('#name').focus(); return 'Please enter your name.'; }
		if (!$('#email').val().trim()) { $('#email').focus(); return 'Please enter your email.'; }
		if (!$('#phone').val().trim()) { $('#phone').focus(); return 'Please enter your phone number.'; }
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
		return $.ajax({
			url: config.ajaxUrl,
			type: 'POST',
			data: {
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
			}
		});
	}

	function processPayment(transientToken) {
		var billing = getBillingData();
		return $.ajax({
			url: config.ajaxUrl,
			type: 'POST',
			timeout: 60000,
			data: {
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
			}
		});
	}

	/* ── Script loader ────────────────────────────────────── */

	function loadScript(src, integrity) {
		return new Promise(function (resolve, reject) {
			if (!src) return reject(new Error('Payment script URL is missing.'));
			if (document.querySelector('script[src="' + src + '"]')) return resolve();
			var s = document.createElement('script');
			s.src = src; s.async = true; s.crossOrigin = 'anonymous';
			if (integrity) s.integrity = integrity;
			s.onload = resolve;
			s.onerror = function () { reject(new Error('Failed to load payment script')); };
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

		return loadScript(clientLibrary, clientLibraryIntegrity || '')
			.then(function () {
				if (typeof window.Accept !== 'function') throw new Error('Payment library did not load.');
				var jwt = (typeof captureContext === 'string') ? captureContext.trim() : '';
				if (!jwt || jwt.split('.').length !== 3) throw new Error('Invalid capture context.');
				return window.Accept(jwt);
			})
			.then(function (accept) {
				ucAccept = accept;
				return accept.unifiedPayments(false);
			})
			.then(function (up) {
				return new Promise(function (resolve, reject) {
					var resolved = false;
					function done(v) { if (!resolved) { resolved = true; clearTimeout(tid); $('#donation-payment-skeleton').addClass('hidden'); resolve(v); } }
					function fail(e) { if (!resolved) { resolved = true; clearTimeout(tid); $('#donation-payment-skeleton').addClass('hidden'); reject(e); } }
					var tid = setTimeout(function () {
						var has = (sel && (sel.querySelector('iframe') || sel.children.length)) ||
								  (screen && (screen.querySelector('iframe') || screen.children.length));
						if (!has) fail(new Error('Payment form did not load. Please refresh and try again.'));
						else $('#donation-payment-skeleton').addClass('hidden');
					}, 5000);
					setTimeout(function () {
						up.show({ containers: { paymentSelection: '#buttonPaymentListContainer', paymentScreen: '#embeddedPaymentContainer' } })
							.then(done)
							.catch(function (err) {
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
		showBillingStep();
	});

	/* ── Step 2 → Step 3: Form submit ─────────────────────── */

	$form.on('submit', function (e) {
		e.preventDefault();
		clearBillingError();

		var err = validateBillingStep();
		if (err) { showBillingError(err); return; }

		setSubmitState(true);

		getCaptureContext()
			.then(function (res) {
				if (!res.success) throw new Error(res.error || 'Could not start payment');
				if (!res.client_library) throw new Error('Payment form could not be loaded. Please refresh.');
				showPaymentStep();
				setSubmitState(false);
				var ctx = (res.capture_context || '').trim();
				if (!ctx || ctx.split('.').length !== 3) throw new Error('Invalid capture context. Please refresh.');
				return runUnifiedCheckout(ctx, res.client_library, res.client_library_integrity);
			})
			.then(function (transientToken) {
				if (!transientToken) throw new Error('No payment token received');
				setSubmitState(true);
				$('#donate-submit .donate-btn-text').text('Processing…');
				return processPayment(transientToken);
			})
			.then(function (res) {
				setSubmitState(false);
				if (res.success) showSuccessStep('Thank you. Your donation has been processed successfully.');
				else showErrorStep(res.error || 'Payment could not be completed.');
			})
			.catch(function (err) {
				setSubmitState(false);
				var msg = (err && err.error) ? err.error : (err && err.message) ? err.message : 'Something went wrong.';
				if (err && (err.status === 502 || err.status === 503 || err.status === 504)) {
					msg = 'Payment request failed (server ' + err.status + '). Please try again.';
				} else if (err && err.status === 0 && err.statusText === 'timeout') {
					msg = 'Request timed out. Please check your connection and try again.';
				}
				if ($('#donation-payment-step').is(':visible')) showPaymentStepError(msg);
				else showBillingError(msg);
			});
	});

	/* ── Back / Retry buttons ─────────────────────────────── */

	$('#billing-back-to-details').on('click', showDetailsStep);
	$('#donation-back-to-billing').on('click', showBillingStep);
	$('#donation-retry-btn').on('click', function () {
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
