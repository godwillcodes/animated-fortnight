<form class="payment-form space-y-12" id="donation-form">

    <!-- Step 1: Donor details + amount -->
    <div id="donation-details-step">
    <!-- Amount: Stripe-style preset + custom -->
    <div>
        <label class="block text-sm font-medium text-neutral-900 mb-3">
            Donation amount
        </label>
        <div class="grid grid-cols-3 sm:grid-cols-6 gap-2 mb-3">
            <?php
            $presets = [ 10, 25, 50, 100, 250, 500 ];
            foreach ( $presets as $p ) :
            ?>
            <button type="button" data-amount="<?php echo (int) $p; ?>"
                class="preset-amount h-11 rounded-lg border border-neutral-200 bg-white text-sm font-medium text-neutral-700 transition-all duration-150 hover:border-neutral-400 hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-neutral-900 focus:ring-offset-1 aria-pressed="false">
                $<?php echo (int) $p; ?>
            </button>
            <?php endforeach; ?>
        </div>
        <div class="relative">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-500 font-medium text-base pointer-events-none">$</span>
            <input type="number" id="amount" name="amount" min="10" step="1" value="25" placeholder="0"
                class="amount-input w-full rounded-lg border border-neutral-200 bg-white pl-8 pr-4 py-3.5 text-base text-neutral-900 placeholder:text-neutral-400 shadow-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-neutral-900 focus:border-transparent focus:shadow-[0_0_0_3px_rgba(0,0,0,0.08)] invalid:border-red-300 invalid:focus:ring-red-500"
                required />
        </div>
        <p class="mt-1.5 text-xs text-neutral-500">Minimum donation is $10 USD.</p>
    </div>

    <!-- Divider -->
    <div class="border-t border-neutral-100"></div>

    <!-- Full Name -->
    <div>
        <label for="name" class="block text-sm font-medium text-neutral-900 mb-1.5">
            Full name
        </label>
        <input type="text" id="name" name="name" autocomplete="name" placeholder="John Doe"
            class="w-full rounded-lg border border-neutral-200 bg-white px-4 py-3.5 text-base text-neutral-900 placeholder:text-neutral-400 shadow-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-neutral-900 focus:border-transparent focus:shadow-[0_0_0_3px_rgba(0,0,0,0.08)]"
            required />
    </div>

    <!-- Email -->
    <div>
        <label for="email" class="block text-sm font-medium text-neutral-900 mb-1.5">
            Email address
        </label>
        <input type="email" id="email" name="email" autocomplete="email" placeholder="you@example.com"
            class="w-full rounded-lg border border-neutral-200 bg-white px-4 py-3.5 text-base text-neutral-900 placeholder:text-neutral-400 shadow-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-neutral-900 focus:border-transparent focus:shadow-[0_0_0_3px_rgba(0,0,0,0.08)]"
            required />
    </div>

    <!-- Billing address (required by payment processor) -->
    <div class="border-t border-neutral-100 pt-6">
        <h3 class="text-sm font-medium text-neutral-900 mb-3">Billing address</h3>
        <div class="space-y-4">
            <div>
                <label for="billing-address1" class="block text-sm font-medium text-neutral-900 mb-1.5">Address line 1</label>
                <input type="text" id="billing-address1" name="billing_address1" autocomplete="street-address-line1" placeholder="Street address"
                    class="w-full rounded-lg border border-neutral-200 bg-white px-4 py-3.5 text-base text-neutral-900 placeholder:text-neutral-400 shadow-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-neutral-900 focus:border-transparent focus:shadow-[0_0_0_3px_rgba(0,0,0,0.08)]"
                    required />
            </div>
            <div>
                <label for="billing-address2" class="block text-sm font-medium text-neutral-900 mb-1.5">Address line 2 <span class="font-normal text-neutral-500">(optional)</span></label>
                <input type="text" id="billing-address2" name="billing_address2" autocomplete="street-address-line2" placeholder="Apartment, suite, etc."
                    class="w-full rounded-lg border border-neutral-200 bg-white px-4 py-3.5 text-base text-neutral-900 placeholder:text-neutral-400 shadow-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-neutral-900 focus:border-transparent focus:shadow-[0_0_0_3px_rgba(0,0,0,0.08)]" />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="billing-city" class="block text-sm font-medium text-neutral-900 mb-1.5">City</label>
                    <input type="text" id="billing-city" name="billing_city" autocomplete="address-level2" placeholder="City"
                        class="w-full rounded-lg border border-neutral-200 bg-white px-4 py-3.5 text-base text-neutral-900 placeholder:text-neutral-400 shadow-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-neutral-900 focus:border-transparent focus:shadow-[0_0_0_3px_rgba(0,0,0,0.08)]"
                        required />
                </div>
                <div>
                    <label for="billing-state" class="block text-sm font-medium text-neutral-900 mb-1.5">State / Province</label>
                    <input type="text" id="billing-state" name="billing_state" autocomplete="address-level1" placeholder="State or province"
                        class="w-full rounded-lg border border-neutral-200 bg-white px-4 py-3.5 text-base text-neutral-900 placeholder:text-neutral-400 shadow-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-neutral-900 focus:border-transparent focus:shadow-[0_0_0_3px_rgba(0,0,0,0.08)]"
                        required />
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="billing-postal" class="block text-sm font-medium text-neutral-900 mb-1.5">Postal code</label>
                    <input type="text" id="billing-postal" name="billing_postal" autocomplete="postal-code" placeholder="ZIP / Postal code"
                        class="w-full rounded-lg border border-neutral-200 bg-white px-4 py-3.5 text-base text-neutral-900 placeholder:text-neutral-400 shadow-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-neutral-900 focus:border-transparent focus:shadow-[0_0_0_3px_rgba(0,0,0,0.08)]"
                        required />
                </div>
                <div>
                    <label for="billing-country" class="block text-sm font-medium text-neutral-900 mb-1.5">Country</label>
                    <select id="billing-country" name="billing_country" autocomplete="country-name"
                        class="w-full rounded-lg border border-neutral-200 bg-white px-4 py-3.5 text-base text-neutral-900 shadow-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-neutral-900 focus:border-transparent focus:shadow-[0_0_0_3px_rgba(0,0,0,0.08)]"
                        required>
                        <option value="US">United States</option>
                        <option value="KE">Kenya</option>
                        <option value="GB">United Kingdom</option>
                        <option value="CA">Canada</option>
                        <option value="AU">Australia</option>
                        <option value="OTHER">Other</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Message (Optional) -->
    <div>
        <label for="message" class="block text-sm font-medium text-neutral-900 mb-1.5">
            Message <span class="font-normal text-neutral-500">(optional)</span>
        </label>
        <textarea id="message" name="message" rows="3" placeholder="Add a note with your donation…"
            class="w-full rounded-lg border border-neutral-200 bg-white px-4 py-3.5 text-base text-neutral-900 placeholder:text-neutral-400 shadow-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-neutral-900 focus:border-transparent focus:shadow-[0_0_0_3px_rgba(0,0,0,0.08)] resize-none"></textarea>
    </div>

    <!-- Submit -->
    <button type="submit" id="donate-submit"
        class="w-full inline-flex justify-center items-center gap-2 rounded-lg bg-neutral-900 px-6 py-3.5 text-base font-semibold text-white shadow-sm transition-all duration-200 hover:bg-neutral-800 hover:shadow focus:outline-none focus:ring-2 focus:ring-neutral-900 focus:ring-offset-2 active:scale-[0.99] disabled:opacity-60 disabled:cursor-not-allowed disabled:active:scale-100">
        <svg class="w-5 h-5 shrink-0 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
        <span class="btn-text">Proceed to payment</span>
    </button>

    <p class="flex items-center justify-center gap-1.5 text-xs text-neutral-500">
        <svg class="w-3.5 h-3.5 shrink-0 text-neutral-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
        </svg>
        Secure donation · CyberSource Unified Checkout
    </p>
    </div>

    <!-- Step 2: CyberSource payment widget (shown after "Proceed to payment") -->
    <div id="donation-payment-step" class="hidden space-y-6">
        <p class="text-sm text-neutral-600">Complete your donation with card or digital wallet.</p>
        <p class="text-neutral-500 text-sm hidden" id="uc-loading-message">Loading payment options…</p>
        <div id="donation-payment-error" class="hidden rounded-lg border border-red-200 bg-red-50 p-4 text-red-800 text-sm"></div>
        <!-- Containers must be empty for CyberSource SDK to inject iframes -->
        <div id="buttonPaymentListContainer" class="min-h-[80px] rounded-lg border border-neutral-200 bg-neutral-50/50 p-4"></div>
        <div id="embeddedPaymentContainer" class="min-h-[280px] rounded-lg border border-neutral-200 bg-white"></div>
    </div>

    <!-- Step 3: Success -->
    <div id="donation-success-step" class="hidden rounded-lg border border-emerald-200 bg-emerald-50 p-6 text-center">
        <svg class="mx-auto h-12 w-12 text-emerald-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <p class="success-message text-lg font-medium text-emerald-900"></p>
    </div>

    <!-- Error state -->
    <div id="donation-error-step" class="hidden rounded-lg border border-red-200 bg-red-50 p-6 text-center">
        <p class="error-message text-red-800"></p>
    </div>

</form>
