<?php
/**
 * The Sidebar
 *
 * @package Graphy
 */
if ( ! is_active_sidebar( 'sidebar' ) ) {
	return;
}
?>

	<div id="secondary" class="widget-area sidebar-area" role="complementary">
		<?php dynamic_sidebar( 'sidebar' ); ?>
	</div><!-- #secondary -->
