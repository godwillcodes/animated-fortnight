<?php

get_header();
?>

<section class="shadow-sm">
  <div class="h-[500px] md:h-[400px] lg:h-[404px] relative"
    style="background-color: #0f6041;">
    <div class="absolute inset-0 flex items-end">
      <div class="container mx-auto w-full px-10 lg:px-0 pb-4 md:pb-12 lg:pb-12 text-white">
        <h1 class="text-2xl md:text-4xl lg:text-4xl font-bold" data-aos="fade-up" data-aos-delay="200">
          <?php the_title(); ?>
        </h1>
        <p class="text-base lg:text-lg my-4 max-w-4xl" data-aos="fade-up" data-aos-delay="400">
          Support our cause by purchasing limited-edition merchandise. Every item fuels the movement for justice and courage.
        </p>

        <a href="/shop" class="inline-flex items-center gap-2 bg-white text-[#0f6041] px-6 py-2.5 mt-4 text-base font-semibold rounded-full shadow-sm">
          Back to Shop
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </a>
       
      </div>
    </div>
  </div>
</section>
<div class="relative w-full h-20 overflow-hidden bg-gray-50">
    <div class="absolute inset-0 w-[200%] h-full bg-repeat-x bg-contain animate-marquee" 
         style="background-image: url('/wp-content/uploads/2025/08/BM-TERRACE-BRANDING-Final-1_page-0001_11zon-1-scaled.jpg');"></div>
</div>

<div class="bg-white">
  <div class="pt-6">
    <nav aria-label="Breadcrumb">
      <ol role="list" class="mx-auto flex max-w-2xl items-center space-x-2 px-4 sm:px-6 lg:container lg:px-0">
        <li>
          <div class="flex items-center">
            <a href="/shop" class="mr-2 text-sm font-medium text-gray-900 hover:text-emerald-700 transition-colors">Shop</a>
            <svg viewBox="0 0 16 20" width="16" height="20" fill="currentColor" aria-hidden="true" class="h-5 w-4 text-gray-300">
              <path d="M5.697 4.34L8.98 16.532h1.327L7.025 4.341H5.697z" />
            </svg>
          </div>
        </li>
        <li class="text-sm">
          <span aria-current="page" class="font-medium text-gray-500"><?php the_title(); ?></span>
        </li>
      </ol>
    </nav>

    <!-- Image gallery -->
    

    <!-- Image gallery -->
    <?php 
    $product_shots = get_field('product_shots');
    if ($product_shots): ?>
    <div class="mx-auto mt-6 max-w-2xl sm:px-6 lg:grid lg:container lg:px-0 lg:grid-cols-3 lg:gap-8">
      <?php 
      $image_count = count($product_shots);
      $image_classes = [
        0 => 'row-span-2 aspect-3/4 size-full rounded-2xl object-cover max-lg:hidden shadow-lg',
        1 => 'col-start-2 aspect-3/2 size-full rounded-2xl object-cover max-lg:hidden shadow-lg',
        2 => 'col-start-2 row-start-2 aspect-3/2 size-full rounded-2xl object-cover max-lg:hidden shadow-lg',
        3 => 'row-span-2 aspect-4/5 size-full object-cover rounded-2xl lg:aspect-3/4 shadow-lg'
      ];
      
      foreach ($product_shots as $index => $image): 
        $class = isset($image_classes[$index]) ? $image_classes[$index] : 'h-[300px] w-[300px] size-full rounded-2xl object-cover shadow-lg';
      ?>
        <img src="<?php echo esc_url($image['url']); ?>" 
             alt="<?php echo esc_attr($image['alt']); ?>" 
             class="<?php echo $class; ?>" />
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Product info -->
    <div class="mx-auto max-w-2xl px-4 pt-10 pb-16 lg:px-0 lg:grid lg:container lg:grid-cols-3 lg:grid-rows-[auto_auto_1fr] lg:gap-x-8  lg:pt-16 lg:pb-24">
      <div class="lg:col-span-2 lg:border-r lg:border-gray-200 lg:pr-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl"><?php the_title(); ?></h1>
      </div>

      <!-- Options -->
      <div class="mt-4 lg:row-span-3 lg:mt-0">
        <h2 class="sr-only">Product information</h2>
        <?php 
        $price = get_field('price');
        if ($price): ?>
          <p class="text-4xl font-bold tracking-tight text-gray-900">$. <?php echo esc_html($price); ?></p>
        <?php endif; ?>

        <!-- Payment Options -->
        <div class="mt-10" x-data="paymentOptions()">
         

          <!-- Payment Status Messages -->
          <div x-show="paymentStatus" 
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="opacity-0 transform scale-95"
               x-transition:enter-end="opacity-100 transform scale-100"
               class="mt-4 p-4 rounded-2xl border-2"
               :class="{
                 'bg-green-50 border-green-200': paymentStatus === 'success',
                 'bg-red-50 border-red-200': paymentStatus === 'error',
                 'bg-blue-50 border-blue-200': paymentStatus === 'info'
               }">
            <div class="flex items-start">
              <div class="flex-shrink-0">
                <svg x-show="paymentStatus === 'success'" class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <svg x-show="paymentStatus === 'error'" class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <svg x-show="paymentStatus === 'info'" class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium" 
                   :class="{
                     'text-green-800': paymentStatus === 'success',
                     'text-red-800': paymentStatus === 'error',
                     'text-blue-800': paymentStatus === 'info'
                   }"
                   x-text="paymentMessage"></p>
              </div>
            </div>
          </div>
        </div>

        <!-- Forminator PayPal Form (Hidden by default, shown when PayPal is selected and button clicked) -->
        <div x-bind:class="selectedMethod === 'paypal' && showForm ? 'block' : 'hidden'"
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             class="mt-8 bg-white border-2 border-[#001c64] rounded-3xl p-8 shadow-xl paypal-form-container">
          
          <!-- PayPal Branded Header -->
          

          <!-- PayPal Security Badge -->
          <div class="flex items-center justify-center mb-6 p-4 bg-blue-50 rounded-xl border border-blue-200">
            <svg class="w-6 h-6 text-[#001c64] mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            <div class="text-center">
              <p class="text-sm font-semibold text-[#001c64]">256-bit SSL Encryption</p>
              <p class="text-xs text-gray-600">Your payment information is secure</p>
            </div>
          </div>

          <!-- Form Content -->
          <div class="forminator-form-113 paypal-form-content">
            <?php echo do_shortcode('[forminator_form id="124"]'); ?>
          </div>

          <!-- PayPal Footer -->
          <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="flex items-center justify-center">
              <img src="https://www.paypalobjects.com/webstatic/mktg/logo/pp_cc_mark_37x23.jpg" 
                   alt="PayPal" 
                   class="h-6 w-auto mr-3">
              <span class="text-sm text-gray-600">Powered by PayPal</span>
            </div>
          </div>
        </div>

        <!-- Security Badge -->
        <div class="mt-8 flex items-center justify-center gap-3 text-sm text-gray-500">
          <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
          </svg>
          <span>Secure SSL encrypted payment</span>
        </div>
      </div>

      <!-- Product Details -->
      <div class="py-10 lg:col-span-2 lg:col-start-1 lg:border-r lg:border-gray-200 lg:pt-6 lg:pr-8 lg:pb-16">
        <div>
          <h3 class="text-2xl font-bold text-gray-900 mb-4">Description</h3>
          <div class="prose prose-gray max-w-none">
            <?php the_content(); ?>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<style>
