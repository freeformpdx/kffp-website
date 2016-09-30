<?php

/**
 * Sidebar portion of the administration dashboard view - SP Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<!-- Use some built-in WP admin theme styles. -->

<div class="sidebar-container metabox-holder">
	<div class="postbox">
		<h3 class="wp-ui-primary"><span><?php _e( 'Quick Links', 'stripe' ); ?></span></h3>
		<div class="inside">
			<ul>
				<li>
					<div class="dashicons dashicons-arrow-right-alt2"></div>
					<a href="<?php echo Stripe_Checkout_Admin::ga_campaign_url( SC_WEBSITE_BASE_URL . 'docs/', 'sidebar-link' ); ?>" target="_blank">
						<?php _e( 'Support & Documentation', 'stripe' ); ?></a>
				</li>
				<li>
					<div class="dashicons dashicons-arrow-right-alt2"></div>
					<a href="<?php echo Stripe_Checkout_Admin::ga_campaign_url( SC_WEBSITE_BASE_URL . 'my-account/', 'sidebar-link' ); ?>" target="_blank">
						<?php _e( 'Your WP Simple Pay Account', 'stripe' ); ?></a>
				</li>
				<li>
					<div class="dashicons dashicons-arrow-right-alt2"></div>
					<a href="https://dashboard.stripe.com/" target="_blank">
						<?php _e( 'Your Stripe Dashboard', 'stripe' ); ?></a>
				</li>
				<li>
					<div class="dashicons dashicons-arrow-right-alt2"></div>
					<a href="<?php echo Stripe_Checkout_Admin::ga_campaign_url( SC_WEBSITE_BASE_URL . 'feature-requests/', 'sidebar-link' ); ?>" target="_blank">
						<?php _e( 'Submit a feature request', 'stripe' ); ?></a>
				</li>
			</ul>
		</div>
	</div>
</div>

<div class="sidebar-container metabox-holder">
	<div class="postbox-nobg">
		<div class="inside centered">
			<a href="https://stripe.com/" target="_blank">
				<img src="<?php echo SC_DIR_URL; ?>assets/images/powered-by-stripe.png" />
			</a>
		</div>
	</div>
</div>
