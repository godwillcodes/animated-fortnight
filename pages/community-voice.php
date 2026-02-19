<?php
/**
 * Template Name: Community Voice
 * Description: A platform for sharing stories from the community.
 */
get_header();
?>

<!-- Hero Section -->
<section class="relative min-h-[90vh] flex items-center overflow-hidden bg-gradient-to-br from-emerald-900 via-emerald-800 to-emerald-700">
    <!-- Decorative Elements -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-20 right-20 w-96 h-96 bg-white rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 left-20 w-96 h-96 bg-emerald-300 rounded-full blur-3xl"></div>
    </div>
    
    <!-- Pattern Overlay -->
    <div class="absolute inset-0 opacity-5" style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 40px 40px;"></div>

    <div class="relative z-10 w-full container mx-auto px-6 py-32">
        <div class="container">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 mb-8 px-5 py-2 bg-white/10 backdrop-blur-sm rounded-full border border-white/20" data-aos="fade-up">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                </svg>
                <span class="text-sm font-medium text-white tracking-wide">Community Voice Platform</span>
            </div>

            <!-- Heading -->
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold text-white leading-[1.1] mb-8 tracking-tight" data-aos="fade-up" data-aos-delay="100">
                What Kenya do<br>
                you <span class="text-emerald-300">dream</span> of?
            </h1>

            <!-- Description -->
            <p class="text-xl md:text-2xl text-emerald-50 leading-relaxed mb-12 max-w-3xl" data-aos="fade-up" data-aos-delay="200">
                Share your experiences, insights, and inspirations. Every story sparks change. Your voice can shape the community and inspire action.
            </p>

            <!-- CTAs -->
            <div class="flex flex-col sm:flex-row gap-4" data-aos="fade-up" data-aos-delay="300">
                <a href="#story-form" 
                   class="inline-flex items-center justify-center gap-3 bg-white text-emerald-900 font-semibold text-lg px-10 py-3 rounded-full hover:bg-emerald-50 hover:scale-105 transition-all duration-300 shadow-2xl group">
                    <span>Share Your Story</span>
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                </a>
                
                <a href="/volunteer" 
                   class="inline-flex items-center justify-center gap-3 bg-transparent text-white font-semibold text-lg px-10 py-3 rounded-full border-2 border-white/30 hover:bg-white/10 hover:border-white transition-all duration-300 group">
                    <span>I want to help</span>
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 text-white/60 animate-bounce">
        <span class="text-xs uppercase tracking-widest">Scroll</span>
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
        </svg>
    </div>
</section>

<!-- Story Submission Form -->
<section id="story-form" class="relative -mt-32 z-20 pb-32">
    <div class="max-w-5xl mx-auto px-6">
        <div class="bg-white rounded-3xl shadow-2xl p-8 md:p-12 lg:p-16 border border-gray-100" data-aos="fade-up">
            <!-- Form Header -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-100 rounded-2xl mb-6">
                    <svg class="w-8 h-8 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
                
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                    Share Your Story
                </h2>
                
                <p class="text-lg text-gray-600 max-w-2xl mx-auto leading-relaxed">
                    Your experience matters. Fill out the form below to inspire, inform, or challenge the community.
                </p>
            </div>

            <!-- Form -->
            <div class="prose prose-lg max-w-none">
                <?php echo do_shortcode('[forminator_form id="38"]'); ?>
            </div>
        </div>
    </div>
</section>

<!-- Stories Section -->
<section class="py-32 bg-gradient-to-b from-white to-gray-50">
    <div class="container mx-auto px-6">
        <!-- Section Header -->
        <div class="text-center mb-20" data-aos="fade-up">
            <div class="inline-flex items-center gap-3 mb-6">
                <div class="h-[1px] w-12 bg-gradient-to-r from-transparent to-gray-300"></div>
                <span class="text-sm uppercase tracking-[0.3em] text-gray-400 font-medium">From The Community</span>
                <div class="h-[1px] w-12 bg-gradient-to-l from-transparent to-gray-300"></div>
            </div>
            
            <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                Communities' Vision
            </h2>
            
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Real stories from real Kenyans building the future we deserve
            </p>
        </div>

        <!-- Stories Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php
            $blog_query = new WP_Query([
                'post_type' => 'post',
                'posts_per_page' => -1,
            ]);

            if ($blog_query->have_posts()) :
                $delay = 0;
                while ($blog_query->have_posts()) : $blog_query->the_post();

                    $word_count = str_word_count(strip_tags(get_the_content()));
                    $reading_time = ceil($word_count / 200);
                    $reading_time_text = $reading_time . ' min read';
                    
                    // Get excerpt
                    $excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 25);
            ?>
                <article class="group bg-white rounded-3xl text-sm overflow-hidden border border-gray-100 hover:border-emerald-600 hover:shadow-2xl hover:-translate-y-2 transition-all duration-500" 
    data-aos="fade-up" 
    data-aos-delay="<?php echo $delay; ?>">

    <!-- Featured Image -->
    <?php if (has_post_thumbnail()) : ?>
        <div class="relative h-56 overflow-hidden bg-gradient-to-br from-emerald-100 to-emerald-50">
            <?php the_post_thumbnail('large', [
                'class' => 'w-full h-full object-cover group-hover:scale-110 transition-transform duration-700'
            ]); ?>
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
        </div>
    <?php endif; ?>

    <!-- Content -->
    <div class="p-8">
        <!-- Meta -->
        <div class="flex items-center gap-4 mb-4 text-sm text-gray-500">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <time datetime="<?php echo get_the_date('c'); ?>">
                    <?php echo get_the_date('M d, Y'); ?>
                </time>
            </div>
        </div>

        <!-- Title -->
        <h3 class="text-2xl font-bold text-gray-900 mb-4 leading-tight group-hover:text-emerald-700 transition-colors duration-300">
            <a href="<?php the_permalink(); ?>" class="hover:underline">
                <?php the_title(); ?>
            </a>
        </h3>

        <!-- Excerpt (trimmed to 280 characters) -->
        <!-- Excerpt (trimmed to 280 characters) -->
<p class="!text-gray-700 !text-[13px] !leading-relaxed !font-sans mb-6">
    <?php
        $excerpt = get_the_excerpt();
        $trimmed_excerpt = mb_strimwidth(strip_tags($excerpt), 0, 40, '...');
        echo esc_html($trimmed_excerpt);
    ?>
</p>

    </div>
</article>

            <?php
                    $delay += 100;
                    if ($delay > 400) $delay = 0; // Reset delay after 4 items
                endwhile;
                wp_reset_postdata();
            else :
            ?>
                <!-- Empty State -->
                <div class="col-span-full py-20 text-center" data-aos="fade-up">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-6">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">No stories yet</h3>
                    <p class="text-lg text-gray-600 mb-8">Be the first to share your vision for Kenya</p>
                    <a href="#story-form" 
                       class="inline-flex items-center gap-3 bg-emerald-700 text-white font-semibold px-8 py-4 rounded-full hover:bg-emerald-800 transition-all duration-300">
                        <span>Share Your Story</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Load More (Optional) -->
        <?php if ($blog_query->max_num_pages > 1) : ?>
            <div class="text-center mt-16" data-aos="fade-up">
                <a href="#" 
                   class="inline-flex items-center gap-3 bg-gray-900 text-white font-semibold px-10 py-5 rounded-full hover:bg-emerald-700 transition-all duration-300 shadow-lg hover:shadow-2xl hover:scale-105 group">
                    <span>Load More Stories</span>
                    <svg class="w-5 h-5 group-hover:translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>