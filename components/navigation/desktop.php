<!-- Desktop Navigation Component -->
<!-- Desktop Navigation Component -->
<header class="hidden lg:block fixed w-full bg-white top-0 z-50 transition-colors duration-300" x-data="{ 
        scrolled: false, 
        logoDefault: '<?php echo esc_url( wp_get_attachment_image_src( get_theme_mod("custom_logo"), "full" )[0] ); ?>',
        logoScrolled: '<?php echo esc_url( wp_get_attachment_image_src( get_theme_mod("custom_logo"), "full" )[0] ); ?>'
        }" x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 100 })"
        :class="{ 'bg-gradient-to-b from-[#F9F8F6]/95 to-[#F9F8F6]/70 mt-0 backdrop-blur-xl shadow-[0_4px_30px_rgba(0,0,0,0.1)] text-black': scrolled }">
        <div class="container mx-auto py-4 px-6 lg:px-0 flex items-center justify-between">

        <!-- Logo -->
        <div class="flex items-center">
            <a href="<?php echo esc_url(home_url('/')); ?>" aria-label="Home">
                <img :src="scrolled ? logoScrolled : logoDefault" alt="<?php echo esc_attr( get_bloginfo('name') ); ?>"
                    class="h-12 w-auto transition-all duration-300" />
            </a>
        </div>


        <!-- Navigation -->
        <nav class="flex items-center space-x-6 text-base font-medium transition-colors duration-300"
            :class="scrolled ? 'text-black' : 'text-black'" x-data="{ open: false }"
            @keydown.escape.window="open = false">
            <!-- Solutions Dropdown -->
           
           <?php
$menu_items = wp_get_nav_menu_items(2);

if ($menu_items) {
    foreach ($menu_items as $menu_item) {
        echo '<a href="' . esc_url($menu_item->url) . '" class="relative group transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#ED2024]">
                <span class="relative z-10">' . esc_html($menu_item->title) . '</span>
                <span class="absolute left-0 -bottom-1 w-0 h-0.5 bg-[#ED2024] transition-all duration-300 group-hover:w-full"></span>
            </a>';
    }
}
?>


        </nav>

        <!-- CTA Button -->
        <div>
           <a href="/donate"
                        class="relative inline-flex items-center px-5 py-2 text-base font-semibold text-[#ED2024] border-2 border-[#ED2024] rounded-lg overflow-hidden group transition-all duration-300">
                        <span
                            class="absolute inset-0 bg-[#ED2024] translate-x-[-100%] group-hover:translate-x-0 transition-transform duration-300 ease-out"></span>
                        <span class="relative group-hover:text-white flex items-center">
                            Donate Now
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6 ml-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                            </svg>

                        </span>
                    </a>



        </div>
    </div>
</header>