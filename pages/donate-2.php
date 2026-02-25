<?php
/**
 * Template Name: Donate 2
 * Description: 
 */
get_header();
?>
<section class="donate-hero relative overflow-hidden" style="background: linear-gradient(160deg, #0a4e34 0%, #0f6041 40%, #1a7a54 70%, #0f6041 100%);">
  <div class="absolute inset-0 opacity-[0.04]" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
  <div class="relative max-w-7xl mx-auto px-6 lg:px-0 py-20 md:py-28 lg:py-32">
    <div class="max-w-3xl">
      <div class="inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur-sm px-4 py-1.5 mb-6" data-aos="fade-up" data-aos-delay="100">
        <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
        <span class="text-sm font-medium text-white/90 tracking-wide">Accepting donations</span>
      </div>
      <h1 class="text-3xl md:text-5xl lg:text-6xl font-bold text-white leading-tight tracking-tight mb-6" data-aos="fade-up" data-aos-delay="200">
        Dear friends of<br class="hidden sm:block"> Boniface Mwangi
      </h1>
      <p class="text-base md:text-lg text-white/80 leading-relaxed max-w-2xl mb-8" data-aos="fade-up" data-aos-delay="400">
        We thank you so much for your consistent support, and look forward to collaborating
        in the co-creation of the Kenya we want and deserve. Your donations towards our journey
        ahead will be extremely appreciated.
      </p>
      <div class="flex flex-wrap gap-4" data-aos="fade-up" data-aos-delay="500">
        <a href="#donation-form"
          class="group inline-flex items-center gap-2 rounded-xl bg-white px-6 py-3.5 text-base font-bold shadow-lg shadow-black/10 transition-all duration-200 ease-out hover:shadow-xl hover:-translate-y-0.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 active:scale-[0.98]"
          style="color: #0f6041;">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
          </svg>
          <span>Donate now</span>
        </a>
        <a href="/volunteer"
          class="group inline-flex items-center gap-2 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 px-6 py-3.5 text-base font-semibold text-white transition-all duration-200 ease-out hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 active:scale-[0.98]">
          <span>I want to help</span>
          <svg class="h-4 w-4 transition-transform duration-200 ease-out group-hover:translate-x-1" fill="none"
            stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            aria-hidden="true">
            <path d="M5 12h14M13 5l7 7-7 7" />
          </svg>
        </a>
      </div>
    </div>
    <div class="hidden lg:flex absolute right-12 bottom-0 opacity-[0.06]">
      <svg class="w-[400px] h-[400px]" viewBox="0 0 200 200" fill="white">
        <path d="M100 20c-15 0-28 8-35 20C58 28 45 20 30 20 13.5 20 0 33.5 0 50c0 55 100 130 100 130s100-75 100-130c0-16.5-13.5-30-30-30-15 0-28 8-35 20z"/>
      </svg>
    </div>
  </div>
</section>
<div class="relative w-full h-16 overflow-hidden bg-neutral-50">
  <div class="absolute inset-0 w-[200%] h-full bg-repeat-x bg-contain animate-marquee"
    style="background-image: url('/wp-content/uploads/2025/08/BM-TERRACE-BRANDING-Final-1_page-0001_11zon-1-scaled.jpg');">
  </div>
</div>

