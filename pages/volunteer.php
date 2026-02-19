<?php
/**
 * Template Name: Volunteer
 * Description: Campaign About Us Page with Clean Typography
 */
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
                    Join our volunteer network and play a vital role in mobilizing communities, educating citizens, and safeguarding the electoral process.
                </p>
               

            </div>
        </div>
    </div>
</section>
<div class="relative w-full h-20 overflow-hidden bg-gray-50">
    <div class="absolute inset-0 w-[200%] h-full bg-repeat-x bg-contain animate-marquee" 
         style="background-image: url('/wp-content/uploads/2025/08/BM-TERRACE-BRANDING-Final-1_page-0001_11zon-1-scaled.jpg');"></div>
</div>



<!-- Beautiful Form Section -->
<section class="py-20 bg-gradient-to-br from-[#F9F9F7] to-[#F0F0F0] text-[#1F3131]">
  <div class="container mx-auto px-6 lg:px-0">
    <div class="max-w-5xl mx-auto">
      <!-- Form Container -->
      <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <!-- Form Header -->
        <div class="bg-gradient-to-r from-[#0f6041] to-[#0a4a32] px-8 py-6">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mr-4">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-2xl font-bold text-white">Join Our Volunteer Network</h3>
              <p class="text-white/90 text-sm">Be part of the movement for change and justice</p>
            </div>
          </div>
        </div>

        <!-- Form Content -->
        <div class="p-8">
          <div class="forminator-form-wrapper">
            <?php echo do_shortcode('[forminator_form id="20"]'); ?>
          </div>
        </div>

        <!-- Form Footer -->
        <div class="bg-gray-50 px-8 py-6 border-t border-gray-200">
          <div class="flex items-center justify-between">
            <div class="flex items-center text-sm text-gray-600">
              <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <span>Your information is secure and will only be used for volunteer coordination</span>
            </div>
            <div class="text-sm text-gray-500">
              <span class="font-medium">Response time:</span> 24-48 hours
            </div>
          </div>
        </div>
      </div>

      <!-- Additional Info Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-16">
        <div class="bg-white p-6 rounded-xl shadow-lg text-center">
          <div class="w-16 h-16 bg-[#0f6041]/10 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-[#0f6041]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <h4 class="text-lg font-bold mb-2 text-[#0f6041]">Quick Response</h4>
          <p class="text-gray-600 text-sm">We'll get back to you within 24-48 hours to discuss volunteer opportunities and next steps.</p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-lg text-center">
          <div class="w-16 h-16 bg-[#0f6041]/10 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-[#0f6041]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
          </div>
          <h4 class="text-lg font-bold mb-2 text-[#0f6041]">Community Impact</h4>
          <p class="text-gray-600 text-sm">Make a real difference in your community through meaningful volunteer work and civic engagement.</p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-lg text-center">
          <div class="w-16 h-16 bg-[#0f6041]/10 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-[#0f6041]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
          </div>
          <h4 class="text-lg font-bold mb-2 text-[#0f6041]">Safe & Secure</h4>
          <p class="text-gray-600 text-sm">All information is protected and used solely for volunteer coordination and communication purposes.</p>
        </div>
      </div>
    </div>
  </div>
</section>


<?php
get_footer(); 
?>