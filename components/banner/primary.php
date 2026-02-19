<section  class="relative bg-[linear-gradient(to_bottom,_#1F3131_85%,_#006155_100%)] h-auto lg:h-[80vh] w-full text-white overflow-hidden">
    <!-- Navigation -->
    <?php get_template_part('components/navigation/desktop'); ?>
    <?php get_template_part('components/navigation/mobile'); ?>

    <div class="w-full pt-[10%] px-6 md:px-12 relative z-20">
        <div class="container mx-auto grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
            <!-- Left Column -->
            <div class="md:col-span-7 space-y-6 order-1">
                <div class="text-3xl md:text-5xl text-white leading-[40px] md:leading-[50px] font-semibold" data-aos="fade-up"
                    data-aos-delay="200"
                    data-aos-duration="1000">
                   test
                </div>
                <div class="text-xl leading-relaxed font-normal" data-aos="fade-up"
                    data-aos-delay="400"
                    data-aos-duration="1000">
                    test
                </div>

                <div class="flex flex-wrap items-center gap-7" data-aos="fade-up"
                    data-aos-delay="600"
                    data-aos-duration="1000">
                  
                </div>
            </div>

            <!-- Right Column Image -->
            <div class="md:col-span-5 order-2 flex justify-center md:justify-end">
                <img src="<?php echo esc_url(get_field('banner_image')); ?>"
                     alt="<?php echo esc_attr(get_bloginfo('name')); ?>"
                     class="w-full max-w-[440px] h-auto shadow-md md:absolute md:bottom-[-50%] md:right-0 md:z-20">
            </div>
        </div>
    </div>

    <!-- Bottom Pattern -->
    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/icons/pattern-1.svg'); ?>"
         alt="<?php echo esc_attr(get_bloginfo('name')); ?>"
         class="absolute bottom-[-19%] left-0 w-full h-[300px] z-10">
</section>
