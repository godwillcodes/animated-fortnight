/**
 * CyberSource Unified Checkout – donation form integration.
 * Loaded only on the donate page. Requires global bonifaceCybersource (ajaxUrl, nonce, origin).
 */
(function ($) {
	'use strict';

	var MIN_AMOUNT = 10;
	var config = window.bonifaceCybersource || {};
	var $form = $('#donation-form');
	var ucLoaded = false;
	var ucAccept = null;

	if (!$form.length) return;

	function setProgress(step) {
		var dots = document.querySelectorAll('.donation-progress-dot');
		var lines = document.querySelectorAll('.donation-progress-line');
		step = parseInt(step, 10) || 1;
		dots.forEach(function (dot, i) {
			dot.classList.toggle('active', i + 1 === step);
		});
		lines.forEach(function (line, i) {
			line.classList.toggle('completed', i < step);
		});
	}

	function setSubmitState(loading, error) {
		var $btn = $('#donate-submit');
		var $err = $('#donation-uc-error');
		$btn.prop('disabled', !!loading);
		$btn.toggleClass('is-loading', !!loading);
		if (loading) {
			$btn.find('.donate-btn-text').text('Preparing…');
			$btn.find('.donate-btn-icon').addClass('hidden');
		} else {
			$btn.find('.donate-btn-text').text('Proceed to payment');
			$btn.find('.donate-btn-icon').removeClass('hidden');
		}
		if (error && $err.length) {
			$err.text(error).removeClass('hidden');
		} else if ($err.length) {
			$err.addClass('hidden').text('');
		}
	}

	function showDetailsStep() {
		$('#donation-payment-step').addClass('hidden').removeClass('donation-step-visible').addClass('donation-step-hidden');
		$('#donation-success-step, #donation-error-step').addClass('hidden').removeClass('donation-step-visible').addClass('donation-step-hidden');
		$('#donation-details-step').removeClass('hidden').removeClass('donation-step-hidden').addClass('donation-step-visible');
		setProgress(1);
	}

	function showPaymentStep() {
		$('#donation-details-step').removeClass('donation-step-visible').addClass('donation-step-hidden');
		$('#donation-success-step, #donation-error-step').addClass('hidden');
		$('#donation-payment-step').removeClass('hidden').removeClass('donation-step-hidden').addClass('donation-step-visible');
		$('#donation-payment-skeleton').removeClass('hidden');
		setProgress(2);
	}

	function hidePaymentSkeleton() {
		$('#donation-payment-skeleton').addClass('hidden');
	}

	function showSuccessStep(message) {
		$('#donation-payment-step').addClass('hidden');
		$('#donation-success-step').find('.success-message').text(message || 'Thank you. Your donation has been processed.');
		$('#donation-success-step').removeClass('hidden').removeClass('donation-step-hidden').addClass('donation-step-visible');
		$('#donation-error-step').addClass('hidden');
		setProgress(3);
	}

	function showErrorStep(message) {
		$('#donation-payment-step').addClass('hidden');
		$('#donation-error-step').find('.error-message').text(message || 'Something went wrong.');
		$('#donation-error-step').removeClass('hidden').removeClass('donation-step-hidden').addClass('donation-step-visible');
		$('#donation-success-step').addClass('hidden');
		setProgress(3);
	}

	function loadScript(src, integrity) {
		return new Promise(function (resolve, reject) {
			if (!src) {
				reject(new Error('Payment script URL is missing. Please try again.'));
				return;
			}
			if (document.querySelector('script[src="' + src + '"]')) {
				resolve();
				return;
			}
			var script = document.createElement('script');
			script.src = src;
			script.async = true;
			script.crossOrigin = 'anonymous';
			if (integrity) script.integrity = integrity;
			script.onload = function () { resolve(); };
			script.onerror = function () { reject(new Error('Failed to load payment script')); };
			document.body.appendChild(script);
		});
	}

	function getBillingData() {
		var country = $('#billing-country').val();
		if (country === 'OTHER') country = 'US';
		return {
			address1: $('#billing-address1').val(),
			address2: $('#billing-address2').val(),
			locality: $('#billing-city').val(),
			administrativeArea: $('#billing-state').val(),
			postalCode: $('#billing-postal').val(),
			country: country
		};
	}

	function getCaptureContext() {
		var amount = parseFloat($('#amount').val(), 10) || 0;
		var name = $('#name').val();
		var email = $('#email').val();
		var billing = getBillingData();
		if (amount < MIN_AMOUNT) return $.Deferred().reject({ error: 'Minimum donation is $' + MIN_AMOUNT }).promise();
		if (!name || !email) return $.Deferred().reject({ error: 'Please enter your name and email.' }).promise();
		if (!billing.address1 || !billing.locality || !billing.administrativeArea || !billing.postalCode) {
			return $.Deferred().reject({ error: 'Please complete the billing address.' }).promise();
		}

		return $.ajax({
			url: config.ajaxUrl,
			type: 'POST',
			data: {
				action: 'boniface_cybersource_capture_context',
				nonce: config.nonce,
				amount: amount,
				currency: 'USD',
				origin: window.location.origin,
				name: name,
				email: email,
				billing_address1: billing.address1,
				billing_address2: billing.address2,
				billing_locality: billing.locality,
				billing_administrative_area: billing.administrativeArea,
				billing_postal_code: billing.postalCode,
				billing_country: billing.country
			}
		});
	}

	function processPayment(transientToken) {
		var amount = parseFloat($('#amount').val(), 10) || 0;
		var billing = getBillingData();
		return $.ajax({
			url: config.ajaxUrl,
			type: 'POST',
			data: {
				action: 'boniface_cybersource_process_payment',
				nonce: config.nonce,
				transient_token: transientToken,
				amount: amount,
				currency: 'USD',
				name: $('#name').val(),
				email: $('#email').val(),
				message: $('#message').val(),
				billing_address1: billing.address1,
				billing_address2: billing.address2,
				billing_locality: billing.locality,
				billing_administrative_area: billing.administrativeArea,
				billing_postal_code: billing.postalCode,
				billing_country: billing.country
			}
		});
	}

	function clearPaymentStepError() {
		var $err = $('#donation-payment-error');
		$err.addClass('hidden').empty();
	}

	function showPaymentStepError(msg) {
		var $err = $('#donation-payment-error');
		$err.removeClass('hidden').text(msg || 'Something went wrong. Please try again.');
	}

	function runUnifiedCheckout(captureContext, clientLibrary, clientLibraryIntegrity) {
		clearPaymentStepError();
		var sel = document.getElementById('buttonPaymentListContainer');
		var screen = document.getElementById('embeddedPaymentContainer');
		if (sel) sel.innerHTML = '';
		if (screen) screen.innerHTML = '';

		return loadScript(clientLibrary, clientLibraryIntegrity || '')
			.then(function () {
				if (typeof window.Accept !== 'function') {
					throw new Error('Payment library did not load. Please refresh and try again.');
				}
				var jwt = typeof captureContext === 'string' ? captureContext.trim() : '';
				if (!jwt) {
					throw new Error('You have not supplied a valid capture context.');
				}
				return window.Accept(jwt);
			})
			.then(function (accept) {
				ucAccept = accept;
				return accept.unifiedPayments(false);
			})
			.then(function (up) {
				var showArgs = {
					containers: {
						paymentSelection: '#buttonPaymentListContainer',
						paymentScreen: '#embeddedPaymentContainer'
					}
				};
				return new Promise(function (resolve, reject) {
					var resolved = false;
					function done(val) {
						if (resolved) return;
						resolved = true;
						clearTimeout(timeoutId);
						hidePaymentSkeleton();
						resolve(val);
					}
					function fail(err) {
						if (resolved) return;
						resolved = true;
						clearTimeout(timeoutId);
						hidePaymentSkeleton();
						reject(err);
					}
					var timeoutId = setTimeout(function () {
						var hasContent = (sel && (sel.querySelector('iframe') || sel.children.length > 0)) ||
							(screen && (screen.querySelector('iframe') || screen.children.length > 0));
						if (!hasContent) {
							fail(new Error('Payment form did not load. Check the browser console (F12) for errors, then refresh and try again.'));
						} else {
							hidePaymentSkeleton();
						}
					}, 5000);
					setTimeout(function () {
						up.show(showArgs).then(done).catch(function (err) {
							console.error('Unified Checkout show error:', err);
							var msg = (err && err.message) ? err.message : (err && err.reason) ? err.reason : 'Payment form could not be loaded.';
							fail(new Error(msg));
						});
					}, 150);
				});
			});
	}

	$form.on('submit', function (e) {
		e.preventDefault();

		var amount = parseFloat($('#amount').val(), 10) || 0;
		if (isNaN(amount) || amount < MIN_AMOUNT) {
			$('#amount').focus().val(MIN_AMOUNT);
			return;
		}

		setSubmitState(true);

		getCaptureContext()
			.then(function (res) {
				if (!res.success) {
					throw new Error(res.error || 'Could not start payment');
				}
				if (!res.client_library) {
					throw new Error('Payment form could not be loaded. Please refresh the page and try again.');
				}
				showPaymentStep();
				setSubmitState(false);
				var ctx = res.capture_context;
				if (typeof ctx !== 'string' || !ctx) {
					throw new Error('Invalid capture context received. Please refresh and try again.');
				}
				ctx = ctx.trim();
				if (ctx.split('.').length !== 3) {
					throw new Error('Invalid capture context format. Please refresh and try again.');
				}
				return runUnifiedCheckout(
					ctx,
					res.client_library,
					res.client_library_integrity
				);
			})
			.then(function (transientToken) {
				if (!transientToken) throw new Error('No payment token received');
				$('#donate-submit').find('.donate-btn-text').text('Processing…');
				setSubmitState(true);
				return processPayment(transientToken);
			})
			.then(function (res) {
				setSubmitState(false);
				if (res.success) {
					showSuccessStep('Thank you. Your donation has been processed successfully.');
				} else {
					showErrorStep(res.error || 'Payment could not be completed.');
				}
			})
			.catch(function (err) {
				setSubmitState(false);
				var msg = (err && err.error) ? err.error : (err && err.message) ? err.message : 'Something went wrong. Please try again.';
				if ($('#donation-payment-step').is(':visible')) {
					showPaymentStepError(msg);
				} else {
					if (!$('#donation-uc-error').length) {
						$form.find('#donate-submit').closest('div').before('<p id="donation-uc-error" class="text-red-600 text-sm mt-2 hidden"></p>');
					}
					$('#donation-uc-error').text(msg).removeClass('hidden');
				}
			});
	});

	// Back to details
	$('#donation-back-to-details').on('click', function () {
		showDetailsStep();
	});
	// Retry after error
	$('#donation-retry-btn').on('click', function () {
		$('#donation-error-step').addClass('hidden');
		showDetailsStep();
		$('#donation-uc-error').addClass('hidden').text('');
	});

	// Preset amount and form validation (keep existing behavior)
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
			var val = parseInt(this.getAttribute('data-amount'), 10);
			amountInput.value = val;
			setPresetActive(this);
		});
	}
	if (amountInput) {
		amountInput.addEventListener('input', function () {
			var val = parseInt(amountInput.value, 10);
			var matched = null;
			for (var k = 0; k < presets.length; k++) {
				if (parseInt(presets[k].getAttribute('data-amount'), 10) === val) matched = presets[k];
			}
			setPresetActive(matched);
		});
		amountInput.addEventListener('change', function () {
			var val = parseInt(amountInput.value, 10);
			if (isNaN(val) || val < MIN_AMOUNT) amountInput.value = MIN_AMOUNT;
		});
	}

})(jQuery);
