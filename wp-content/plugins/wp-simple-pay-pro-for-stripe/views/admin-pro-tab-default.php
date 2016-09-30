<?php 

/**
 * Represents the view for the additional pro only Default Settings tab - SP Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $sc_options;

// TODO Set radio button defaults in case upgrading from Lite to Pro.
// TODO Not the best way but best I could come up with at the moment. -PD 4/14/16
if ( null === $sc_options->get_setting_value( 'sc_coup_apply_button_style' ) ) {
	$sc_options->add_setting( 'sc_coup_apply_button_style', 'none' );
}

if ( null === $sc_options->get_setting_value( 'payment_button_style' ) ) {
	$sc_options->add_setting( 'payment_button_style', 'stripe' );
}

?>

<div>
	<label for="<?php echo esc_attr( $sc_options->get_setting_id( 'shipping' ) ); ?>"><?php _e( 'Enable Shipping Address', 'stripe' ); ?></label>
	<?php $sc_options->checkbox( 'shipping' ); ?>
	<span><?php _e( 'Require the user to enter their shipping address during checkout.', 'stripe' ); ?></span>
	<?php $sc_options->description( __( 'When a shipping address is required, the customer is also required to enter a billing address.', 'stripe' ) ); ?>
</div>

<div>
	<label><?php _e( 'Payment Button Style', 'stripe' ); ?></label>
	<?php $sc_options->radio_button( 'none', 'None', 'none', 'payment_button_style' ); ?>
	<?php $sc_options->radio_button( 'stripe', 'Stripe', 'stripe', 'payment_button_style' ); ?>
	<?php $sc_options->description( __( 'Enable Stripe styles for the main payment button. Base button CSS class:', 'stripe' ) . ' <code>sc-payment-btn</code>' ); ?>
</div>

<div>
	<label for="<?php echo esc_attr( $sc_options->get_setting_id( 'coup_label' ) ); ?>"><?php _e( 'Coupon Input Label', 'stripe' ); ?></label>
	<?php $sc_options->textbox( 'coup_label', 'regular-text' ); ?>
	<?php $sc_options->description( __( 'Label to show before the coupon code input.', 'stripe' ) ); ?>
</div>

<div>
	<label><?php _e( 'Apply Button Style', 'stripe' ); ?></label>
	<?php $sc_options->radio_button( 'none', 'None', 'none', 'sc_coup_apply_button_style' ); ?>
	<?php $sc_options->radio_button( 'stripe', 'Stripe', 'stripe', 'sc_coup_apply_button_style' ); ?>
	<?php $sc_options->description( __( 'Optionally enable Stripe styles for the coupon "Apply" button. Base button CSS class:', 'stripe' ) . ' <code>sc-coup-apply-btn</code>' ); ?>
</div>

<div>
	<label for="<?php echo esc_attr( $sc_options->get_setting_id( 'stripe_total_label' ) ); ?>"><?php _e( 'Stripe Total Label', 'stripe' ); ?></label>
	<?php $sc_options->textbox( 'stripe_total_label', 'regular-text' ); ?>
	<?php $sc_options->description( __( 'The default label for the stripe_total shortcode.', 'stripe' ) ); ?>
</div>

<div>
	<label for="<?php echo esc_attr( $sc_options->get_setting_id( 'sc_uea_label' ) ); ?>"><?php _e( 'Amount Input Label', 'stripe' ); ?></label>
	<?php $sc_options->textbox( 'sc_uea_label', 'regular-text' ); ?>
	<?php $sc_options->description( __( 'Label to show before the amount input.', 'stripe' ) ); ?>
</div>
