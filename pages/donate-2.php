<?php
/**
 * Template Name: Donate 2
 * Description: 
 */
get_header();
// get_template_part( 'components/banner/primary' );
?>
<section class="shadow-sm">
  <div class="h-[500px] md:h-[400px] lg:h-[554px] relative" style="background-color: #0f6041;">
    <div class="absolute inset-0 flex items-end">
      <div class="container mx-auto w-full px-10 lg:px-0 pb-4 md:pb-12 lg:pb-12 text-white">
        <h1 class="text-2xl md:text-4xl lg:text-4xl mb-4 font-bold" data-aos="fade-up" data-aos-delay="200">
          Dear friends of Boniface Mwangi
        </h1>
        <p class="text-base lg:text-lg my-4 max-w-4xl" data-aos="fade-up" data-aos-delay="400">
          We thank you so much for your consistent support, and look forward to collaborating
          in the co-creation of the Kenya we want and deserve. Your donations towards our journey
          ahead will be extremely appreciated, and we also look forward to hearing your ideas
          as to the Kenya you dream of, and how we can work together to bring this dream into a reality.
        </p>
        <a href="/volunteer"
          class="group inline-flex items-center gap-2 rounded-lg bg-neutral-900 px-6 py-3 text-base font-semibold text-white shadow-sm transition-all duration-200 ease-out hover:bg-neutral-800 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-500 focus-visible:ring-offset-2 active:scale-[0.98]"
          aria-label="Volunteer with us">
          <span>I want to help</span>
          <svg class="h-4 w-4 transition-transform duration-200 ease-out group-hover:translate-x-1" fill="none"
            stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            aria-hidden="true">
            <path d="M5 12h14M13 5l7 7-7 7" />
          </svg>
        </a>

      </div>
    </div>
  </div>
</section>
<div class="relative w-full h-20 overflow-hidden bg-gray-50">
  <div class="absolute inset-0 w-[200%] h-full bg-repeat-x bg-contain animate-marquee"
    style="background-image: url('/wp-content/uploads/2025/08/BM-TERRACE-BRANDING-Final-1_page-0001_11zon-1-scaled.jpg');">
  </div>
</div>

<section class="max-w-6xl mx-auto my-24 px-6">
  <div class="grid lg:grid-cols-2 gap-12 items-start">

    <!-- LEFT: Donation Form -->
    <div class="bg-white border border-neutral-200 rounded-2xl shadow-sm p-8">
      <header class="mb-8">
        <h2 class="text-2xl font-bold text-neutral-900">Support the Work</h2>
        <p class="mt-2 text-neutral-600">
          Complete the form and proceed with your preferred payment method.
        </p>
      </header>

      <?php get_template_part('components/payment/payment', 'form'); ?>
      </div>

    <!-- RIGHT: Payment Details -->
    <div class="space-y-8">

      <!-- ABSA -->
      <div class="bg-white border border-neutral-200 rounded-2xl shadow-sm p-8">
        <h3 class="text-xl font-bold text-neutral-900 flex items-center mb-6">
          <img 
            src="https://www.absabank.co.ke/content/dam/kenya/absa/logos/absa-logo-bg.png" 
            alt="ABSA Bank Kenya" 
            class="h-8 w-auto mr-3"
          >
          Bank Transfer (ABSA)
        </h3>

        <dl class="grid sm:grid-cols-2 gap-x-8 gap-y-4 text-neutral-700 text-sm">
          <div>
            <dt class="font-semibold text-neutral-900">Account Name</dt>
            <dd>BONIFACE MWANGI MWANZO MPYA</dd>
          </div>
          <div>
            <dt class="font-semibold text-neutral-900">Account Number</dt>
            <dd>2053955064</dd>
          </div>
          <div>
            <dt class="font-semibold text-neutral-900">Bank Code</dt>
            <dd>03</dd>
          </div>
          <div>
            <dt class="font-semibold text-neutral-900">Branch</dt>
            <dd>Yaya (109)</dd>
          </div>
          <div>
            <dt class="font-semibold text-neutral-900">Sort Code</dt>
            <dd>03109</dd>
          </div>
          <div>
            <dt class="font-semibold text-neutral-900">SWIFT</dt>
            <dd>BARCKENX</dd>
          </div>
        </dl>
      </div>

      <!-- MPESA -->
      <div class="bg-emerald-50 border border-emerald-200 rounded-2xl shadow-sm p-8">
        <h3 class="text-xl font-bold text-emerald-800 flex items-center mb-6">
          <img 
            src="https://www.m-pesa.africa/images/mpesa-logo.png" 
            alt="M-PESA Kenya" 
            class="h-8 w-auto mr-3"
          >
          M-PESA (Buy Goods)
        </h3>

        <dl class="space-y-4 text-sm text-neutral-800">
          <div>
            <dt class="font-semibold">Till Number</dt>
            <dd>5010215</dd>
          </div>
          <div>
            <dt class="font-semibold">Send Money</dt>
            <dd>Boniface Mwangi — 0792788638</dd>
          </div>
        </dl>

        <p class="mt-6 text-xs text-neutral-600">
          Compatible with international remittance platforms supporting Kenyan mobile transfers.
        </p>
      </div>

      <!-- Contact -->
      <div class="text-sm text-neutral-600">
        <p>
          Tel: <span class="font-medium text-neutral-900">+254 117 777 111</span>
        </p>
        <p>
          <a href="mailto:hello@bonifacemwangi.com" class="hover:underline">
            hello@bonifacemwangi.com
          </a>
        </p>
        <p>
          <a href="https://bonifacemwangi.com" class="text-emerald-700 font-medium hover:underline">
            bonifacemwangi.com
          </a>
        </p>
      </div>

    </div>
  </div>
</section>


<?php
// get_template_part( 'components/common/cta' ); 
get_footer();
?>