[x-cloak] { display: none !important; }

/* PayPal Form Styling */
.paypal-form-container {
  border-color: #001c64 !important;
  box-shadow: 0 20px 25px -5px rgba(0, 28, 100, 0.1), 0 10px 10px -5px rgba(0, 28, 100, 0.04);
}

.paypal-form-content {
  background: #f8fafc;
  border-radius: 12px;
  padding: 24px;
  border: 1px solid #e2e8f0;
}

/* Forminator Form Styling for PayPal */
.paypal-form-content .forminator-form {
  background: transparent !important;
  border: none !important;
  padding: 0 !important;
}

.paypal-form-content .forminator-field {
  margin-bottom: 20px;
}

.paypal-form-content .forminator-label {
  color: #001c64 !important;
  font-weight: 600 !important;
  font-size: 14px !important;
  margin-bottom: 8px !important;
  display: block !important;
}

.paypal-form-content .forminator-input {
  width: 100% !important;
  padding: 12px 16px !important;
  border: 2px solid #e2e8f0 !important;
  border-radius: 8px !important;
  font-size: 16px !important;
  transition: all 0.3s ease !important;
  background: white !important;
}

.paypal-form-content .forminator-input:focus {
  border-color: #001c64 !important;
  box-shadow: 0 0 0 3px rgba(0, 28, 100, 0.1) !important;
  outline: none !important;
}

