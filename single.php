<?php
/**
 * The template for displaying single posts with artistic layout
 *
 * @package PiedmontGlobal
 */

get_header();
?>

<main id="primary" class="site-main">

    <?php
    while ( have_posts() ) :
        the_post();
    ?>

    <!-- Hero / Post Banner -->
    <section class="shadow-sm">
    <div class="h-[500px] md:h-[400px] lg:h-[554px] relative"
        style="background-color: #0f6041;">
        <div class="absolute inset-0 flex items-end">
            <div class="container mx-auto w-full px-10 lg:px-0 pb-4 md:pb-12 lg:pb-12 text-white">
                <h1 class="text-2xl md:text-4xl lg:text-4xl font-bold" data-aos="fade-up" data-aos-delay="200">
                    <?php the_title(); ?>
                </h1>
                <p class="text-base lg:text-lg my-4 max-w-4xl" data-aos="fade-up" data-aos-delay="400">
                    Join our volunteer network and play a vital role in mobilizing communities, educating citizens, and safeguarding the electoral process.
                </p>
                <a href="/community-voice/"
                    class="inline-block bg-black text-white px-5 py-2 mt-4 font-bold text-lg shadow-md hover:bg-gray-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-gray-500 transition flex items-center">
                    Make your voice heard
                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>

    <!-- Post Content -->
    <section class="max-w-4xl mx-auto px-6 py-16 prose prose-lg md:prose-xl" data-aos="fade-up">
        <?php the_content(); ?>
    </section>

    <!-- Post Navigation -->
    <section class="max-w-4xl mx-auto px-6 py-8 flex justify-between text-gray-800" data-aos="fade-up">
        <?php
        the_post_navigation(array(
            'prev_text' => '<span class="font-semibold">&larr; Previous:</span> <span class="underline">%title</span>',
            'next_text' => '<span class="font-semibold">Next:</span> <span class="underline">%title &rarr;</span>',
        ));
        ?>
    </section>

    

    <?php
    endwhile;
    ?>

</main>

<?php
get_footer();
