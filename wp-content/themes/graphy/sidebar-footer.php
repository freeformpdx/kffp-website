<?php
/**
 * The Footer Widget
 *
 * @package Graphy
 */
if (   ! is_active_sidebar( 'footer-1' )
	&& ! is_active_sidebar( 'footer-2' )
	&& ! is_active_sidebar( 'footer-3' )
	&& ! is_active_sidebar( 'footer-4' )
)
	return;
?>

<div id="supplementary" class="footer-area">
	<div class="footer-widget">
		<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
		<div class="footer-widget-1 widget-area" role="complementary">
			<?php dynamic_sidebar( 'footer-1' ); ?>
		</div>
		<?php endif; ?>
		<?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
		<div class="footer-widget-2 widget-area" role="complementary">
			<?php dynamic_sidebar( 'footer-2' ); ?>
		</div>
		<?php endif; ?>
		<?php if ( is_active_sidebar( 'footer-3' ) ) : ?>
		<div class="footer-widget-3 widget-area" role="complementary">
			<?php dynamic_sidebar( 'footer-3' ); ?>
		</div>
		<?php endif; ?>
		<?php if ( is_active_sidebar( 'footer-4' ) ) : ?>
		<div class="footer-widget-4 widget-area" role="complementary">
			<?php dynamic_sidebar( 'footer-4' ); ?>
		</div>
		<?php endif; ?>
	</div><!-- #footer-widget-wrap -->
</div><!-- #supplementary -->
