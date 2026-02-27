/**
 * CyberSource Unified Checkout – donation form (completeMandate mode).
 * 2-step flow: Details → Payment (UC widget handles billing + card + payment).
 */
(function ($) {
	'use strict';

	var MIN_AMOUNT = 10;
	var config = window.bonifaceCybersource || {};
	var $form = $('#donation-form');
	var ucAccept = null;
	var isSubmitting = false;

	if (!$form.length) return;

	console.log('[CYBERSOURCE] Donation form initialized (completeMandate mode)', {
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
	function showPaymentStep()  {
		showStep($('#donation-payment-step'), 2);
		$('#donation-payment-skeleton').removeClass('hidden');
	}
	function showSuccessStep(msg) {
		showStep($('#donation-success-step'), 2);
		$('#donation-success-step .success-message').text(msg || 'Thank you. Your donation has been processed.');
	}
	function showErrorStep(msg) {
		showStep($('#donation-error-step'), 2);
		$('#donation-error-step .error-message').text(msg || 'Something went wrong.');
	}

	/* ── Button state helper ──────────────────────────────── */

	function setSubmitState(loading) {
		var $btn = $('#donate-submit');
		$btn.prop('disabled', !!loading).toggleClass('is-loading', !!loading);
		$btn.find('.donate-btn-text').text(loading ? 'Preparing payment…' : 'Continue to payment');
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

	/* ── Error helpers ────────────────────────────────────── */

	function showFormError(msg) {
		if (!$('#step1-error').length) {
			$('#donate-submit').closest('.pt-2').before('<div id="step1-error" class="rounded-xl border-2 border-red-200 bg-red-50 p-4 text-red-800 text-sm font-medium hidden"></div>');
		}
		$('#step1-error').text(msg).removeClass('hidden');
	}
	function clearFormError() {
		$('#step1-error').addClass('hidden').text('');
	}
	function clearPaymentStepError() { $('#donation-payment-error').addClass('hidden').empty(); }
	function showPaymentStepError(msg) { $('#donation-payment-error').removeClass('hidden').text(msg || 'Something went wrong.'); }

	/* ── CyberSource API calls ────────────────────────────── */

	function getCaptureContext() {
		var data = {
			action: 'boniface_cybersource_capture_context',
			nonce: config.nonce,
			amount: parseFloat($('#amount').val()) || 0,
			currency: 'USD',
			origin: window.location.origin,
			name: $('#name').val(),
			email: $('#email').val(),
			phone: $('#phone').val()
		};
		console.log('[CYBERSOURCE] Requesting capture context (completeMandate)...', {
			amount: data.amount,
			currency: data.currency,
			origin: data.origin,
			name: data.name,
			email: data.email
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
				error: res.error || null
			});
			return res;
		});
	}

	function recordPayment(result) {
		var paymentId = '';
		var paymentStatus = 'UNKNOWN';
		var rawForServer = result;

		if (typeof result === 'string' && result.indexOf('eyJ') === 0) {
			var parsed = parseCompleteMandateJwt(result);
			if (parsed) {
				paymentId = parsed.jti || '';
				paymentStatus = parsed.jti ? 'CAPTURED' : 'UNKNOWN';
			}
			rawForServer = result;
		} else if (result && typeof result === 'object') {
			paymentId = (result.id != null) ? String(result.id) : '';
			paymentStatus = (result.status != null) ? String(result.status) : (paymentId ? 'CAPTURED' : 'UNKNOWN');
			rawForServer = JSON.stringify(result);
		}

		return $.ajax({
			url: config.ajaxUrl,
			type: 'POST',
			data: {
				action: 'boniface_cybersource_record_payment',
				nonce: config.nonce,
				name: $('#name').val(),
				email: $('#email').val(),
				phone: $('#phone').val(),
				amount: parseFloat($('#amount').val()) || 0,
				currency: 'USD',
				message: $('#message').val(),
				payment_id: paymentId,
				payment_status: paymentStatus,
				raw_result: rawForServer
			}
		});
	}

	/**
	 * Parse completeMandate response JWT payload (Base64url). Returns { jti } or null.
	 */
	function parseCompleteMandateJwt(jwtStr) {
		try {
			var parts = (typeof jwtStr === 'string') ? jwtStr.trim().split('.') : [];
			if (parts.length !== 3) return null;
			var payloadB64 = parts[1].replace(/-/g, '+').replace(/_/g, '/');
			var jsonStr = decodeURIComponent(atob(payloadB64).split('').map(function (c) {
				return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
			}).join(''));
			var payload = JSON.parse(jsonStr);
			return { jti: payload.jti || null };
		} catch (e) {
			console.warn('[CYBERSOURCE] JWT parse error:', e);
			return null;
		}
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

	/* ── Unified Checkout runner (completeMandate, embedded) ── */

	function runUnifiedCheckout(captureContext, clientLibrary, clientLibraryIntegrity) {
		clearPaymentStepError();
		var sel = document.getElementById('buttonPaymentListContainer');
		var screen = document.getElementById('embeddedPaymentContainer');
		if (sel) sel.innerHTML = '';
		if (screen) screen.innerHTML = '';

		console.log('[CYBERSOURCE] Running Unified Checkout (completeMandate, embedded mode)...', {
			captureContextLength: captureContext ? captureContext.length : 0,
			clientLibrary: clientLibrary,
			hasIntegrity: !!clientLibraryIntegrity
		});

		return loadScript(clientLibrary, clientLibraryIntegrity || '')
			.then(function () {
				if (typeof window.Accept !== 'function') throw new Error('Payment library did not load correctly.');
				var jwt = (typeof captureContext === 'string') ? captureContext.trim() : '';
				if (!jwt || jwt.split('.').length !== 3) throw new Error('Invalid capture context.');
				console.log('[CYBERSOURCE] Calling Accept() with JWT...');
				return window.Accept(jwt);
			})
			.then(function (accept) {
				ucAccept = accept;
				console.log('[CYBERSOURCE] Accept() returned, calling unifiedPayments(false) for embedded mode...');
				return accept.unifiedPayments(false);
			})
			.then(function (up) {
				console.log('[CYBERSOURCE] unifiedPayments() returned, calling show() with containers...');
				return new Promise(function (resolve, reject) {
					var resolved = false;
					function done(v) {
						if (!resolved) {
							resolved = true;
							clearTimeout(tid);
							$('#donation-payment-skeleton').addClass('hidden');
							console.log('[CYBERSOURCE] up.show() resolved:', typeof v, v);
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
						if (!has) fail(new Error('Payment form did not load within 10 seconds. Please refresh and try again.'));
						else {
							console.log('[CYBERSOURCE] Payment form containers populated.');
							$('#donation-payment-skeleton').addClass('hidden');
						}
					}, 10000);
					setTimeout(function () {
						up.show({ containers: { paymentSelection: '#buttonPaymentListContainer', paymentScreen: '#embeddedPaymentContainer' } })
							.then(done)
							.catch(function (err) {
								console.error('[CYBERSOURCE] up.show() error:', err);
								fail(new Error((err && err.message) || 'Payment could not be processed.'));
							});
					}, 150);
				});
			});
	}

	/* ── Form submit → UC widget ──────────────────────────── */

	$form.on('submit', function (e) {
		e.preventDefault();

		if (isSubmitting) {
			console.log('[CYBERSOURCE] Submit ignored — already in progress.');
			return;
		}

		clearFormError();

		var err = validateDetailsStep();
		if (err) { showFormError(err); return; }

		isSubmitting = true;
		setSubmitState(true);
		console.log('[CYBERSOURCE] === PAYMENT FLOW STARTED (completeMandate) ===');
		console.log('[CYBERSOURCE] Form data:', {
			amount: $('#amount').val(),
			name: $('#name').val(),
			email: $('#email').val(),
			phone: $('#phone').val()
		});

		getCaptureContext()
			.then(function (res) {
				if (!res.success) throw new Error(res.error || 'Could not start payment.');
				if (!res.client_library) throw new Error('Payment form could not be loaded. Please refresh.');
				showPaymentStep();
				setSubmitState(false);
				var ctx = (res.capture_context || '').trim();
				if (!ctx || ctx.split('.').length !== 3) throw new Error('Invalid capture context. Please refresh.');
				return runUnifiedCheckout(ctx, res.client_library, res.client_library_integrity);
			})
			.then(function (result) {
				console.log('[CYBERSOURCE] === completeMandate RESULT ===');
				console.log('[CYBERSOURCE] Result type:', typeof result);
				console.log('[CYBERSOURCE] Result value:', JSON.stringify(result, null, 2));

				if (typeof result === 'string' && result.indexOf('eyJ') === 0) {
					console.log('[CYBERSOURCE] Got transient token (completeMandate may have processed internally).');
					console.log('[CYBERSOURCE] Token length:', result.length);
				}

				if (result && typeof result === 'object') {
					console.log('[CYBERSOURCE] Result keys:', Object.keys(result));
					if (result.paymentResponse) {
						console.log('[CYBERSOURCE] paymentResponse:', result.paymentResponse);
					}
				}

				recordPayment(result).always(function () {
					console.log('[CYBERSOURCE] Payment recorded on server.');
				});

				isSubmitting = false;
				setSubmitState(false);
				showSuccessStep('Thank you! Your donation has been processed successfully.');
			})
			.catch(function (err) {
				isSubmitting = false;
				setSubmitState(false);
				console.error('[CYBERSOURCE] === PAYMENT FLOW ERROR ===', err);

				var msg = 'Something went wrong.';
				if (err && err.error) msg = err.error;
				else if (err && err.message) msg = err.message;

				if (err && err.responseJSON) {
					console.error('[CYBERSOURCE] Server response:', err.responseJSON);
				}

				if ($('#donation-payment-step').is(':visible')) {
					showPaymentStepError(msg);
				} else {
					showFormError(msg);
				}
			});
	});

	/* ── Back / Retry buttons ─────────────────────────────── */

	$('#donation-back-to-details').on('click', function () {
		isSubmitting = false;
		showDetailsStep();
	});
	$('#donation-retry-btn').on('click', function () {
		console.log('[CYBERSOURCE] Retry clicked — resetting to details step.');
		isSubmitting = false;
		showDetailsStep();
	});

	/* ── Preset amount buttons ────────────────────────────── */

	var amountInput = document.getElementById('amount');
	var presets = $form[0].querySelectorAll('.preset-amount');

	function setPresetActive(btn) {
		for (var i = 0; i < presets.length; i++) {
			presets[i].setAttribute('aria-pressed', presets[i] === btn ? 'true' : 'false');
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
