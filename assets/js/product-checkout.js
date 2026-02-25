/**
 * CyberSource Unified Checkout – product purchase (completeMandate mode).
 * Flow: Buyer details → Payment widget (UC handles billing + card + payment).
 */
(function ($) {
	'use strict';

	var config = window.bonifaceCybersource || {};
	var $form = $('#product-checkout-form');
	var isSubmitting = false;

	if (!$form.length) return;

	var price = parseFloat($('#product-price').val()) || 0;
	var title = $('#product-title').val() || 'Product';

	/* ── Helpers ──────────────────────────────────────────── */

	function showEl(id)  { $('#' + id).removeClass('hidden'); }
	function hideEl(id)  { $('#' + id).addClass('hidden'); }

	function setBtnState(loading) {
		var $btn = $('#product-buy-btn');
		$btn.prop('disabled', !!loading).toggleClass('is-loading', !!loading);
		$btn.find('.donate-btn-text').text(loading ? 'Preparing payment…' : 'Buy now — $' + price.toFixed(2));
		$btn.find('.donate-btn-icon').toggleClass('hidden', !!loading);
	}

	function showFormError(msg) {
		$('#product-form-error').text(msg).removeClass('hidden');
	}
	function clearFormError() {
		$('#product-form-error').addClass('hidden').text('');
	}

	function showSuccess(msg) {
		$form.addClass('hidden');
		hideEl('product-payment-step');
		hideEl('product-error-step');
		$('#product-success-step .product-success-msg').text(msg || 'Purchase complete!');
		showEl('product-success-step');
	}

	function showError(msg) {
		$form.addClass('hidden');
		hideEl('product-payment-step');
		hideEl('product-success-step');
		$('#product-error-step .product-error-msg').text(msg || 'Something went wrong.');
		showEl('product-error-step');
	}

	function showPaymentStep() {
		$form.addClass('hidden');
		hideEl('product-error-step');
		hideEl('product-success-step');
		showEl('product-payment-step');
		showEl('product-payment-skeleton');
	}

	function resetToForm() {
		hideEl('product-payment-step');
		hideEl('product-success-step');
		hideEl('product-error-step');
		$form.removeClass('hidden');
		isSubmitting = false;
		setBtnState(false);
	}

	/* ── Validation ──────────────────────────────────────── */

	function validate() {
		if (!$('#buyer-name').val().trim()) { $('#buyer-name').focus(); return 'Please enter your name.'; }
		var email = $('#buyer-email').val().trim();
		if (!email) { $('#buyer-email').focus(); return 'Please enter your email.'; }
		if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { $('#buyer-email').focus(); return 'Please enter a valid email.'; }
		var phone = $('#buyer-phone').val().trim();
		if (!phone) { $('#buyer-phone').focus(); return 'Please enter your phone number.'; }
		if (phone.replace(/[^0-9]/g, '').length < 7) { $('#buyer-phone').focus(); return 'Please enter a valid phone number.'; }
		return null;
	}

	/* ── Script loader ───────────────────────────────────── */

	function loadScript(src, integrity) {
		return new Promise(function (resolve, reject) {
			if (!src) return reject(new Error('Payment script URL is missing.'));
			if (document.querySelector('script[src="' + src + '"]')) return resolve();
			var s = document.createElement('script');
			s.src = src; s.async = true; s.crossOrigin = 'anonymous';
			if (integrity) s.integrity = integrity;
			s.onload = resolve;
			s.onerror = function () { reject(new Error('Failed to load payment script.')); };
			document.body.appendChild(s);
		});
	}

	/* ── CyberSource API ─────────────────────────────────── */

	function getCaptureContext() {
		return $.ajax({
			url: config.ajaxUrl,
			type: 'POST',
			data: {
				action: 'boniface_cybersource_capture_context',
				nonce: config.nonce,
				amount: price,
				currency: 'USD',
				origin: window.location.origin,
				name: $('#buyer-name').val(),
				email: $('#buyer-email').val(),
				phone: $('#buyer-phone').val()
			}
		});
	}

	function recordPayment(result) {
		return $.ajax({
			url: config.ajaxUrl,
			type: 'POST',
			data: {
				action: 'boniface_cybersource_record_payment',
				nonce: config.nonce,
				name: $('#buyer-name').val(),
				email: $('#buyer-email').val(),
				phone: $('#buyer-phone').val(),
				amount: price,
				currency: 'USD',
				message: 'Product purchase: ' + title,
				payment_id: (result && result.id) || '',
				payment_status: (result && result.status) || 'UNKNOWN',
				raw_result: JSON.stringify(result || {})
			}
		});
	}

	/* ── Unified Checkout runner ─────────────────────────── */

	function runCheckout(captureContext, clientLibrary, clientLibraryIntegrity) {
		var sel = document.getElementById('productPaymentListContainer');
		var screen = document.getElementById('productPaymentContainer');
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
				return accept.unifiedPayments(false);
			})
			.then(function (up) {
				return new Promise(function (resolve, reject) {
					var resolved = false;
					function done(v) { if (!resolved) { resolved = true; clearTimeout(tid); hideEl('product-payment-skeleton'); resolve(v); } }
					function fail(e) { if (!resolved) { resolved = true; clearTimeout(tid); hideEl('product-payment-skeleton'); reject(e); } }
					var tid = setTimeout(function () {
						var has = (sel && (sel.querySelector('iframe') || sel.children.length)) ||
								  (screen && (screen.querySelector('iframe') || screen.children.length));
						if (!has) fail(new Error('Payment form did not load. Please refresh and try again.'));
						else hideEl('product-payment-skeleton');
					}, 10000);
					setTimeout(function () {
						up.show({ containers: { paymentSelection: '#productPaymentListContainer', paymentScreen: '#productPaymentContainer' } })
							.then(done)
							.catch(function (err) { fail(new Error((err && err.message) || 'Payment could not be processed.')); });
					}, 150);
				});
			});
	}

	/* ── Form submit ─────────────────────────────────────── */

	$form.on('submit', function (e) {
		e.preventDefault();
		if (isSubmitting) return;

		clearFormError();
		var err = validate();
		if (err) { showFormError(err); return; }
		if (price <= 0) { showFormError('Product price is not set.'); return; }

		isSubmitting = true;
		setBtnState(true);

		getCaptureContext()
			.then(function (res) {
				if (!res.success) throw new Error(res.error || 'Could not start payment.');
				if (!res.client_library) throw new Error('Payment form could not be loaded. Please refresh.');
				showPaymentStep();
				setBtnState(false);
				var ctx = (res.capture_context || '').trim();
				if (!ctx || ctx.split('.').length !== 3) throw new Error('Invalid capture context.');
				return runCheckout(ctx, res.client_library, res.client_library_integrity);
			})
			.then(function (result) {
				recordPayment(result).always(function () {});
				isSubmitting = false;
				showSuccess('Thank you! Your purchase of "' + title + '" is complete.');
			})
			.catch(function (err) {
				isSubmitting = false;
				setBtnState(false);
				var msg = 'Something went wrong.';
				if (err && err.error) msg = err.error;
				else if (err && err.message) msg = err.message;

				if ($('#product-payment-step').is(':visible')) {
					$('#product-payment-error').text(msg).removeClass('hidden');
				} else {
					showFormError(msg);
				}
			});
	});

	/* ── Back / Retry ────────────────────────────────────── */

	$('#product-back-btn').on('click', resetToForm);
	$('#product-retry-btn').on('click', resetToForm);

})(jQuery);
