<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Graphy
 */
?><!DOCTYPE html>
<!--[if IE 8]>
<html class="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 8) ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">

	<header id="masthead" class="site-header" role="banner">
		<div class="site-branding">
			<h1 class="site-logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><img alt="<?php bloginfo( 'name' ); ?>" src="<?php bloginfo('stylesheet_directory'); ?>/images/freeform-portland.svg" /></a></h1>
		</div>

		<div class="main-navigation-wrapper">
			<nav id="site-navigation" class="main-navigation" role="navigation">
				<h1 class="menu-toggle"><?php _e( 'Menu', 'graphy' ); ?></h1>
				<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'graphy' ); ?></a>
				<?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
			</nav><!-- #site-navigation -->
		</div>

		<?php if ( is_home() && get_header_image() ) : ?>
		<div id="header-image" class="header-image">
			<img src="<?php header_image(); ?>" width="<?php echo get_custom_header()->width; ?>" height="<?php echo get_custom_header()->height; ?>" alt="">
		</div><!-- #header-image -->
		<?php elseif ( is_page() && has_post_thumbnail() ) : ?>
		<div id="header-image" class="header-image">
			<?php the_post_thumbnail( 'graphy-page-thumbnail' ); ?>
		</div><!-- #header-image -->
		<?php endif; ?>
	</header><!-- #masthead -->

	<div id="content" class="site-content">
