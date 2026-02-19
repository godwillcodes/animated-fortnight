<?php
/**
 * Template Name: Terms, Privacy
 * Description: 
 */
get_header();
?>

<section class="relative bg-[linear-gradient(to_bottom,_#1F3131_85%,_#006155_100%)]  w-full text-white overflow-hidden">
    <?php get_template_part('components/navigation/desktop'); ?>
    <?php get_template_part('components/navigation/mobile'); ?>

    <div class="w-full pt-[30%] lg:pt-[8%] px-4 sm:px-6 md:px-12 relative z-20 pb-60">
        <div class="text-center gap-y-8 max-w-4xl mx-auto">
            <h1 class="text-base sm:text-lg md:text-xl font-bold hidden">
                <?php the_title(); ?>
            </h1>

            <h2 class="text-3xl py-5 sm:text-4xl md:text-5xl max-w-4xl font-extrabold mb-6 leading-[98%]">
                <?php the_title(); ?>
            </h2>

            <p class="text-sm sm:text-base md:text-lg leading-relaxed">
                We help you with the ​people, capabilities, and strategies you need to lead with clarity, confidence, and cultural fluency. Let’s build real connection across markets, channels, and communities.
            </p>

             <a href="/contact" class="inline-block mt-8 bg-[#98C441] text-[#1F3131] px-5 py-2 font-bold text-base shadow-md hover:bg-[#8AB738] focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#98C441] transition">
                Get in Touch
            </a>
        </div>
    </div>

    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/icons/pattern-3.svg'); ?>"
         alt="<?php echo esc_attr(get_bloginfo('name')); ?>"
         class="absolute bottom-[-20px] lg:bottom-[-105%] left-0 w-full z-10">
</section>



<section class="container mx-auto my-12 px-8 lg:px-0">
  <div class="prose lg:prose-xl">
    <?php the_content(); ?>
  </div>
</section>


<style>
  /* Base text styles */
.prose {
  color: #1a1a1a;
  line-height: 1.7;
  font-size: 1.125rem;
}

.prose h1, 
.prose h2, 
.prose h3, 
.prose h4 {
  margin-top: 2.5rem;
  margin-bottom: 1.25rem;
  font-weight: 700;
  line-height: 1.3;
}

.prose h1 { font-size: 2.25rem; }
.prose h2 { font-size: 1.875rem; }
.prose h3 { font-size: 1.5rem; }
.prose h4 { font-size: 1.25rem; }

.prose p {
  margin-top: 1.25rem;
  margin-bottom: 1.25rem;
}

.prose ul, 
.prose ol {
  margin: 1.5rem 0;
  padding-left: 1.75rem;
}

.prose ul li {
  list-style-type: disc;
  margin-bottom: 0.5rem;
}

.prose ol li {
  list-style-type: decimal;
  margin-bottom: 0.5rem;
}

.prose a {
  color: #006155;
  text-decoration: underline;
  transition: color 0.2s ease;
}
.prose a:hover {
  color: #004c44;
}

.prose blockquote {
  border-left: 4px solid #006155;
  padding-left: 1rem;
  margin: 2rem 0;
  font-style: italic;
  color: #444;
  background: #f9f9f9;
}

.prose img {
  margin: 2rem 0;
  border-radius: 0.5rem;
  max-width: 100%;
  height: auto;
}

.prose pre {
  background: #1e1e1e;
  color: #f8f8f2;
  padding: 1rem;
  border-radius: 0.5rem;
  overflow-x: auto;
  margin: 2rem 0;
}

.prose code {
  background: #f5f5f5;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
  font-size: 0.95em;
}

</style>


<?php
get_template_part( 'components/common/cta' ); 
get_footer(); 
?>