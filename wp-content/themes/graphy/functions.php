<?php
wp_register_script('freeform', get_stylesheet_directory_uri() . '/js/freeform.js', array('jquery', 'mb.miniAudioPlayer'));
wp_enqueue_script('freeform');

/**
 * KFFP - Add Show Custom Post Type
 */

add_action( 'init', 'create_show_post_type' );
function create_show_post_type() {
  register_post_type( 'show',
    array(
      'capabilities' => array(
        'create_posts' => true,
      ),
      'has_archive' => 'schedule',
      'labels' => array(
        'name' => __( 'Shows' ),
        'singular_name' => __( 'Show' ),
        'edit_item' => 'Edit Show',
        'new_item' => 'New Show',
        'view_item' => 'View Show',
        'search_items' => 'Search Shows',
        'not_found' => 'No shows found',
        'all_items' => 'All Shows',
      ),
      'map_meta_cap' => true,
      'menu_icon' => 'dashicons-format-audio',
      'public' => true,
      'rewrite' => array(
        'with_front' => false,
      ),
      'supports' => array('title','author','editor','thumbnail','custom-fields'),
    )
  );
}

// ordering for shows archive
add_action( 'pre_get_posts', 'pre_filter_shows_archive' );
function pre_filter_shows_archive( $query ) {
    // only modify front-end category archive pages
    if( is_post_type_archive('show') && !is_admin() && $query->is_main_query() ) {
        $query->set( 'posts_per_page','200' );
        $query->set( 'orderby','meta_value_num' );
        $query->set( 'meta_key','start_day' );
        
        $query->set( 'meta_query', array(
          'relation' => 'AND',
            array( 'key' => 'start_day', 'compare' => '>=', 'type' => 'numeric' ), 
            array( 'key' => 'start_hour', 'compare' => '>=', 'type' => 'numeric' ) 
          ) 
        );
        $query->set( 'order','ASC' );
    }
}

add_filter('posts_orderby', 'shows_orderby');
function shows_orderby( $orderby ) {
  if( get_queried_object()->query_var === 'show' )  {
  
    global $wpdb;
    $orderby = str_replace( $wpdb->prefix.'postmeta.meta_value', 'mt1.meta_value, mt2.meta_value', $orderby );
  
  }
  return $orderby;
}

// adjust admin UI columns for shows
add_filter('manage_edit-show_columns', 'create_manage_shows_columns');
function create_manage_shows_columns($columns) {
    $columns['dj_name'] = 'DJ';
    $columns['timeslot'] = 'Time Slot';
    
    $stats = $columns['gadwp_stats'];
    if (strlen($stats)) {
      unset($columns['gadwp_stats']);
      $columns['gadwp_stats'] = $stats;
    }
    
    unset($columns['author']);
    unset($columns['date']);
    unset($columns['wpseo-score']);
    return $columns;
}

add_action('manage_posts_custom_column',  'add_manage_shows_columns');
function add_manage_shows_columns($name) {
    global $post;
    switch ($name) {
        case 'dj_name':
          $output = get_post_meta($post->ID, 'dj_name', true);
          echo $output;
          
          break;
        
        case 'timeslot':
          echo get_timeslot($post->ID);
          
          break;
    }
}

// display clean timeslot from custom fields
function display_day_of_week($day, $fancy = false) {
  $dowMap = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
  
  $output = $dowMap[$day];
  
  if (!$fancy) $output = substr($output, 0, 3);
  
  return $output;
}

function get_timeslot($id, $fancy = false) {
  $output = '';
  $custom_fields = get_post_custom($id);
  
  $dayInt = $custom_fields['start_day'][0];
  $startHour = $custom_fields['start_hour'][0];
  $endHour = $custom_fields['end_hour'][0];
  if ($endHour === '0') $endHour = 24;
  
  if ( strlen($dayInt) ) {
    $output .= display_day_of_week($dayInt, $fancy) . ' ';
    
    $output .= $startHour . ':00';
    $output .= ' - ';
    $output .= $endHour . ':00';
  }
  
  return $output;
}

/**
 * KFFP - Clean up admin UI
 */

// hide menus for the donkeys (contributors)
add_action( 'admin_menu', 'remove_menus_for_donkeys' );
function remove_menus_for_donkeys() {
  if (current_user_can('contributor')) {
    remove_menu_page('edit-comments.php');
    remove_menu_page('profile.php');
    remove_menu_page('tools.php');
    
    
  }
}

// clean up dashboard
add_action( 'wp_dashboard_setup', 'remove_dashboard_widgets' );
function remove_dashboard_widgets() {
  remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
  remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
  remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
  remove_meta_box( 'wpseo-dashboard-overview', 'dashboard', 'normal' );
  
  if (current_user_can('contributor')) {
    remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
    remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
  }
}

// clean up top admin bar
add_action( 'admin_bar_menu', 'remove_admin_bar_stuff', 999 );
function remove_admin_bar_stuff( $wp_admin_bar ) {
  $wp_admin_bar->remove_node( 'wp-logo' );
  $wp_admin_bar->remove_node( 'wpseo-menu' );
  
  if ( current_user_can('contributor') ) {
    $wp_admin_bar->remove_node( 'comments' );
  }
}



/**
 * END KFFP
 */














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

/**
 * add custom URLs to sitemap
 */

function add_sitemap_custom_items(){
  $sitemap_custom_items = '<sitemap>
    <loc>http://www.freeformportland.com/kornhub/</loc>
  </sitemap>';

  return $sitemap_custom_items;
}
add_filter( 'wpseo_sitemap_index', 'add_sitemap_custom_items' );
