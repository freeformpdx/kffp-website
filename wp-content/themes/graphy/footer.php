<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Graphy
 */
?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer" role="contentinfo">
		<?php get_sidebar( 'footer' ); ?>
		<div class="site-info">
			<div class="site-copyright">&copy; <?php echo date('Y'); ?> <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></div>
			<div class="site-credit"><?php _e( 'Powered by ', 'graphy' ); ?><a href="<?php echo esc_url( __( 'http://wordpress.org/', 'graphy' ) ); ?>">WordPress</a> &amp;
			<a href="<?php echo esc_url( __( 'http://themegraphy.com/', 'graphy' ) ); ?>">Themegraphy</a></div>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>