<section class="max-w-7xl mx-auto my-24 px-6 lg:px-0 space-y-20">

  <!-- DONATION FORM (primary CTA — comes first) -->
  <div id="donation-form" class="max-w-5xl mx-auto scroll-mt-8">
    <div class="donate-card rounded-3xl shadow-xl border border-neutral-200/60 bg-white overflow-hidden">
      <?php $donation_stats = boniface_donation_stats(); ?>
      <div class="donate-card-header px-8 pt-10 pb-8 text-center" style="background: linear-gradient(135deg, #0f6041 0%, #1a7a54 50%, #0f6041 100%);">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-white/15 backdrop-blur-sm mb-5">
          <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
          </svg>
        </div>
        <h2 class="text-3xl font-bold text-white tracking-tight">Support the Work</h2>
        <p class="mt-2 text-white/80 text-base">Every contribution fuels the movement for change.</p>

        <!-- Donation progress stats -->
        <div class="mt-8 pt-6 border-t border-white/15 flex items-center justify-center gap-8">
          <div>
            <p class="text-2xl md:text-3xl font-extrabold text-white tracking-tight leading-none">
              <?php echo esc_html( $donation_stats['total_formatted'] ); ?>
            </p>
            <p class="text-xs text-white/60 mt-1 font-medium">raised</p>
          </div>
          <div class="w-px h-10 bg-white/15"></div>
          <div>
            <p class="text-2xl md:text-3xl font-extrabold text-white tracking-tight leading-none">
              <?php echo esc_html( number_format( $donation_stats['count'] ) ); ?>
            </p>
            <p class="text-xs text-white/60 mt-1 font-medium">donation<?php echo $donation_stats['count'] !== 1 ? 's' : ''; ?></p>
          </div>
        </div>
      </div>
      <div class="px-8 py-10">
        <?php get_template_part('components/payment/payment', 'form'); ?>
      </div>
    </div>
  </div>

  <!-- DIVIDER -->
  <div class="flex items-center gap-6">
    <div class="flex-1 h-px bg-neutral-200"></div>
    <span class="text-sm font-semibold text-neutral-400 uppercase tracking-widest">Other ways to give</span>
    <div class="flex-1 h-px bg-neutral-200"></div>
  </div>

  <!-- ALTERNATIVE PAYMENT INFO CARDS -->
  <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">

    <!-- ABSA -->
    <div class="bg-white border border-neutral-200 rounded-2xl shadow-sm p-8 hover:shadow-md transition-shadow duration-300">
      <h3 class="text-xl font-bold text-neutral-900 flex items-center mb-6">
        <img 
          src="https://www.absabank.co.ke/content/dam/kenya/absa/logos/absa-logo-bg.png" 
          alt="ABSA Bank Kenya" 
          class="h-8 w-auto mr-3"
        >
        Bank Transfer (ABSA)
      </h3>

      <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm text-neutral-700">
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
    <div class="border border-neutral-200 rounded-2xl shadow-sm p-8 hover:shadow-md transition-shadow duration-300" style="background-color: #f0faf4;">
      <h3 class="text-xl font-bold flex items-center mb-6" style="color: #0f6041;">
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
          <dd class="text-lg font-bold text-neutral-900">5010215</dd>
        </div>
        <div>
          <dt class="font-semibold">Send Money</dt>
          <dd>Boniface Mwangi — <span class="font-bold">0792788638</span></dd>
        </div>
      </dl>

      <p class="mt-6 text-xs text-neutral-500">
        Compatible with international remittance platforms supporting Kenyan mobile transfers.
      </p>
    </div>

    <!-- Contact -->
    <div class="bg-white border border-neutral-200 rounded-2xl shadow-sm p-8 hover:shadow-md transition-shadow duration-300">
      <h3 class="text-xl font-bold text-neutral-900 mb-6">
        Contact & Support
      </h3>

      <div class="space-y-4 text-sm text-neutral-700">
        <p>
          <span class="font-semibold text-neutral-900">Tel:</span><br>
          +254 117 777 111
        </p>
        <p>
          <span class="font-semibold text-neutral-900">Email:</span><br>
          <a href="mailto:hello@bonifacemwangi.com" class="hover:underline" style="color: #0f6041;">
            hello@bonifacemwangi.com
          </a>
        </p>
        <p>
          <span class="font-semibold text-neutral-900">Website:</span><br>
          <a href="https://bonifacemwangi.com" class="font-medium hover:underline" style="color: #0f6041;">
            bonifacemwangi.com
          </a>
        </p>
      </div>
    </div>

  </div>

</section>

<?php
get_footer();
?>
