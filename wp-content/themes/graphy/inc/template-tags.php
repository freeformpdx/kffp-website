<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Graphy
 */

if ( ! function_exists( 'graphy_paging_nav' ) ) :
/**
 * Display navigation to next/previous set of posts when applicable.
 *
 * @return void
 */
function graphy_paging_nav() {
	// Don't print empty markup if there's only one page.
	if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
		return;
	}
	?>
	<nav class="navigation paging-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Posts navigation', 'graphy' ); ?></h1>
		<div class="nav-links">

			<?php if ( get_next_posts_link() ) : ?>
			<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'graphy' ) ); ?></div>
			<?php endif; ?>

			<?php if ( get_previous_posts_link() ) : ?>
			<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'graphy' ) ); ?></div>
			<?php endif; ?>

		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'graphy_post_nav' ) ) :
/**
 * Display navigation to next/previous post when applicable.
 *
 * @return void
 */
function graphy_post_nav() {
	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}
	?>
	<nav class="navigation post-navigation" role="navigation">
		<div class="nav-links">

			<?php previous_post_link( '<div class="nav-previous"><h2>' . __( 'Previous post', 'graphy' ) . '</h2>%link</div>', _x( '%title', 'Previous post link', 'graphy' ) ); ?>
			<?php next_post_link(     '<div class="nav-next"><h2>' . __( 'Next post', 'graphy' ) . '</h2>%link</div>', _x( '%title', 'Next post link', 'graphy' ) ); ?>

		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'graphy_header_meta' ) ) :
/**
 * Display post header meta.
 *
 * @return void
 */
function graphy_header_meta() {
	// Hide for pages on Search.
	if ( 'post' != get_post_type() ) {
		return;
	}
	?>
	<div class="entry-meta">
		<span class="posted-on"><?php _e( 'Posted on', 'graphy' ); ?>
			<?php printf( '<a href="%1$s" rel="bookmark"><time class="entry-date published" datetime="%2$s">%3$s</time></a>',
				esc_url( get_permalink() ),
				esc_attr( get_the_date( 'c' ) ),
				esc_html( get_the_date() )
			); ?>
		</span>
		<?php if ( ! get_theme_mod( 'graphy_hide_author' ) ) : ?>
		<span class="byline"><?php _e( 'by', 'graphy' ); ?>
			<span class="author vcard">
				<?php printf( '<a class="url fn n" href="%1$s">%2$s</a>',
					esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
					esc_html( get_the_author() )
				); ?>
			</span>
		</span>
		<?php endif; ?>
		<?php if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
		<span class="comments-link">&middot; <?php comments_popup_link( __( 'Leave a comment', 'graphy' ), __( '1 Comment', 'graphy' ), __( '% Comments', 'graphy' ) ); ?></span>
		<?php endif; ?>
	</div><!-- .entry-meta -->
	<?php
}
endif;

if ( ! function_exists( 'graphy_footer_meta' ) ) :
/**
 * Display post footer meta when applicable.
 *
 * @return void
 */
function graphy_footer_meta() {
	// Hide for pages on Search.
	if ( 'post' != get_post_type() ) {
		return;
	}
	// Don't print empty markup if there's no Categories or Tags.
	$tags_list = get_the_tag_list( '', __( ', ', 'graphy' ) );
	if ( get_theme_mod( 'graphy_hide_category' ) && $tags_list == '' ) {
		return;
	}
	?>
	<footer class="entry-meta entry-footer">
		<?php if ( ! get_theme_mod( 'graphy_hide_category' ) ) : ?>
		<span class="cat-links">
			<?php printf( __( 'Posted in %1$s', 'graphy' ), get_the_category_list( __( ', ', 'graphy' ) ) ); ?>
		</span>
		<?php endif; ?>
		<?php if ( $tags_list != '' ) : ?>
		<span class="tags-links">
			<?php printf( __( 'Tagged %1$s', 'graphy' ), $tags_list ); ?>
		</span>
		<?php endif; // End if $tags_list ?>
	</footer><!-- .entry-meta -->
	<?php
}
endif;
