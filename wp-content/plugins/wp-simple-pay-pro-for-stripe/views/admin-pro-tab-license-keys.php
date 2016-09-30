<?php

/**
 * Represents the view for the License Keys admin tab - SP Pro
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function simplepay_tab_license_keys() {
	global $sc_options;

	$main_license_key    = $sc_options->get_setting_value( 'main_license_key' );
	$main_license_status = get_option( 'simplepay_main_license_status' );
	$main_license_error  = get_option( 'simplepay_main_license_error' );
	?>

	<div class="tab-content sc-admin-hidden" id="license-keys-settings-tab">
		<div>
			<?php $sc_options->description( __( 'Your license key is used for access to automatic upgrades and premium support.', 'stripe' ) ); ?>
		</div>

		<div>
			<label for="<?php echo esc_attr( $main_license_key ); ?>">
				<?php echo Stripe_Checkout_Pro::get_plugin_title() . ' ' . __( 'License Key', 'stripe' ); ?>
			</label>

			<?php $sc_options->license_key( 'main_license_key', $main_license_status, $main_license_error ); ?>
		</div>

		<?php do_action( 'sc_settings_tab_license' ); ?>
	</div>

	<?php
}

simplepay_tab_license_keys();
