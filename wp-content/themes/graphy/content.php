<?php
/**
 * @package Graphy
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<h1 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
		<?php graphy_header_meta(); ?>
		<?php if ( has_post_thumbnail() && ! is_search() ): ?>
		<div class="post-thumbnail">
			<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
		</div>
		<?php endif; ?>
	</header><!-- .entry-header -->

	<?php if ( get_post_format() || 'content' == get_theme_mod( 'graphy_content' ) && ! ( is_search() || is_archive() ) ) : ?>
	<div class="entry-content">
		<?php the_content( __( '<span class="continue-reading">Continue reading &rarr;</span>', 'graphy' ) ); ?>
		<?php wp_link_pages( array(	'before' => '<div class="page-links">' . __( 'Pages:', 'graphy' ), 'after'  => '</div>', ) ); ?>
	</div><!-- .entry-content -->
	<?php else : ?>
	<div class="entry-summary">
		<?php the_excerpt(); ?>
		<a href="<?php the_permalink(); ?>" rel="bookmark"><?php _e( '<span class="continue-reading">Continue reading &rarr;</span>', 'graphy' ); ?></a>
	</div><!-- .entry-summary -->
	<?php endif; ?>
</article><!-- #post-## -->
