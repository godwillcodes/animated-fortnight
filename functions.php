<?php
/**
 * PiedmontGlobal functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package PiedmontGlobal
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function pg_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on PiedmontGlobal, use a find and replace
		* to change 'pg' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'pg', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'pg' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'pg_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'pg_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function pg_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'pg_content_width', 640 );
}
add_action( 'after_setup_theme', 'pg_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function pg_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'pg' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'pg' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'pg_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function pg_scripts() {
	// Tailwind CSS
	$tailwind_path = get_template_directory() . '/assets/css/output.css';
	$tailwind_uri  = get_template_directory_uri() . '/assets/css/output.css';

	if ( file_exists( $tailwind_path ) ) {
		wp_enqueue_style(
			'pg-tailwind',
			$tailwind_uri,
			array(),
			filemtime( $tailwind_path )
		);
	} elseif ( WP_DEBUG ) {
		error_log( 'Missing Tailwind CSS: ' . $tailwind_path );
	}

	// AOS CSS
	$aos_css_path = get_template_directory() . '/assets/css/aos.css';
	$aos_css_uri  = get_template_directory_uri() . '/assets/css/aos.css';

	if ( file_exists( $aos_css_path ) ) {
		wp_enqueue_style(
			'pg-aos',
			$aos_css_uri,
			array(),
			filemtime( $aos_css_path )
		);
	} elseif ( WP_DEBUG ) {
		error_log( 'Missing AOS CSS: ' . $aos_css_path );
	}

	// Owl Carousel CSS
	$owl_css_path = get_template_directory() . '/assets/css/owl.carousel.min.css';
	$owl_css_uri  = get_template_directory_uri() . '/assets/css/owl.carousel.min.css';

	if ( file_exists( $owl_css_path ) ) {
		wp_enqueue_style(
			'pg-owl-carousel',
			$owl_css_uri,
			array(),
			filemtime( $owl_css_path )
		);
	} elseif ( WP_DEBUG ) {
		error_log( 'Missing Owl Carousel CSS: ' . $owl_css_path );
	}

	$owl_theme_path = get_template_directory() . '/assets/css/owl.theme.default.min.css';
	$owl_theme_uri  = get_template_directory_uri() . '/assets/css/owl.theme.default.min.css';

	if ( file_exists( $owl_theme_path ) ) {
		wp_enqueue_style(
			'pg-owl-theme',
			$owl_theme_uri,
			array( 'pg-owl-carousel' ),
			filemtime( $owl_theme_path )
		);
	} elseif ( WP_DEBUG ) {
		error_log( 'Missing Owl Carousel Theme CSS: ' . $owl_theme_path );
	}

	// Alpine.js (self-hosted)
	$alpine_path = get_template_directory() . '/assets/js/alpine.min.js';
	$alpine_uri  = get_template_directory_uri() . '/assets/js/alpine.min.js';

	if ( file_exists( $alpine_path ) ) {
		wp_enqueue_script(
			'pg-alpine',
			$alpine_uri,
			array(),
			filemtime( $alpine_path ),
			true
		);
	} elseif ( WP_DEBUG ) {
		error_log( 'Missing Alpine JS: ' . $alpine_path );
	}

	// AOS JS
	$aos_js_path = get_template_directory() . '/assets/js/aos.js';
	$aos_js_uri  = get_template_directory_uri() . '/assets/js/aos.js';

	if ( file_exists( $aos_js_path ) ) {
		wp_enqueue_script(
			'pg-aos',
			$aos_js_uri,
			array(),
			filemtime( $aos_js_path ),
			true
		);
	} elseif ( WP_DEBUG ) {
		error_log( 'Missing AOS JS: ' . $aos_js_path );
	}

	// jQuery (self-hosted)
	$jquery_path = get_template_directory() . '/assets/js/jquery.min.js';
	$jquery_uri  = get_template_directory_uri() . '/assets/js/jquery.min.js';

	if ( file_exists( $jquery_path ) ) {
		wp_enqueue_script(
			'pg-jquery',
			$jquery_uri,
			array(),
			filemtime( $jquery_path ),
			true
		);
	} elseif ( WP_DEBUG ) {
		error_log( 'Missing jQuery: ' . $jquery_path );
	}

	// Owl Carousel JS
	$owl_js_path = get_template_directory() . '/assets/js/owl.carousel.min.js';
	$owl_js_uri  = get_template_directory_uri() . '/assets/js/owl.carousel.min.js';

	if ( file_exists( $owl_js_path ) ) {
		wp_enqueue_script(
			'pg-owl-carousel',
			$owl_js_uri,
			array( 'pg-jquery' ),
			filemtime( $owl_js_path ),
			true
		);
	} elseif ( WP_DEBUG ) {
		error_log( 'Missing Owl Carousel JS: ' . $owl_js_path );
	}

	// Navigation JS
	$nav_path = get_template_directory() . '/js/navigation.js';
	$nav_uri  = get_template_directory_uri() . '/js/navigation.js';

	if ( file_exists( $nav_path ) ) {
		wp_enqueue_script(
			'pg-navigation',
			$nav_uri,
			array( 'pg-alpine' ),
			filemtime( $nav_path ),
			true
		);
	} elseif ( WP_DEBUG ) {
		error_log( 'Missing Navigation JS: ' . $nav_path );
	}

	// Comments
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'pg_scripts' );


// AOS initialization
add_action( 'wp_footer', function () {
	echo "<script>AOS.init();</script>";
}, 100 );

// Allow SVG
add_filter( 'wp_check_filetype_and_ext', function($data, $file, $filename, $mimes) {

  global $wp_version;
  if ( $wp_version !== '4.7.1' ) {
     return $data;
  }

  $filetype = wp_check_filetype( $filename, $mimes );

  return [
      'ext'             => $filetype['ext'],
      'type'            => $filetype['type'],
      'proper_filename' => $data['proper_filename']
  ];

}, 10, 4 );

function cc_mime_types( $mimes ){
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter( 'upload_mimes', 'cc_mime_types' );

function fix_svg() {
  echo '<style type="text/css">
        .attachment-266x266, .thumbnail img {
             width: 100% !important;
             height: auto !important;
        }
        </style>';
}
add_action( 'admin_head', 'fix_svg' );


/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Load WooCommerce compatibility file.
 */
if ( class_exists( 'WooCommerce' ) ) {
	require get_template_directory() . '/inc/woocommerce.php';
}

/**
 * CyberSource Unified Checkout for donations (donate-2 page).
 */
require get_template_directory() . '/inc/cybersource.php';
