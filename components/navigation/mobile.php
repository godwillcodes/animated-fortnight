<div
    class="container mx-auto px-6 py-6 fixed top-0 w-full z-[99] bg-white flex lg:hidden justify-between items-center">
    <div class="flex items-center">
        <?php
    $custom_logo_id = get_theme_mod('custom_logo');
    if ($custom_logo_id) {
      $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
      echo '<img src="' . esc_url($logo[0]) . '" alt="' . get_bloginfo('name') . '" class="h-8 w-auto" />';
    } else {
      echo '<span class="text-xl font-bold">' . get_bloginfo('name') . '</span>';
    }
    ?>
    </div>

    <div x-data="{ open: false, submenu: null }" @keydown.escape.window="open = false; submenu = null" x-cloak>
        <button @click="open = true" aria-label="Open main menu" class="focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7">
  <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5" />
</svg>

        </button>

        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm"
            @click="open = false; submenu = null" aria-hidden="true">
        </div>

        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-y-full opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0 opacity-100"
            x-transition:leave-end="translate-y-full opacity-0"
            class="fixed inset-x-4 bottom-4 z-50 rounded-2xl bg-white/20 backdrop-blur-2xl ring-1 ring-white/10 shadow-xl p-6 text-white"
            style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(20px);" role="dialog"
            aria-label="Mobile menu">

            <div x-show="submenu === null" x-transition>
                <div class="flex justify-end mb-6">
                    <button @click="open = false" class="text-white hover:text-gray-300 focus:outline-none">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <nav class="space-y-4">
                    <!-- <button @click="submenu = 'solutions'"
                        class="w-full text-left text-base font-semibold text-white hover:text-[#98C441] transition flex items-center justify-between">
                        Solutions
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <button @click="submenu = 'industries'"
                        class="w-full text-left text-base font-semibold text-white hover:text-[#98C441] transition flex items-center justify-between">
                        Industries
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </button> -->
                    <?php
                    $menu_items = wp_get_nav_menu_items(2);

                    if ($menu_items) {
                        foreach ($menu_items as $menu_item) {
                            echo '<a href="' . esc_url($menu_item->url) . '" class="block text-base font-semibold text-white hover:text-[#98C441] transition">' . esc_html($menu_item->title) . '</a>';
                        }
                    }
                    ?>


                </nav>

<div class="mt-6">
  <a href="/donate"
     class="relative inline-flex items-center justify-center w-full sm:w-auto px-6 py-3 text-base font-semibold text-white bg-[#ED2024] rounded-lg overflow-hidden group transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-red-300">
     
    <!-- Hover Background Animation -->
    <span class="absolute inset-0 bg-[#c71a1f] translate-x-[-100%] group-hover:translate-x-0 transition-transform duration-300 ease-out"></span>
    
    <!-- Button Content -->
    <span class="relative flex items-center justify-center gap-3 group-hover:text-white">
      Donate Now
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
           stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 
             1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 
             1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 
             0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 
             1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 
             0H3.75m0 0h-.375a1.125 1.125 0 0 
             1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 
             0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 
             0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 
             0h.008v.008H6V10.5Z" />
      </svg>
    </span>
  </a>
</div>

            </div>

            <div x-show="submenu !== null" x-transition>
                <div class="flex justify-between items-center mb-6">
                    <button @click="submenu = null" class="text-white hover:text-gray-300 focus:outline-none">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <span class="text-base font-semibold"
                        x-text="submenu.charAt(0).toUpperCase() + submenu.slice(1)"></span>
                    <button @click="open = false" class="text-white hover:text-gray-300 focus:outline-none">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div x-show="submenu === 'solutions'" x-transition class="space-y-3">
                   <?php 
                    // Get all terms in the 'solution' taxonomy
                    $terms = get_terms([
                        'taxonomy'   => 'solution',
                        'hide_empty' => false, // true hides unused terms
                    ]);

                    if (!empty($terms) && !is_wp_error($terms)) : 
                        foreach ($terms as $term) : ?>
                            <a href="<?php echo esc_url(get_term_link($term)); ?>" 
                            class="block text-white hover:text-[#98C441]">
                            <?php echo esc_html($term->name); ?>
                            </a>
                        <?php endforeach; 
                    endif;
                    ?>
                    <a href="#" class="block text-base my-8 font-semibold text-white hover:text-[#98C441] transition">Explore All Solutions</a>


                </div>

                <div x-show="submenu === 'industries'" x-transition class="space-y-3">
                    <?php
                    $industries = new WP_Query([
                        'post_type'      => 'industry',
                        'posts_per_page' => -1,
                        'post_status'    => 'publish',
                    ]);

                    if ($industries->have_posts()) {
                        while ($industries->have_posts()) {
                            $industries->the_post();
                            echo '<a href="' . esc_url(get_permalink()) . '" class="block text-white hover:text-[#98C441]">' . esc_html(get_the_title()) . '</a>';
                        }
                        wp_reset_postdata();
                    }
                    ?>
                    <a href="#" class="block text-base font-semibold text-white hover:text-[#98C441] transition">Explore All Industries</a>

                </div>

                <div class="mt-6">
                    <a href="#"
                        class="block w-full text-center bg-[#98C441] text-[#1F3131] font-medium py-2 rounded-lg hover:bg-[#8ABF3B] transition">Request
                        Demo</a>
                </div>
            </div>
        </div>
    </div>
</div>