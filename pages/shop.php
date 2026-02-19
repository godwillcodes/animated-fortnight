<?php
/**
 * Template Name: Shop22
 * Description: Minimalist Shop Page with Artistic Layout
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
          Support our cause by purchasing limited-edition merchandise. Every item fuels the movement for justice and courage.
        </p>
        <a href="/donate" 
  class="inline-flex items-center gap-2 bg-white text-[#0f6041] px-6 py-2.5 mt-4 text-base font-semibold rounded-full shadow-sm">
  Donate Now
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
<section class="py-20 bg-[#FAFAFA] text-[#1F3131]">
  <div class="mx-auto container px-6 lg:px-0">
    <div class="">

      <div class="grid grid-cols-1 gap-x-8 gap-y-12 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <?php
        $args = array(
          'post_type' => 'product',
          'posts_per_page' => -1,
          'post_status' => 'publish',
        );
        $products = new WP_Query($args);

        if ($products->have_posts()):
          while ($products->have_posts()): $products->the_post();
            $price = get_field('price');
            $image = get_the_post_thumbnail_url(get_the_ID(), 'full');
            ?>
            
           

            <a href="<?php the_permalink(); ?>" class="group">
        <img src="<?php echo esc_url($image); ?>" alt="<?php the_title_attribute(); ?>" class="w-[300px] h-[300px] rounded-lg bg-gray-200 object-cover group-hover:opacity-75 " />
        <p class="mt-4 text-sm text-gray-700"><?php the_title(); ?></p>
        <p class="mt-1 text-lg font-medium text-gray-900">KSh <?php echo esc_html($price); ?></p>
      </a>

         
         
         
         
         
         
         
         <?php
          endwhile;
          wp_reset_postdata();
        else:
          echo '<p class="text-center text-gray-600 col-span-4">No products available at the moment.</p>';
        endif;
        ?>
      </div>
    </div>
  </div>
</section>




<?php get_footer(); ?>
