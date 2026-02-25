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
      <div class="mt-4 lg:row-span-3 lg:mt-0 border border-gray-500 p-10">
        <h2 class="sr-only">Product information</h2>
        <?php
        $price = get_field('price');
        $price_float = $price ? (float) $price : 0;
        ?>
        <?php if ( $price_float > 0 ) : ?>
          <p class="text-4xl font-bold tracking-tight text-gray-900">$<?php echo esc_html( number_format( $price_float, 2 ) ); ?></p>

        <!-- CyberSource Checkout -->
        <form id="product-checkout-form" class="mt-8 space-y-6">
          <input type="hidden" id="product-price" value="<?php echo esc_attr( $price_float ); ?>">
          <input type="hidden" id="product-title" value="<?php echo esc_attr( get_the_title() ); ?>">

          <!-- Buyer details -->
          <div class="space-y-4">
            <h3 class="text-sm font-bold text-neutral-800 tracking-tight">Your details</h3>
            <div>
              <label for="buyer-name" class="block text-sm font-medium text-neutral-600 mb-1.5">Full name</label>
              <input type="text" id="buyer-name" name="name" autocomplete="name" placeholder="John Doe"
                class="donation-input w-full rounded-xl border-2 border-neutral-200 bg-neutral-50 px-4 py-3 text-neutral-900 placeholder:text-neutral-400 transition-all duration-200 focus:outline-none focus:bg-white"
                required />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="buyer-email" class="block text-sm font-medium text-neutral-600 mb-1.5">Email</label>
                <input type="email" id="buyer-email" name="email" autocomplete="email" placeholder="you@example.com"
                  class="donation-input w-full rounded-xl border-2 border-neutral-200 bg-neutral-50 px-4 py-3 text-neutral-900 placeholder:text-neutral-400 transition-all duration-200 focus:outline-none focus:bg-white"
                  required />
              </div>
              <div>
                <label for="buyer-phone" class="block text-sm font-medium text-neutral-600 mb-1.5">Phone</label>
                <input type="tel" id="buyer-phone" name="phone" autocomplete="tel" placeholder="+254 7XX XXX XXX"
                  class="donation-input w-full rounded-xl border-2 border-neutral-200 bg-neutral-50 px-4 py-3 text-neutral-900 placeholder:text-neutral-400 transition-all duration-200 focus:outline-none focus:bg-white"
                  required />
              </div>
            </div>
          </div>

          <!-- Submit -->
          <button type="submit" id="product-buy-btn"
            class="button-kenya w-full inline-flex justify-center items-center gap-3 rounded-xl px-6 py-4 text-base font-bold text-white transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 active:scale-[0.99] disabled:opacity-70 disabled:cursor-not-allowed"
            style="--tw-ring-color: #0f6041;">
            <span class="donate-btn-spinner" aria-hidden="true"></span>
            <svg class="donate-btn-icon w-5 h-5 shrink-0 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
            </svg>
            <span class="donate-btn-text">Buy now — $<?php echo esc_html( number_format( $price_float, 2 ) ); ?></span>
          </button>

          <div id="product-form-error" class="hidden rounded-xl border-2 border-red-200 bg-red-50 p-4 text-red-800 text-sm font-medium"></div>
        </form>

        <!-- Payment step (hidden until buyer clicks Buy) -->
        <div id="product-payment-step" class="hidden mt-6 space-y-4">
          <div class="flex items-center justify-between gap-4">
            <div>
              <p class="text-base font-semibold text-neutral-900">Complete your purchase</p>
              <p class="text-sm text-neutral-500 mt-0.5">Card details handled securely by CyberSource.</p>
            </div>
            <button type="button" id="product-back-btn" class="text-sm font-medium text-neutral-500 hover:text-neutral-900 transition-colors flex items-center gap-1.5 shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
              Back
            </button>
          </div>
          <div id="product-payment-error" class="hidden rounded-xl border-2 border-red-200 bg-red-50 p-4 text-red-800 text-sm font-medium"></div>
          <div class="relative min-h-[360px]">
            <div id="product-payment-skeleton" class="absolute inset-0 z-10 flex flex-col gap-4 pointer-events-none">
              <div class="donation-skeleton h-20 w-full flex-0"></div>
              <div class="donation-skeleton h-72 w-full flex-1 min-h-[280px]"></div>
            </div>
            <div class="relative z-0 space-y-4">
              <div id="productPaymentListContainer" class="min-h-[80px] rounded-xl border-2 border-neutral-200 bg-neutral-50/50 p-4"></div>
              <div id="productPaymentContainer" class="min-h-[280px] rounded-xl border-2 border-neutral-200 bg-white"></div>
            </div>
          </div>
        </div>

        <!-- Success -->
        <div id="product-success-step" class="hidden mt-6 text-center py-10 px-6 rounded-2xl" style="background: linear-gradient(to bottom, #f0faf4, #ffffff); border: 2px solid #bbf7d0;">
          <div class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-6" style="background-color: #dcfce7;">
            <svg class="w-8 h-8 donation-success-check" style="color: #0f6041;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <p class="product-success-msg text-xl font-bold text-neutral-900 mb-2"></p>
          <p class="text-sm text-neutral-600">A confirmation email will be sent to you.</p>
        </div>

        <!-- Error -->
        <div id="product-error-step" class="hidden mt-6 text-center py-10 px-6 rounded-2xl border-2 border-red-200 bg-red-50">
          <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 mb-6">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <p class="product-error-msg text-lg font-semibold text-red-900 mb-4"></p>
          <button type="button" id="product-retry-btn" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-white transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2" style="background-color: #0f6041; --tw-ring-color: #0f6041;">Try again</button>
        </div>

        <!-- Security -->
        <div class="mt-6 flex items-center justify-center gap-3">
          <div class="flex items-center gap-1.5">
            <svg class="w-4 h-4 shrink-0 text-neutral-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
              <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
            </svg>
            <span class="text-xs text-neutral-400">256-bit SSL</span>
          </div>
          <span class="text-neutral-300">&middot;</span>
          <span class="text-xs text-neutral-400">CyberSource Unified Checkout</span>
        </div>

        <?php else : ?>
          <p class="mt-4 text-lg text-neutral-500">Price not available. Please contact us for details.</p>
        <?php endif; ?>
      </div>

      <!-- Product Details -->
      <div class="py-10 lg:col-span-2 lg:col-start-1 lg:border-r lg:border-gray-200 lg:pt-6 lg:pr-8 lg:pb-16">
        <div>
          <h3 class="text-2xl font-bold text-gray-900 mb-4">Description</h3>
          <div class="prose text-lg prose-gray max-w-none">
            <?php the_content(); ?>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>


<?php get_footer(); ?>