<form class="payment-form relative" id="donation-form">

    <!-- Progress: 1 — 2 -->
    <div class="flex items-center justify-center gap-3 mb-10" aria-label="Progress">
        <div class="donation-progress-dot active flex items-center justify-center">
            <span class="donation-progress-num" data-step="1">1</span>
        </div>
        <span class="w-16 h-0.5 bg-neutral-200 rounded-full donation-progress-line" aria-hidden="true"></span>
        <div class="donation-progress-dot flex items-center justify-center">
            <span class="donation-progress-num" data-step="2">2</span>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- Step 1: Amount + Your details                                -->
    <!-- ============================================================ -->
    <div id="donation-details-step" class="donation-form-step donation-step-visible space-y-7">
        <!-- Amount -->
        <div>
            <label class="block text-sm font-bold text-neutral-800 tracking-tight mb-3">
                Choose amount
            </label>
            <div class="grid grid-cols-3 sm:grid-cols-6 gap-2 mb-3">
                <?php
                $presets = [ 10, 25, 50, 100, 250, 500 ];
                foreach ( $presets as $p ) :
                ?>
                <button type="button" data-amount="<?php echo (int) $p; ?>"
                    class="preset-amount h-12 rounded-xl border-2 border-neutral-200 bg-white text-sm font-semibold text-neutral-700 transition-all duration-200 hover:border-neutral-400 hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-offset-2" style="--tw-ring-color: #0f6041;" aria-pressed="false">
                    $<?php echo (int) $p; ?>
                </button>
                <?php endforeach; ?>
            </div>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 font-semibold text-lg pointer-events-none">$</span>
                <input type="number" id="amount" name="amount" min="10" step="1" value="25" placeholder="0"
                    class="donation-input w-full rounded-xl border-2 border-neutral-200 bg-neutral-50 pl-9 pr-4 py-4 text-lg font-semibold text-neutral-900 placeholder:text-neutral-400 transition-all duration-200 focus:outline-none focus:bg-white focus:border-2"
                    style="--focus-border: #0f6041;"
                    required />
            </div>
            <p class="mt-2 text-xs text-neutral-500">Minimum $10 USD. Secure payment via card or digital wallet.</p>
        </div>

        <div class="border-t border-neutral-100"></div>

        <!-- Your details -->
        <div>
            <h3 class="text-sm font-bold text-neutral-800 tracking-tight mb-4">Your details</h3>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-neutral-600 mb-1.5">Full name</label>
                    <input type="text" id="name" name="name" autocomplete="name" placeholder="John Doe"
                        class="donation-input w-full rounded-xl border-2 border-neutral-200 bg-neutral-50 px-4 py-3.5 text-neutral-900 placeholder:text-neutral-400 transition-all duration-200 focus:outline-none focus:bg-white"
                        required />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-neutral-600 mb-1.5">Email address</label>
                        <input type="email" id="email" name="email" autocomplete="email" placeholder="you@example.com"
                            class="donation-input w-full rounded-xl border-2 border-neutral-200 bg-neutral-50 px-4 py-3.5 text-neutral-900 placeholder:text-neutral-400 transition-all duration-200 focus:outline-none focus:bg-white"
                            required />
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-neutral-600 mb-1.5">Phone number</label>
                        <input type="tel" id="phone" name="phone" autocomplete="tel" placeholder="+254 7XX XXX XXX"
                            class="donation-input w-full rounded-xl border-2 border-neutral-200 bg-neutral-50 px-4 py-3.5 text-neutral-900 placeholder:text-neutral-400 transition-all duration-200 focus:outline-none focus:bg-white"
                            required />
                    </div>
                </div>
            </div>
        </div>

        <!-- Message -->
        <div>
            <label for="message" class="block text-sm font-medium text-neutral-600 mb-1.5">Message <span class="font-normal text-neutral-400">(optional)</span></label>
            <textarea id="message" name="message" rows="2" placeholder="Add a note with your donation…"
                class="donation-input w-full rounded-xl border-2 border-neutral-200 bg-neutral-50 px-4 py-3.5 text-neutral-900 placeholder:text-neutral-400 transition-all duration-200 focus:outline-none focus:bg-white resize-none"></textarea>
        </div>

        <!-- Submit to payment -->
        <div class="pt-1">
            <button type="submit" id="donate-submit"
                class="donate-submit-btn button-kenya w-full inline-flex justify-center items-center gap-3 rounded-xl px-6 py-4 text-base font-bold text-white transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 active:scale-[0.99] disabled:opacity-70 disabled:cursor-not-allowed"
                style="--tw-ring-color: #0f6041;">
                <span class="donate-btn-spinner" aria-hidden="true"></span>
                <svg class="donate-btn-icon w-5 h-5 shrink-0 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <span class="donate-btn-text">Continue to payment</span>
            </button>
            <div class="mt-5 flex items-center justify-center gap-3">
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 shrink-0 text-neutral-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-xs text-neutral-400">256-bit SSL</span>
                </div>
                <span class="text-neutral-300">·</span>
                <span class="text-xs text-neutral-400">CyberSource Unified Checkout</span>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- Step 2: Payment (CyberSource widget — billing + card + pay)  -->
    <!-- ============================================================ -->
    <div id="donation-payment-step" class="donation-form-step donation-step-hidden hidden space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-base font-semibold text-neutral-900">Complete your donation</p>
                <p class="text-sm text-neutral-500 mt-0.5">Billing and card details handled securely by CyberSource.</p>
            </div>
            <button type="button" id="donation-back-to-details" class="text-sm font-medium text-neutral-500 hover:text-neutral-900 transition-colors flex items-center gap-1.5 shrink-0" aria-label="Back to details">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                Back
            </button>
        </div>
        <div id="donation-payment-error" class="hidden rounded-xl border-2 border-red-200 bg-red-50 p-4 text-red-800 text-sm font-medium"></div>
        <div class="relative min-h-[360px]">
            <div id="donation-payment-skeleton" class="absolute inset-0 z-10 flex flex-col gap-4 pointer-events-none">
                <div class="donation-skeleton h-20 w-full flex-0"></div>
                <div class="donation-skeleton h-72 w-full flex-1 min-h-[280px]"></div>
            </div>
            <div class="relative z-0 space-y-4">
                <div id="buttonPaymentListContainer" class="min-h-[80px] rounded-xl border-2 border-neutral-200 bg-neutral-50/50 p-4"></div>
                <div id="embeddedPaymentContainer" class="min-h-[280px] rounded-xl border-2 border-neutral-200 bg-white"></div>
            </div>
        </div>
    </div>

    <!-- Success -->
    <div id="donation-success-step" class="donation-form-step donation-step-hidden hidden text-center py-10 px-6 rounded-2xl" style="background: linear-gradient(to bottom, #f0faf4, #ffffff); border: 2px solid #bbf7d0;">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-6" style="background-color: #dcfce7;">
            <svg class="w-8 h-8 donation-success-check" style="color: #0f6041;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <p class="success-message text-xl font-bold text-neutral-900 mb-2"></p>
        <p class="text-sm text-neutral-600">A confirmation email has been sent to you.</p>
    </div>

    <!-- Error -->
    <div id="donation-error-step" class="donation-form-step donation-step-hidden hidden text-center py-10 px-6 rounded-2xl border-2 border-red-200 bg-red-50">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 mb-6">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <p class="error-message text-lg font-semibold text-red-900 mb-4"></p>
        <button type="button" id="donation-retry-btn" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-white transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2" style="background-color: #0f6041; --tw-ring-color: #0f6041;">Try again</button>
    </div>

</form>
