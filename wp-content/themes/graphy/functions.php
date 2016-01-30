<?php
/**
 * Graphy functions and definitions
 *
 * @package Graphy
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 700; /* pixels */
}

if ( ! function_exists( 'graphy_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function graphy_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on Graphy, use a find and replace
	 * to change 'graphy' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'graphy', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Switches default core markup for search form, comment form,
	 * and comments to output valid HTML5.
	 */
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list' ) );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 800 );
	add_image_size( 'graphy-page-thumbnail', 1260, 350, true );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'graphy' ),
	) );

	// Enable support for Post Formats.
	add_theme_support( 'post-formats', array( 'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video' ) );

	// Setup the WordPress core custom header feature.
	add_theme_support( 'custom-header', apply_filters( 'graphy_custom_header_args', array(
		'default-image' => '',
		'width'         => 1260,
		'height'        => 350,
		'flex-height'   => false,
		'header-text'   => false,
	) ) );

	// This theme styles the visual editor to resemble the theme style.
	add_editor_style( array( 'css/editor-style.css', graphy_fonts_url() ) );

	// This theme uses its own gallery styles.
	add_filter( 'use_default_gallery_style', '__return_false' );
}
endif; // graphy_setup
add_action( 'after_setup_theme', 'graphy_setup' );

/**
 * Register widgetized area and update sidebar with default widgets.
 */
function graphy_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'graphy' ),
		'id'            => 'sidebar',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
	register_sidebar( array(
		'name'          => __( 'Footer 1', 'graphy' ),
		'id'            => 'footer-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
	register_sidebar( array(
		'name'          => __( 'Footer 2', 'graphy' ),
		'id'            => 'footer-2',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
	register_sidebar( array(
		'name'          => __( 'Footer 3', 'graphy' ),
		'id'            => 'footer-3',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
	register_sidebar( array(
		'name'          => __( 'Footer 4', 'graphy' ),
		'id'            => 'footer-4',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'graphy_widgets_init' );

/**
 * Return the Google font stylesheet URL, if available.
 *
 * This function is based on the twentythirteen_fonts_url function of Twenty Thirteen
 * http://wordpress.org/themes/twentythirteen
 */
function graphy_fonts_url() {
	$fonts_url = '';

	/* Translators: If there are characters in your language that are not
	 * supported by Lora, translate this to 'off'. Do not translate
	 * into your own language.
	 */
	$lora = _x( 'on', 'Lora font: on or off', 'graphy' );

	/* Translators: If there are characters in your language that are not
	 * supported by Bitter, translate this to 'off'. Do not translate into your
	 * own language.
	 */
	$bitter = _x( 'on', 'Bitter font: on or off', 'graphy' );

	if ( 'off' !== $lora || 'off' !== $bitter ) {
		$font_families = array();

		if ( 'off' !== $lora )
			$font_families[] = 'Lora:400,400italic,700,700italic';

		if ( 'off' !== $bitter )
			$font_families[] = 'Bitter:400';

		$query_args = array(
			'family' => urlencode( implode( '|', $font_families ) ),
			'subset' => urlencode( 'latin,latin-ext' ),
		);
		$fonts_url = add_query_arg( $query_args, "//fonts.googleapis.com/css" );
	}

	return $fonts_url;
}

/**
 * Enqueue scripts and styles.
 */
function graphy_scripts() {
	wp_enqueue_style( 'graphy-fonts', graphy_fonts_url(), array(), null );
	wp_enqueue_style( 'graphy-genericons', get_template_directory_uri() . '/genericons/genericons.css', array(), '3.0.3' );
	wp_enqueue_style( 'graphy-style', get_stylesheet_uri() );
	if ( 'ja' == get_bloginfo( 'language' ) ) {
		wp_enqueue_style( 'graphy-style-ja', get_template_directory_uri() . '/css/ja.css' );
	}
	wp_enqueue_script( 'graphy-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20140207', true );
	wp_enqueue_script( 'graphy-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	if ( preg_match( '/MSIE [6-8]/', $_SERVER['HTTP_USER_AGENT'] ) ) {
		wp_enqueue_script( 'graphy-html5shiv', get_template_directory_uri() . '/js/html5shiv.js', array(), '3.7.0' );
		wp_enqueue_script( 'graphy-css3-mediaqueries', get_template_directory_uri() . '/js/css3-mediaqueries.js', array(), '20100301' );
	}
}
add_action( 'wp_enqueue_scripts', 'graphy_scripts' );

/**
 * Adds custom style to the header.
 */
function graphy_customize_css() {
	?>
		<style type="text/css">
			.site-logo { margin-top: <?php echo esc_attr( get_theme_mod('graphy_top_margin') ); ?>px; padding-bottom: <?php echo esc_attr( get_theme_mod('graphy_bottom_margin') ); ?>px; }
			.entry-content a, .entry-summary a, .comment-content a, .comment-respond a, .navigation a, .comment-navigation a, .current-menu-item > a { color: <?php echo esc_attr( get_theme_mod('graphy_link_color') ); ?>; }
			a:hover { color: <?php echo esc_attr( get_theme_mod('graphy_link_hover_color') ); ?>; }
		</style>
	<?php
}
add_action( 'wp_head', 'graphy_customize_css');

/**
 * Adds custom classes to the body.
 */
function graphy_body_classes( $classes ) {

	if ( is_active_sidebar( 'sidebar' ) && ! is_page_template('nosidebar.php') ) {
		$classes[] = 'has-sidebar';
	} else {
		$classes[] = 'no-sidebar';
	}

	$graphy_footer = 0;
	$graphy_footer_max = 4;
	for( $i = 1; $i <= $graphy_footer_max; $i++ ) {
		if ( is_active_sidebar( 'footer-' . $i ) ) {
				$graphy_footer++;
			}
	}
	$classes[] = 'footer-' . $graphy_footer;

	if ( get_option( 'show_avatars' ) ) {
		$classes[] = 'has-avatars';
	}

	if ( get_theme_mod( 'graphy_add_border_radius' ) ) {
		$classes[] = 'border-radius';
	}

	return $classes;
}
add_filter( 'body_class', 'graphy_body_classes' );

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';
