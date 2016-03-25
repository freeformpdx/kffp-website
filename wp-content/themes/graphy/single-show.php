<?php
/**
 * @package Graphy
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>


		<?php graphy_header_meta(); ?>
		<?php if ( has_post_thumbnail() ): ?>
		<div class="post-thumbnail"><?php the_post_thumbnail(); ?></div>
		<?php endif; ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
	  <?php
	  $id = get_the_ID();
	  $custom_fields = get_post_custom();
	  ?>
	  <h2 class="dj-name"><?= $custom_fields['dj_name'][0] ?></h2>
	  
	  <h3 class="timeslot"><?= get_timeslot($id, true) ?></h3>
	  
		<?php the_content(); ?>
		<?php wp_link_pages( array(	'before' => '<div class="page-links">' . __( 'Pages:', 'graphy' ), 'after'  => '</div>', ) ); ?>
	</div><!-- .entry-content -->
	<?php graphy_footer_meta(); ?>
</article><!-- #post-## -->

		</main><!-- #main -->
	</div><!-- #primary -->

<? // if dj is logged in, show the show id and a link to create a playlist ?>
<?php if (is_user_logged_in ()) {
$showID = $custom_fields['id'][0];
?>
<div class="dj-login-wrapper">
<div class="dj-login">
<p class="show-id">KFFP ID: <?= $showID ?></p>
<a href='http://kffp.rocks/api/newSetlist/<?= $showID ?>' target='_blank'>Make New Playlist</a>
</div>
</div>

<?php } ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