.paypal-form-content .forminator-textarea {
  width: 100% !important;
  padding: 12px 16px !important;
  border: 2px solid #e2e8f0 !important;
  border-radius: 8px !important;
  font-size: 16px !important;
  min-height: 100px !important;
  resize: vertical !important;
  transition: all 0.3s ease !important;
  background: white !important;
}

.paypal-form-content .forminator-textarea:focus {
  border-color: #001c64 !important;
  box-shadow: 0 0 0 3px rgba(0, 28, 100, 0.1) !important;
  outline: none !important;
}

.paypal-form-content .forminator-select {
  width: 100% !important;
  padding: 12px 16px !important;
  border: 2px solid #e2e8f0 !important;
  border-radius: 8px !important;
  font-size: 16px !important;
  background: white !important;
  cursor: pointer !important;
  transition: all 0.3s ease !important;
}

.paypal-form-content .forminator-select:focus {
  border-color: #001c64 !important;
  box-shadow: 0 0 0 3px rgba(0, 28, 100, 0.1) !important;
  outline: none !important;
}

.paypal-form-content .forminator-checkbox,
.paypal-form-content .forminator-radio {
  accent-color: #001c64 !important;
  margin-right: 8px !important;
}

.paypal-form-content .forminator-checkbox-label,
.paypal-form-content .forminator-radio-label {
  color: #374151 !important;
  font-size: 14px !important;
  cursor: pointer !important;
}

.paypal-form-content .forminator-button {
  background: linear-gradient(135deg, #001c64 0%, #003087 100%) !important;
  color: white !important;
  border: none !important;
  padding: 16px 32px !important;
  border-radius: 8px !important;
  font-size: 16px !important;
  font-weight: 600 !important;
  cursor: pointer !important;
  transition: all 0.3s ease !important;
  width: 100% !important;
  text-transform: uppercase !important;
  letter-spacing: 0.5px !important;
}

.paypal-form-content .forminator-button:hover {
  background: linear-gradient(135deg, #003087 0%, #001c64 100%) !important;
  transform: translateY(-2px) !important;
  box-shadow: 0 10px 20px rgba(0, 28, 100, 0.3) !important;
}

.paypal-form-content .forminator-button:active {
  transform: translateY(0) !important;
}

.paypal-form-content .forminator-error {
  color: #dc2626 !important;
  font-size: 12px !important;
  margin-top: 4px !important;
  display: block !important;
}

.paypal-form-content .forminator-success {
  color: #059669 !important;
  font-size: 12px !important;
  margin-top: 4px !important;
  display: block !important;
}

/* PayPal Brand Colors */
.paypal-blue {
  color: #001c64 !important;
}

.paypal-blue-bg {
  background-color: #001c64 !important;
}

.paypal-blue-border {
  border-color: #001c64 !important;
}

/* Form Field Groups */
.paypal-form-content .forminator-row {
  display: flex !important;
  gap: 16px !important;
  margin-bottom: 20px !important;
}

.paypal-form-content .forminator-col {
  flex: 1 !important;
}

/* Loading States */
.paypal-form-content .forminator-loading {
  opacity: 0.6 !important;
  pointer-events: none !important;
}

.paypal-form-content .forminator-spinner {
  border: 2px solid #e2e8f0 !important;
  border-top: 2px solid #001c64 !important;
  border-radius: 50% !important;
  width: 20px !important;
  height: 20px !important;
  animation: spin 1s linear infinite !important;
  display: inline-block !important;
  margin-right: 8px !important;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 768px) {
  .paypal-form-content .forminator-row {
    flex-direction: column !important;
    gap: 0 !important;
  }
  
  .paypal-form-content {
    padding: 16px !important;
  }
}
</style>

<script>
function paymentOptions() {
  return {
    selectedMethod: 'paypal',
    showForm: false,
    paymentStatus: null,
    paymentMessage: '',
    isLoading: false,

    init() {
      // Start with PayPal selected
      this.selectedMethod = 'paypal';
      this.showForm = false;
    },

    handleContinue() {
      // Show the PayPal form
      this.showForm = true;
      this.paymentStatus = 'success';
      this.paymentMessage = 'Complete the form below to proceed with PayPal payment';
      
      // Reinitialize Forminator when form is revealed
      this.$nextTick(() => {
        if (typeof window.FORMINATOR !== 'undefined' && window.FORMINATOR.init) {
          window.FORMINATOR.init();
        }
        
        // Smooth scroll to form
        setTimeout(() => {
          const formElement = document.querySelector('.forminator-form-113');
          if (formElement) {
            formElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        }, 100);
      });
    }
  }
}
</script>

<?php get_footer(); ?>