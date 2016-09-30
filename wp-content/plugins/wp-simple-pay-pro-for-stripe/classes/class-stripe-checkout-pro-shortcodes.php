<?php
/**
 * Shortcodes class - SP Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Stripe_Checkout_Shortcodes' ) ) {
	
	class Stripe_Checkout_Shortcodes {
		
		// class instance variable
		private static $instance = null;

		private static $sc_id = null;
		
		/*
		 * class constructor
		 */
		private function __construct() {

			self::$sc_id = 0;

			// Add the shortcode functionality
			add_shortcode( 'stripe', array( $this, 'stripe_shortcode' ) );
			
			add_shortcode( 'stripe_total', array( $this, 'stripe_total' ) );
			
			add_shortcode( 'stripe_coupon', array( $this, 'stripe_coupon' ) );
			
			add_shortcode( 'stripe_text', array( $this, 'stripe_text' ) );
			
			add_shortcode( 'stripe_date', array( $this, 'stripe_date' ) );
			
			add_shortcode( 'stripe_checkbox', array( $this, 'stripe_checkbox' ) );
			
			add_shortcode( 'stripe_number', array( $this, 'stripe_number' ) );
			
			add_shortcode( 'stripe_amount', array( $this, 'stripe_amount' ) );
			
			add_shortcode( 'stripe_dropdown', array( $this, 'stripe_dropdown' ) );
			
			add_shortcode( 'stripe_radio', array( $this, 'stripe_radio' ) );
		}
		
		/**
		 * Function to process the [stripe] shortcode
		 * 
		 * @since 2.0.0
		 */
	   function stripe_shortcode( $attr, $content = null ) {

		   global $sc_options, $sc_script_options, $script_vars;
		  
		   //static $sc_id = 0;
		   
		   // Increment static uid counter
		   self::$sc_id++;

		   $attr = shortcode_atts( array(
						   'name'                      => ( ! ( null === $sc_options->get_setting_value( 'name' ) ) ? $sc_options->get_setting_value( 'name' ) : get_bloginfo( 'title' ) ),
						   'description'               => '',
						   'amount'                    => 0,
						   'image_url'                 => '',
						   'currency'                  => ( ! ( null === $sc_options->get_setting_value( 'currency' ) ) ? $sc_options->get_setting_value( 'currency' ) : 'USD' ),
						   'checkout_button_label'     => '',
						   'billing'                   => ( ! ( null === $sc_options->get_setting_value( 'billing' ) ) ? 'true' : 'false' ),    // true or false
						   'shipping'                  => ( ! ( null === $sc_options->get_setting_value( 'shipping' ) ) ? 'true' : 'false' ),    // true or false
						   'payment_button_label'      => ( ! ( null === $sc_options->get_setting_value( 'payment_button_label' ) ) ? $sc_options->get_setting_value( 'payment_button_label' ) : __( 'Pay with Card', 'stripe' ) ),
						   'enable_remember'           => ( ! ( null === $sc_options->get_setting_value( 'enable_remember' ) ) ? 'true' : 'false' ),    // true or false
						   'bitcoin'                   => ( ! ( null === $sc_options->get_setting_value( 'use_bitcoin' ) ) ? 'true' : 'false' ),    // true or false
						   'success_redirect_url'      => ( ! ( null === $sc_options->get_setting_value( 'success_redirect_url' ) ) ? $sc_options->get_setting_value( 'success_redirect_url' ) : get_permalink() ),
						   'failure_redirect_url'      => ( ! ( null === $sc_options->get_setting_value( 'failure_redirect_url' ) ) ? $sc_options->get_setting_value( 'failure_redirect_url' ) : get_permalink() ),
						   'prefill_email'             => 'false',
						   'verify_zip'                => ( ! ( null === $sc_options->get_setting_value( 'verify_zip' ) ) ? 'true' : 'false' ),
						   'payment_button_style'      => ( null === $sc_options->get_setting_value( 'payment_button_style' ) || $sc_options->get_setting_value( 'payment_button_style' ) == 'none' ? 'none' : '' ),
						   'test_mode'                 => 'false',
						   'id'                        => null,
						   'alipay'                    => ( ! ( null === $sc_options->get_setting_value( 'alipay' ) ) ? $sc_options->get_setting_value( 'alipay' ) : 'false' ), // true, false or auto
						   'alipay_reusable'           => ( ! ( null === $sc_options->get_setting_value( 'alipay_reusable' ) ) ? 'true' : 'false' ), // true or false
						   'locale'                    => ( ! ( null === $sc_options->get_setting_value( 'locale' ) ) ? $sc_options->get_setting_value( 'locale' ) : 'en' ), // empty or auto
						   'payment_details_placement' => 'above',
						   'test_secret_key'           => '',
						   'test_publishable_key'      => '',
						   'live_secret_key'           => '',
						   'live_publishable_key'      => '',
					   ), $attr, 'stripe' );
		   
		   
		   $name                      = $attr['name'];
		   $description               = $attr['description'];
		   $amount                    = $attr['amount'];
		   $image_url                 = $attr['image_url'];
		   $currency                  = $attr['currency'];
		   $checkout_button_label     = $attr['checkout_button_label'];
		   $billing                   = $attr['billing'];
		   $shipping                  = $attr['shipping'];
		   $payment_button_label      = $attr['payment_button_label'];
		   $enable_remember           = $attr['enable_remember'];
		   $bitcoin                   = $attr['bitcoin'];
		   $success_redirect_url      = $attr['success_redirect_url'];
		   $failure_redirect_url      = $attr['failure_redirect_url'];
		   $prefill_email             = $attr['prefill_email'];
		   $verify_zip                = $attr['verify_zip'];
		   $payment_button_style      = $attr['payment_button_style'];
		   $test_mode                 = $attr['test_mode'];
		   $id                        = $attr['id'];
		   $alipay                    = $attr['alipay'];
		   $alipay_reusable           = $attr['alipay_reusable'];
		   $locale                    = $attr['locale'];
		   $payment_details_placement = $attr['payment_details_placement'];
		   $test_secret_key           = $attr['test_secret_key'];
		   $test_publishable_key      = $attr['test_publishable_key'];
		   $live_secret_key           = $attr['live_secret_key'];
		   $live_publishable_key      = $attr['live_publishable_key'];
		   
		   // Remove these first to avoid issues if there were keys set in the past but now there are not.
		   $sc_options->delete_setting( 'live_secret_key_temp' );
		   $sc_options->delete_setting( 'test_secret_key_temp' );
		   
		   if ( ! empty( $test_secret_key ) ) {
			   $sc_options->add_setting( 'test_secret_key_temp', $test_secret_key );
		   }
		   
		   if ( ! empty( $test_publishable_key ) ) {
			   $sc_options->add_setting( 'test_publishable_key_temp', $test_publishable_key );
		   }
		   
		   if ( ! empty( $live_secret_key ) ) {
			   $sc_options->add_setting( 'live_secret_key_temp', $live_secret_key );
		   }
		   
		   if ( ! empty( $live_publishable_key ) ) {
			   $sc_options->add_setting( 'live_publishable_key_temp', $live_publishable_key );
		   }

		   // Generate custom form id attribute if one not specified.
		   // Rename var for clarity.
		   $form_id = $id;
		   if ( $form_id === null || empty( $form_id ) ) {
				$form_id = 'sc_checkout_form_' . self::$sc_id;
		   }

		   Shortcode_Tracker::set_as_base( 'stripe', $attr );

		   $test_mode = ( isset( $_GET['test_mode'] ) ? 'true' : $test_mode );


		    // Check if in test mode or live mode
		   if ( 0 == $sc_options->get_setting_value( 'enable_live_key' ) || 'true' == $test_mode ) {
			   // Test mode
			   if ( ! ( null === $sc_options->get_setting_value( 'test_publishable_key_temp' ) ) ) {
				   $data_key = $sc_options->get_setting_value( 'test_publishable_key_temp' );
				   $sc_options->delete_setting( 'test_publishable_key_temp' );
			   } else {
				   $data_key = ( null !== $sc_options->get_setting_value( 'test_publish_key' ) ? $sc_options->get_setting_value( 'test_publish_key' ) : '' );
			   }
			   
			   if ( null === $sc_options->get_setting_value( 'test_secret_key' ) && null === $sc_options->get_setting_value( 'test_publishable_key_temp' ) ) {
				   $data_key = '';
			   }
		   } else {
			   // Live mode
			   if ( ! ( null === $sc_options->get_setting_value( 'live_publishable_key_temp' ) ) ) {
				   $data_key = $sc_options->get_setting_value( 'live_publishable_key_temp' );
				   $sc_options->delete_setting( 'live_publishable_key_temp' );
			   } else {
				   $data_key = ( null !== $sc_options->get_setting_value( 'live_publish_key' ) ? $sc_options->get_setting_value( 'live_publish_key' ) : '' );
			   }
			   
			   if ( null === $sc_options->get_setting_value( 'live_secret_key' ) && null === $sc_options->get_setting_value( 'live_publishable_key_temp' ) ) {
				   $data_key = '';
			   }
		   }

		   if ( empty( $data_key ) ) {
			   if ( current_user_can( 'manage_options' ) ) {
				   return '<h6>' . __( 'Admin note: Checkout button will not appear until Stripe API keys are saved in settings.', 'stripe' ) . '</h6>';
			   } else {
				   return '';
			   }
		   }

		   if ( ! empty( $prefill_email ) && $prefill_email !== 'false' ) {
			   // Get current logged in user email
			   if ( is_user_logged_in() ) {
				   $prefill_email = get_userdata( get_current_user_id() )->user_email;
			   } else { 
				   $prefill_email = 'false';
			   }
		   }

		   // Add Parsley JS form validation attribute here.
		   $html  =
			   '<form method="POST" action="" class="' . $this->get_form_classes() . '" ' .
			   'id="' . esc_attr( $form_id ) . '" ' .
			   'data-sc-id="' . self::$sc_id . '" ' .
			   'data-parsley-validate>';

		   // Save all of our options to an array so others can run them through a filter if they need to
		   $sc_script_options = array( 
			   'script' => array(
				   'key'                  => $data_key,
				   'name'                 => html_entity_decode( $name ),
				   'description'          => html_entity_decode( $description ),
				   'amount'               => $amount,
				   'image'                => $image_url,
				   'currency'             => strtoupper( $currency ),
				   'panel-label'          => html_entity_decode( $checkout_button_label ),
				   'billing-address'      => $billing,
				   'shipping-address'     => $shipping,
				   'label'                => html_entity_decode( $payment_button_label ),
				   'allow-remember-me'    => $enable_remember,
				   'bitcoin'              => $bitcoin,
				   'email'                => $prefill_email,
				   'verify_zip'           => $verify_zip,
				   'alipay'               => $alipay,
				   'alipay_reusable'      => $alipay_reusable,
				   'locale'               => $locale,
				   'test_mode'            => $test_mode,
			   ),
			   'other' => array(
				   'success-redirect-url'      => $success_redirect_url,
				   'failure-redirect-url'      => $failure_redirect_url,
			   )
		   );

		   $html .= do_shortcode( $content );

		   // Apply filter after shortcode processed.
		   $sc_script_options = apply_filters( 'sc_modify_script_options', $sc_script_options );

		   // Set our global array based on the uid so we can make sure each button/form is unique
		   $script_vars[ self::$sc_id ] = array(
				   	'key'                 => ( ! empty( $sc_script_options['script']['key'] ) ? $sc_script_options['script']['key'] : ( ! ( null === $sc_options->get_setting_value( 'key' ) ) ? $sc_options->get_setting_value( 'key' ) : -1 ) ),
				   	'name'                => ( ! empty( $sc_script_options['script']['name'] ) ? $sc_script_options['script']['name'] : ( ! ( null === $sc_options->get_setting_value( 'name' ) ) ? $sc_options->get_setting_value( 'name' ) : -1 ) ),
				   	'description'         => ( ! empty( $sc_script_options['script']['description'] ) ? $sc_script_options['script']['description'] : ( ! ( null === $sc_options->get_setting_value( 'description' ) ) ? $sc_options->get_setting_value( 'description' ) : -1 ) ),
				   	'amount'              => ( ! empty( $sc_script_options['script']['amount'] ) ? $sc_script_options['script']['amount'] : 0 ),
				   	'image'               => ( ! empty( $sc_script_options['script']['image'] ) ? $sc_script_options['script']['image'] : ( ! ( null === $sc_options->get_setting_value( 'image_url' ) ) ? $sc_options->get_setting_value( 'image_url' ) : -1 ) ),
				   	'currency'            => ( ! empty( $sc_script_options['script']['currency'] ) ? $sc_script_options['script']['currency'] : ( ! ( null === $sc_options->get_setting_value( 'currency' ) ) ? $sc_options->get_setting_value( 'currency' ) : -1 ) ),
				   	'panelLabel'          => ( ! empty( $sc_script_options['script']['panel-label'] ) ? $sc_script_options['script']['panel-label'] : ( ! ( null === $sc_options->get_setting_value( 'checkout_button_label' ) ) ? $sc_options->get_setting_value( 'checkout_button_label' ) : -1 ) ),
				   	'billingAddress'      => ( ! empty( $sc_script_options['script']['billing-address'] ) ? $sc_script_options['script']['billing-address'] : ( ! ( null === $sc_options->get_setting_value( 'billing' ) ) ? $sc_options->get_setting_value( 'billing' ) : -1 ) ),
				   	'shippingAddress'     => ( ! empty( $sc_script_options['script']['shipping-address'] ) ? $sc_script_options['script']['shipping-address'] : ( ! ( null === $sc_options->get_setting_value( 'shipping' ) ) ? $sc_options->get_setting_value( 'shipping' ) : -1 ) ),
				   	'allowRememberMe'     => ( ! empty( $sc_script_options['script']['allow-remember-me'] ) ? $sc_script_options['script']['allow-remember-me'] : ( ! ( null === $sc_options->get_setting_value( 'enable_remember' ) ) ? $sc_options->get_setting_value( 'enable_remember' ) : -1 ) ),
				   	'bitcoin'             => ( ! empty( $sc_script_options['script']['bitcoin'] ) ? $sc_script_options['script']['bitcoin'] : ( ! ( null === $sc_options->get_setting_value( 'use_bitcoin' ) ) ? $sc_options->get_setting_value( 'use_bitcoin' ) : -1 ) ),
				   	'email'               => ( ! empty( $sc_script_options['script']['email'] ) && ! ( $sc_script_options['script']['email'] === 'false' ) ? $sc_script_options['script']['email'] : -1 ),
				   	'zipCode'             => ( ! empty( $sc_script_options['script']['verify_zip'] ) && ! ( $sc_script_options['script']['verify_zip'] === 'false' ) ? $sc_script_options['script']['verify_zip'] : -1 ),
				   	'alipay'              => ( ! empty( $sc_script_options['script']['alipay'] ) && ! ( $sc_script_options['script']['alipay'] === 'false' ) ? $sc_script_options['script']['alipay'] : -1 ),
				   	'alipayReusable'      => ( ! empty( $sc_script_options['script']['alipay_reusable'] ) && ! ( $sc_script_options['script']['alipay_reusable'] === 'false' ) ? $sc_script_options['script']['alipay_reusable'] : -1 ),
				   	'locale'              => ( ! empty( $sc_script_options['script']['locale'] ) ? $sc_script_options['script']['locale'] : 'auto' ),
				    'testMode'            => ( ! empty( $sc_script_options['script']['test_mode'] ) && ! ( $sc_script_options['script']['test_mode'] === 'false' ) ? $sc_script_options['script']['test_mode'] : -1 ),
			   		'invalid_html_string' => __( 'This button has been disable because the form is not well-formed HTML. Please check your shortcode source code to make sure nothing is conflicting.', 'stripe' ),
			   		'setupFee'            => ( ! empty( $sc_script_options['script']['setupFee'] ) ? $sc_script_options['script']['setupFee'] : 0 ),
		   );
		   
		   // Check if the current user is an admin and add a script variable we can use to check this
		   if ( current_user_can( 'manage_options' ) ) {
			   $script_vars[ self::$sc_id ]['is_admin'] = true;
		   }

		   $name                 = $sc_script_options['script']['name'];
		   $description          = $sc_script_options['script']['description'];
		   $amount               = $sc_script_options['script']['amount'];
		   $success_redirect_url = $sc_script_options['other']['success-redirect-url'];
		   $failure_redirect_url = $sc_script_options['other']['failure-redirect-url'];
		   $currency             = $sc_script_options['script']['currency'];
		   
		   $html .= '<input type="hidden" name="sc-name" value="' . esc_attr( $name ) . '" />';
		   $html .= '<input type="hidden" name="sc-description" value="' . esc_attr( $description ) . '" />';
		   $html .= '<input type="hidden" name="sc-amount" class="sc_amount" value="" />';
		   $html .= '<input type="hidden" name="sc-redirect" value="' . esc_attr( ( ! empty( $success_redirect_url ) ? $success_redirect_url : get_permalink() ) ) . '" />';
		   $html .= '<input type="hidden" name="sc-redirect-fail" value="' . esc_attr( ( ! empty( $failure_redirect_url ) ? $failure_redirect_url : get_permalink() ) ) . '" />';
		   $html .= '<input type="hidden" name="sc-currency" value="' . esc_attr( $currency ) . '" />';
		   $html .= '<input type="hidden" name="stripeToken" value="" class="sc_stripeToken" />';
		   $html .= '<input type="hidden" name="stripeEmail" value="" class="sc_stripeEmail" />';
		   $html .= '<input type="hidden" name="wp-simple-pay" value="1" />';
		   $html .= '<input type="hidden" name="sc-details-placement" value="' . esc_attr( $payment_details_placement ) . '" />';
		   $html .= wp_nonce_field( 'charge_card', 'wp-simple-pay-pro-nonce', '', false );

		   if ( $test_mode == 'true' ) {
			   $html .= '<input type="hidden" name="sc_test_mode" value="true" />'; 
		   }

		   // Add shipping information fields if it is enabled
		   if ( $shipping === 'true' ) {
			   $html .= '<input type="hidden" name="sc-shipping-name" class="sc-shipping-name" value="" />';
			   $html .= '<input type="hidden" name="sc-shipping-country" class="sc-shipping-country" value="" />';
			   $html .= '<input type="hidden" name="sc-shipping-zip" class="sc-shipping-zip" value="" />';
			   $html .= '<input type="hidden" name="sc-shipping-state" class="sc-shipping-state" value="" />';
			   $html .= '<input type="hidden" name="sc-shipping-address" class="sc-shipping-address" value="" />';
			   $html .= '<input type="hidden" name="sc-shipping-city" class="sc-shipping-city" value="" />';
		   }

		   // Add filter for adding html before payment button.
		   $html .= apply_filters( 'sc_before_payment_button', '' );

		   // Add filter for adding icon html inside <button> element preceding button text.
		   $payment_button_icon_html = apply_filters( 'simpay_payment_button_icon_html', '' );

		   // Default output: <button class="sc-payment-btn stripe-button-el"><span>Buy Now</span></button>;
		   $payment_button_html = '<button class="' . $this->get_payment_button_classes( $payment_button_style ) . '">' .
		                          $payment_button_icon_html . '<span>' . $payment_button_label . '</span></button>';

		   $html .= $payment_button_html;

		   $html .= '</form>';

		   $error_count = Shortcode_Tracker::get_error_count();

		   Shortcode_Tracker::reset_error_count();

		   if ( $error_count > 0 && ! isset( $_GET['charge'] ) ) {
			   if ( current_user_can( 'manage_options' ) ) {
				   return Shortcode_Tracker::print_errors();
			   } else {
				   return '';
			   }
		   }

		   // Reset the static counter now in case there are multiple forms on a page
		   $this->total_fields( true );

		   $referer = wp_get_referer();

		   // Also check 'sub_id' here for trials subscriptions that don't pass 'charge'
		   if ( ( ! isset( $_GET['charge'] ) && ! isset( $_GET['error_code'] ) && ! isset( $_GET['sub_id'] ) ) ||
				   ( ( ! ( null === $sc_options->get_setting_value( 'success_redirect_url' ) ) || ! ( null === $sc_options->get_setting_value( 'failure_redirect_url' ) ) ) &&  
				   ( ( $referer !== false && $success_redirect_url != $referer ) && ( $referer !== false && $failure_redirect_url != $referer ) ) )  && ! isset( $_GET['test_mode'] ) )  {
			   
			   return $html;
		   }
		   
		   return '';
	   }
	   
	   /**
		* Function to process [stripe_total] shortcode
		* 
		* @since 2.0.0
		*/
	   function stripe_total( $attr ) {
	
			global $sc_options, $sc_script_options;

			static $counter = 1;

			$attr = shortcode_atts( array(
							'label' => ( ! null === $sc_options->get_setting_value( 'stripe_total_label' ) ? $sc_options->get_setting_value( 'stripe_total_label' ) : __( 'Total Amount:', 'stripe' ) )
						), $attr, 'stripe_total' );
			
			$label = $attr['label'];

			Shortcode_Tracker::add_new_shortcode( 'stripe_total_' . $counter, 'stripe_total', $attr, false );

			$currency = strtoupper( $sc_script_options['script']['currency'] );
			$stripe_amount = $sc_script_options['script']['amount'];

			$attr['currency'] = $currency;
			$attr['amount']   = $stripe_amount;

			$html = $label . ' ';
			$html .= '<span class="' . apply_filters( 'sc_total_amount_class', 'sc-total-amount' ) . '">';

			// USD only: Show dollar sign on left of amount.
			if ( $currency === 'USD' ) {
				$html .= '$';
			}

			$html .= Stripe_Checkout_Misc::to_formatted_amount( $stripe_amount, $currency );

			// Non-USD: Show currency on right of amount.
			if ( $currency !== 'USD' ) {
				$html .= ' ' . $currency;
			}

			$html .= '</span>'; //sc-total-amount

			$args = $this->get_args( '', $attr );
			$counter++;

			return '<div class="' . apply_filters( 'sc_form_group_class' , 'sc-form-group' ) . '">' . apply_filters( 'sc_stripe_total', $html, $args ) . '</div>';
		}
		
		/**
		 * Render code for [stripe_coupon]
		 * 
		 * @since 2.0.0
		 */
		function stripe_coupon( $attr ) {
			global $sc_options;

			static $counter = 1;

			if ( Shortcode_Tracker::shortcode_exists_current( 'stripe_coupon' ) ) {
				Shortcode_Tracker::update_error_count();

				if ( current_user_can( 'manage_options' ) ) {
					Shortcode_Tracker::add_error_message( __( 'Admin note: Only one coupon code per form is allowed.', 'stripe' ) );
				} else {
					return '';
				}
			}

			$attr = shortcode_atts( array(
				'label'              => ( ! null === $sc_options->get_setting_value( 'sc_coup_label' ) ? $sc_options->get_setting_value( 'sc_coup_label' ) : '' ),
				'placeholder'        => '',
				'apply_button_style' => ( ! ( null === $sc_options->get_setting_value( 'sc_coup_apply_button_style' ) ) && $sc_options->get_setting_value( 'sc_coup_apply_button_style' ) == 'stripe' ? 'stripe' : '' )
			), $attr, 'stripe_coupon' );
			
			$label              = $attr['label'];
			$placeholder        = $attr['placeholder'];
			$apply_button_style = $attr['apply_button_style'];
			
			Shortcode_Tracker::add_new_shortcode( 'stripe_coupon_' . $counter, 'stripe_coupon', $attr, false );

			$html = ( ! empty( $label ) ? '<label for="sc-coup-coupon-' . $counter . '">' . $label . '</label>' : '' );
			$html .= '<div class="' . apply_filters( 'sc_coup_coupon_container_class', 'sc-coup-coupon-container' ) . '">';
			$html .= '<input type="text" class="' . apply_filters( 'sc_form_control_class', 'sc-form-control' ) . ' ' . apply_filters( 'sc_coup_coupon_class', 'sc-coup-coupon' ) .
				'" id="sc-coup-coupon-' . $counter . '" name="sc_coup_coupon" placeholder="' . esc_attr( $placeholder ) . '" ';

			// Make Parsley JS validation ignore this field entirely.
			$html .= 'data-parsley-ui-enabled="false">';

			// Store valid applied coupon code in hidden field.
			$html .= '<input type="hidden" class="sc-coup-coupon-applied" name="sc_coup_coupon_applied" />';

			// Apply button (using "stripe" style if indicated).
			$html .= '<button class="sc-coup-apply-btn' . ( $apply_button_style == 'stripe' ? ' stripe-button-el' : '' ) . '"><span>' . __( 'Apply', 'stripe' ) . '</span></button>';

			$html .= '</div>'; //sc-coup-coupon-container

			// Loading indicator and validation message.
			$html .= '<div class="sc-coup-loading"><img src="' . SC_DIR_URL . 'assets/images/loading.gif" /></div>';
			$html .= '<div class="sc-coup-validation-message"></div>';

			// Success message and removal link.
			$html .= '<div class="sc-coup-success-row">';
			$html .= '<span class="sc-coup-success-message"></span>';
			$html .= ' <span class="sc-coup-remove-coupon">(<a href="#">' . __( 'remove', 'stripe' ) . '</a>)</span>';
			$html .= '</div>'; //sc-coup-success-row

			$args = $this->get_args( '', $attr, $counter );

			$counter++;
			$this->total_fields();

			return '<div class="' . apply_filters( 'sc_form_group_class' , 'sc-form-group' ) . '">' . apply_filters( 'sc_stripe_coupon', $html, $args ) . '</div>';
		}
		
		/**
		 * Shortcode to output a custom text field [stripe_text]
		 * 
		 * @since 2.0.0
		 */
		function stripe_text( $attr ) {
	
			static $counter = 1;

			$attr = shortcode_atts( array(
							'id'          => '',
							'label'       => '',
							'placeholder' => '',
							'required'    => 'false',
							'default'     => '',
							'multiline'   => 'false',
							'rows'        => '5',
							'is_quantity' => 'false'
						), $attr, 'stripe_text' );
			
			$id          = $attr['id'];
			$label       = $attr['label'];
			$placeholder = $attr['placeholder'];
			$required    = $attr['required'];
			$default     = $attr['default'];
			$multiline   = $attr['multiline'];
			$rows        = $attr['rows'];
			$is_quantity = $attr['is_quantity'];

			Shortcode_Tracker::add_new_shortcode( 'stripe_text_' . $counter, 'stripe_text', $attr, false );

			// Check for ID and if it doesn't exist then we will make our own
			if ( $id == '' ) {
				$id = 'sc_cf_text_' . $counter;
			}

			$quantity_html  = ( ( 'true' == $is_quantity ) ? ' data-sc-quantity="true" data-parsley-type="integer" data-parsley-min="1" ' : '' );
			$quantity_class = ( ( 'true' == $is_quantity ) ? ' sc-cf-quantity' : '' );

			$html = ( ! empty( $label ) ? '<label for="' . esc_attr( $id ) . '">' . $label . '</label>' : '' );

			if ( $multiline === 'true' ) {
				$html .= '<textarea rows="' . esc_attr( $rows ) . '" class="' . apply_filters( 'sc_form_control_class', 'sc-form-control' ) . ' ' .
						 apply_filters( 'sc_cf_text_area_class', 'sc-cf-textarea' ) . '" id="' . esc_attr( $id ) . '" ' .
						 'name="sc_form_field[' . $id . ']" placeholder="' . esc_attr( $placeholder ) . '" ' . ( $required === 'true' ? 'required' : '' ) . '>' .
						 esc_textarea( $default ) . '</textarea>';
			} else {
				$html .= '<input type="text" value="' . esc_attr( $default ) . '" class="' . apply_filters( 'sc_form_control_class', 'sc-form-control' ) . ' ' .
						 apply_filters( 'sc_cf_text_class', 'sc-cf-text' ) . $quantity_class . '" id="' . esc_attr( $id ) . '" ' .
						 'name="sc_form_field[' . $id . ']" placeholder="' . esc_attr( $placeholder ) . '" ' . ( $required === 'true' ? 'required ' : '' ) . $quantity_html . '>';
			}

			$args = $this->get_args( $id, $attr, $counter );

			// Increment static counter
			$counter++;
			$this->total_fields();

			return '<div class="' . apply_filters( 'sc_form_group_class' , 'sc-form-group' ) . '">' . apply_filters( 'sc_stripe_text', $html, $args ) . '</div>';
		}
		
		/**
		 * Shortcode to output a date field - [stripe_date]
		 * 
		 * @since 2.0.0
		 */
		function stripe_date( $attr ) {
	
			static $counter = 1;

			$attr = shortcode_atts( array(
							'id'          => '',
							'label'       => '',
							'placeholder' => '',
							'required'    => 'false',
							'default'     => ''
						), $attr, 'stripe_date' );
			
			$id          = $attr['id'];
			$label       = $attr['label'];
			$placeholder = $attr['placeholder'];
			$required    = $attr['required'];
			$default     = $attr['default'];

			Shortcode_Tracker::add_new_shortcode( 'stripe_date_' . $counter, 'stripe_date', $attr, false );

			// Check for ID and if it doesn't exist then we will make our own
			if ( $id == '' ) {
				$id = 'sc_cf_date_' . $counter;
			}

			$html = ( ! empty( $label ) ? '<label for="' . esc_attr( $id ) . '">' . $label . '</label>' : '' );

			// Include inline Parsley JS validation data attributes.
			// Parsley doesn't have date validation built-in, so add as custom validator using Moment JS.
			$html .= '<input type="text" value="' . esc_attr( $default ) . '" class="' . apply_filters( 'sc_form_control_class', 'sc-form-control' ) . ' ' .
						apply_filters( 'sc_cf_date_class', 'sc-cf-date' ) . '" name="sc_form_field[' . $id . ']" ';

			$html .= 'id="' . esc_attr( $id ) . '" placeholder="' . esc_attr( $placeholder ) . '" ';
			$html .= ( ( $required === 'true') ? 'required' : '' ) . ' data-parsley-required-message="Please select a date.">';

			$args = $this->get_args( $id, $attr, $counter );

			// Increment static counter
			$counter++;
			$this->total_fields();

			return '<div class="' . apply_filters( 'sc_form_group_class' , 'sc-form-group' ) . '">' . apply_filters( 'sc_stripe_date', $html, $args ) . '</div>';
		}
	   
		/**
		 * Shortcode to output a checkbox - [stripe_checkbox]
		 * 
		 * @since 2.0.0
		 */
		function stripe_checkbox( $attr ) {
	
			static $counter = 1;

			$attr = shortcode_atts( array(
							'id'         => '',
							'label'      => '',
							'required'   => 'false',
							'sub_toggle' => 'false',
							'default'    => 'false'
						), $attr, 'stripe_date' );
			
			$id         = $attr['id'];
			$label      = $attr['label'];
			$required   = $attr['required'];
			$default    = $attr['default'];
			$sub_toggle = $attr['sub_toggle'];

			Shortcode_Tracker::add_new_shortcode( 'stripe_checkbox_' . $counter, 'stripe_checkbox', $attr, false );

			// Check for ID and if it doesn't exist then we will make our own
			if ( $id == '' ) {
				$id = 'sc_cf_checkbox_' . $counter;
			}

			// If this is a toggle for subs then
			if ( $sub_toggle === 'true' ) {
				$id = 'sc_sub_toggle_' . $counter;
			}

			$checked  = ( ( $default === 'true' || $default === 'checked' ) ? 'checked' : '' );

			// Put <input type="checkbox"> inside of <lable> like Bootstrap 3.
			$html = '<label>';

			$html .= '<input type="checkbox" id="' . esc_attr( $id ) . '" class="' . apply_filters( 'sc_cf_checkbox_class', 'sc-cf-checkbox' ) .'" name="sc_form_field[' . esc_attr( $id) . ']" ';
			$html .= ( ( $required === 'true' ) ? 'required' : '' ) . ' ' . $checked . ' value="Yes" ';

			// Point to custom container for errors as checkbox fields aren't automatically placing it in the right place.
			$html .= 'data-parsley-errors-container="#sc_cf_checkbox_error_' . $counter . '">';

			// Actual label text.
			$html .= $label;

			$html .= '</label>';

			// Hidden field to hold a value to pass to Stripe payment record.
			$html .= '<input type="hidden" id="' . esc_attr( $id ) . '_hidden" class="sc-cf-checkbox-hidden" name="sc_form_field[' .
					esc_attr( $id ) . ']" value="' . ( 'true' === $default || 'checked' === $default ? 'Yes' : 'No' ) . '">';
			
			// Custom validation errors container for checkbox fields.
			// Needs counter ID specificity to match input above.
			$html .= '<div id="sc_cf_checkbox_error_' . $counter . '"></div>';

			$args = $this->get_args( $id, $attr, $counter );

			// Incrememnt static counter
			$counter++;
			$this->total_fields();

			return '<div class="' . apply_filters( 'sc_form_group_class' , 'sc-form-group' ) . '">' . apply_filters( 'sc_stripe_checkbox', $html, $args ) . '</div>';
		}
		
		/**
		 * Shortcode to output a number box - [stripe_number]
		 * 
		 * @since 2.0.0
		 */
		function stripe_number( $attr ) {

			static $counter = 1;

			$attr = shortcode_atts( array(
							'id'          => '',
							'label'       => '',
							'required'    => 'false',
							'placeholder' => '',
							'default'     => '',
							'min'         => '',
							'max'         => '',
							'step'        => '',
							'is_quantity' => 'false'
						), $attr, 'stripe_date' );
			
			$id          = $attr['id'];
			$label       = $attr['label'];
			$required    = $attr['required'];
			$placeholder = $attr['placeholder'];
			$default     = $attr['default'];
			$min         = $attr['min'];
			$max         = $attr['max'];
			$step        = $attr['step'];
			$is_quantity = $attr['is_quantity'];

			Shortcode_Tracker::add_new_shortcode( 'stripe_number_' . $counter, 'stripe_number', $attr, false );

			// Check for ID and if it doesn't exist then we will make our own
			if ( $id == '' ) {
				$id = 'sc_cf_number_' . $counter;
			}

			$quantity_html  = ( ( 'true' == $is_quantity ) ? 'data-sc-quantity="true" data-parsley-min="1" ' : '' );
			$quantity_class = ( ( 'true' == $is_quantity ) ? ' sc-cf-quantity' : '' );

			$min  = ( ! empty( $min ) ? 'min="' . $min . '" ' : '' );
			$max  = ( ! empty( $max ) ? 'max="' . $max . '" ' : '' );
			$step = ( ! empty( $step ) ? 'step="' . $step . '" ' : '' );

			$html = ( ! empty( $label ) ? '<label for="' . esc_attr( $id ) . '">' . $label . '</label>' : '' );

			// No Parsley JS number validation yet as HTML5 number type takes care of it.
			$html .= '<input type="number" data-parsley-type="number" class="' . apply_filters( 'sc_form_control_class', 'sc-form-control' ) .
					' ' . apply_filters( 'sc_cf_number_class', 'sc-cf-number' ) . $quantity_class . '" id="' . esc_attr( $id ) . '" name="sc_form_field[' . $id . ']" ';

			$html .= 'placeholder="' . esc_attr( $placeholder ) . '" value="' . esc_attr( $default ) . '" ';
			$html .= $min . $max . $step . ( ( $required === 'true' ) ? 'required ' : '' ) . $quantity_html . '>';

			$args = $this->get_args( $id, $attr, $counter );

			// Incrememnt static counter
			$counter++;
			$this->total_fields();

			return '<div class="' . apply_filters( 'sc_form_group_class' , 'sc-form-group' ) . '">' . apply_filters( 'sc_stripe_number', $html, $args ) . '</div>';
		}
		
		/**
		 * Function to add the custom user amount textbox via shortcode - [stripe_amount]
		 * 
		 * @since 2.0.0
		 */
		function stripe_amount( $attr ) {
			global $sc_script_options, $sc_options;

			static $counter = 1;

			$attr = shortcode_atts( array(
							'label'       => ( ! null === $sc_options->get_setting_value( 'sc_uea_label' ) ? $sc_options->get_setting_value( 'sc_uea_label' ) : '' ),
							'placeholder' => '',
							'default'     => ''
						), $attr, 'stripe_amount' );
						
			$label       = $attr['label'];
			$placeholder = $attr['placeholder'];
			$default     = $attr['default'];

		   Shortcode_Tracker::add_new_shortcode( 'stripe_amount_' . $counter, 'stripe_amount', $attr, false );

			$currency = strtoupper( $sc_script_options['script']['currency'] );

			$attr['currency'] = $currency;

			$html  = '';

			$html .= ( ! empty( $label ) ? '<label for="sc_uea_custom_amount_' . $counter . '">' . $label . '</label>' : '' );
			$html .= '<div class="sc-uea-container">';

			$currency_args['before'] = ( $currency === 'USD' ? '$' : '' );
			$currency_args['after']  = ( $currency === 'USD' ? '' : $currency );
			$currency_args           = apply_filters( 'sc_uea_currency', $currency_args );

			$currency_before = $currency_args['before'];
			$currency_after  = $currency_args['after'];

			if ( ! empty( $currency_before ) ) {
				$html .= '<span class="' . apply_filters( 'sc_uea_currency_class', 'sc-uea-currency' ) . ' ' .
					apply_filters( 'sc_uea_currency_before_class', 'sc-uea-currency-before' ) . '">' . $currency_before . '</span> ';
			}

			// Stripe minimum amount allowed is 50 currency units.
			$stripe_minimum_amount = 50;

			// Get amount to validate based on currency.
			$converted_minimum_amount = Stripe_Checkout_Misc::to_decimal_amount( $stripe_minimum_amount, $currency );

			// Non-USD: Format and show currency code on right.
			$minimum_amount_validation_more_than = $converted_minimum_amount . ' ' . $currency;

			// USD only: Show "50 cents" instead of "50" + currency code.
			if ( $currency === 'USD' ) {
				$minimum_amount_validation_more_than = $stripe_minimum_amount . ' cents';
			}

			$minimum_amount_validation_msg = sprintf( __( 'Please enter an amount equal to or more than %s. Do not include symbols or thousands separators.', 'stripe' ),
				$minimum_amount_validation_more_than );

			$minimum_amount_validation_msg = apply_filters( 'sc_stripe_amount_validation_msg', $minimum_amount_validation_msg, $stripe_minimum_amount, $currency );

			$attr['min_validation_msg'] = $minimum_amount_validation_msg;

			// Include inline Parsley JS validation data attributes.
			// http://parsleyjs.org/doc/index.html#psly-validators-list
			$html .= '<input type="text" class="' . apply_filters( 'sc_form_control_class', 'sc-form-control' ) . ' '
				. apply_filters( 'sc_uea_custom_amount_class', 'sc-uea-custom-amount' ) . '" name="sc_uea_custom_amount" ';
			$html .= 'id="sc_uea_custom_amount_' . $counter . '" value="' . esc_attr( $default ) . '" placeholder="' . esc_attr( $placeholder ) . '" ';

			// TODO Required validation is never hit because of formatting done by accounting.js. Change this behavior?
			$html .= 'required data-parsley-required-message="' . __( 'Please enter an amount.', 'stripe' ) . '" ';

			// Can remove character validation with use of accounting.js now.
			// Would need to convert via accounting.js before validating minimum amount first.
			//$html .= 'data-parsley-type="number" data-parsley-type-message="Please enter a valid amount. Do not include symbols or thousands separators." ';
			$html .= 'data-parsley-min="' . $converted_minimum_amount . '" data-parsley-min-message="' . $minimum_amount_validation_msg . '" ';

			// Point to custom container for errors so we can place the non-USD currencies on the right of the input box.
			$html .= 'data-parsley-errors-container="#sc_uea_custom_amount_errors_' . $counter . '">';

			if ( ! empty( $currency_after ) ) {
				$html .= ' <span class="' . apply_filters( 'sc_uea_currency_class', 'sc-uea-currency' ) . ' ' .
					apply_filters( 'sc_uea_currency_after_class', 'sc-uea-currency-after' ) . '">' . $currency_after . '</span>';
			}

			// Custom validation errors container for UEA.
			// Needs counter ID specificity to match input above.
			$html .= '<div id="sc_uea_custom_amount_errors_' . $counter . '"></div>';

			$html .= '</div>'; //sc-uea-container

			$args = $this->get_args( '', $attr, $counter );

			$counter++;

			if ( ! isset( $_GET['charge'] ) ) {
				return '<div class="' . apply_filters( 'sc_form_group_class' , 'sc-form-group' ) . '">' . apply_filters( 'sc_stripe_amount', $html, $args ) . '</div>';
			}

			return '';
		}
		
		/**
		 * Shortcode to output a dropdown list - [stripe_dropdown]
		 * 
		 * @since 2.0.0
		 */
		function stripe_dropdown( $attr ) {
	
			static $counter = 1;

			global $sc_script_options;

			$attr = shortcode_atts( array(
							'id'          => '',
							'label'       => '',
							'default'     => '',
							'options'     => '',
							'is_quantity' => 'false',
							'amounts'     => '',
							'is_amount'   => 'false' // For backwards compatibility
						), $attr, 'stripe_dropdown' );

			$id          = $attr['id'];
			$label       = $attr['label'];
			$default     = $attr['default'];
			$options     = $attr['options'];
			$is_quantity = $attr['is_quantity'];
			$amounts     = $attr['amounts'];
			$is_amount   = $attr['is_amount'];

			Shortcode_Tracker::add_new_shortcode( 'stripe_dropdown_' . $counter, 'stripe_dropdown', $attr, false );

			// Check for ID and if it doesn't exist then we will make our own
			if ( $id == '' ) {
				$id = 'sc_cf_select_' . $counter;
			}

			$quantity_html  = ( ( 'true' == $is_quantity ) ? 'data-sc-quantity="true" ' : '' );
			$quantity_class = ( ( 'true' == $is_quantity ) ? ' sc-cf-quantity' : '' );

			$amount_class = ( ! empty( $amounts ) || $is_amount == 'true' ? ' sc-cf-amount' : '' );

			$options = explode( ',', $options );

			if ( ! empty( $amounts ) ) {
				$amounts = explode( ',', str_replace( ' ', '', $amounts ) );

				if ( count( $options ) != count( $amounts ) ) {
					Shortcode_Tracker::update_error_count();

					if ( current_user_can( 'manage_options' ) ) {
						Shortcode_Tracker::add_error_message( '<h6>' . __( 'Admin note: Your number of options and amounts are not equal.', 'stripe' ) . '</h6>' );
					} else {
						return '';
					}
				}
			}

			if ( $is_amount == 'true' ) {
				if ( current_user_can( 'manage_options' ) ) {
					echo '<h6>' . sprintf( __( 'Admin note: The "is_amount" attribute is deprecated and will be removed in an upcoming release. Please use the new "amounts" attribute instead. %s', 'stripe' ),
							'<a href="' . SC_WEBSITE_BASE_URL . 'docs/shortcodes/stripe-custom-fields/" target="_blank">' . __( 'See Documentation', 'stripe' ) . '</a>' ) . '</h6>';
				}
			}

			$html = ( ! empty( $label ) ? '<label for="' . esc_attr( $id ) . '">' . $label . '</label>' : '' );
			$html .= '<select class="' . apply_filters( 'sc_form_control_class', 'sc-form-control' ) . ' ' . apply_filters( 'sc_cf_dropdown_class', 'sc-cf-dropdown' ) .
				$quantity_class . $amount_class . '" id="' . esc_attr( $id ) . '" name="sc_form_field[' . esc_attr( $id ) . ']" ' . $quantity_html . '>';

			$i = 1;
			foreach ( $options as $option ) {

				$option = trim( $option );
				$value = $option;

				if ( $is_amount == 'true' ) {

					$currency = strtoupper( $sc_script_options['script']['currency'] );
					$amount = Stripe_Checkout_Misc::to_formatted_amount( $option, $currency );

					if ( $currency == 'USD' ) {
						$option_name = '$' . $amount;
					} else {
						$option_name = $amount . ' ' . $currency;
					}

				} else if ( ! empty( $amounts ) ) {
					$value = $amounts[$i - 1];
				}

				if ( empty( $default ) ) {
					$default = $option;
				}

				if ( $default == $option  && $is_quantity != 'true' && ! empty( $amounts ) ) {
					$sc_script_options['script']['amount'] = $value;
				}

				$html .= '<option value="' . ( isset( $option_name ) ? $option_name : $option ) . '" ' . ( $default == $option ? 'selected' : '' ) . ' data-sc-price="' . esc_attr( $value ) . '">' . ( isset( $option_name ) ? $option_name : $option ) . '</option>';
				$i++;
			}

			$html .= '</select>';

			$args = $this->get_args( $id, $attr, $counter );

			// Incrememnt static counter
			$counter++;
			$this->total_fields();

			return '<div class="' . apply_filters( 'sc_form_group_class' , 'sc-form-group' ) . '">' . apply_filters( 'sc_stripe_dropdown', $html, $args ) . '</div>';
		}
		
		/**
		 * Shortcode to output a number box - [stripe_radio]
		 * 
		 * @since 2.0.0
		 */
		function stripe_radio( $attr ) {
	
			static $counter = 1;

			global $sc_script_options;

			$attr = shortcode_atts( array(
							'id'          => '',
							'label'       => '',
							'default'     => '',
							'options'     => '',
							'is_quantity' => 'false',
							'amounts'     => '',
							'is_amount'   => 'false'  // For backwards compatibility
						), $attr, 'stripe_radio' );
			
			$id          = $attr['id'];
			$label       = $attr['label'];
			$default     = $attr['default'];
			$options     = $attr['options'];
			$is_quantity = $attr['is_quantity'];
			$amounts     = $attr['amounts'];
			$is_amount   = $attr['is_amount'];

			Shortcode_Tracker::add_new_shortcode( 'stripe_radio_' . $counter, 'stripe_radio', $attr, false );

			// Check for ID and if it doesn't exist then we will make our own
			if ( $id == '' ) {
				$id = 'sc_cf_radio_' . $counter;
			}

			$options = explode( ',', $options );

			if ( ! empty( $amounts ) ) {
				$amounts = explode( ',', str_replace( ' ', '', $amounts ) );

				if ( count( $options ) != count( $amounts ) ) {
					Shortcode_Tracker::update_error_count();

					if ( current_user_can( 'manage_options' ) ) {
						Shortcode_Tracker::add_error_message( '<h6>' . __( 'Admin note: Your number of options and amounts are not equal.', 'stripe' ) . '</h6>' );
					} else {
						return '';
					}
				}
			}

			if ( $is_amount == 'true' ) {
				if ( current_user_can( 'manage_options' ) ) {
					echo '<h6>' . sprintf( __( 'Admin note: The "is_amount" attribute is deprecated and will be removed in an upcoming release. Please use the new "amounts" attribute instead. %s', 'stripe' ),
							'<a href="' . SC_WEBSITE_BASE_URL . 'docs/shortcodes/stripe-custom-fields/" target="_blank">' . __( 'See Documentation', 'stripe' ) . '</a>' ) . '</h6>';
				}
			}

			$quantity_html  = ( ( 'true' == $is_quantity ) ? 'data-sc-quantity="true" ' : '' );
			$quantity_class = ( ( 'true' == $is_quantity ) ? ' sc-cf-quantity' : '' );

			$amount_class = ( ! empty( $amounts ) || $is_amount == 'true' ? ' sc-cf-amount' : '' );

			$html = ( ! empty( $label ) ? '<label>' . $label . '</label>' : '' );

			$html .= '<div class="sc-radio-group">';

			$i = 1;
			foreach ( $options as $option ) {

				$option = trim( $option );
				$value = $option;

				if ( empty( $default ) ) {
					$default = $option;
				}

				if ( $is_amount == 'true' ) {

					$currency = strtoupper( $sc_script_options['script']['currency'] );
					$amount = Stripe_Checkout_Misc::to_formatted_amount( $option, $currency );

					if ( $currency == 'USD' ) {
						$option_name = '$' . $amount;
					} else {
						$option_name = $amount . ' ' . $currency;
					}
				} else if ( ! empty( $amounts ) ) {
					$value = $amounts[$i - 1];
				}

				if ( $default == $option  && $is_quantity != 'true' && ! empty( $amounts ) ) {
					$sc_script_options['script']['amount'] = $value;
				}

				// Don't use built-in checked() function here for now since we need "checked" in double quotes.
				$html .= '<label title="' . esc_attr( $option ) . '">';
				$html .= '<input type="radio" name="sc_form_field[' . esc_attr( $id ) . ']" value="' . ( isset( $option_name ) ? $option_name : $option ) . '" ' .
						'data-sc-price="' . esc_attr( $value ) . '" ' . ( $default == $option ? 'checked="checked"' : '' ) . 
						' class="' . esc_attr( $id ) . '_' . $i . $quantity_class . $amount_class . '" data-parsley-errors-container=".' . apply_filters( 'sc_form_group_class' , 'sc-form-group' ) . '" ' . $quantity_html . '>';
				$html .= '<span>' . ( isset( $option_name ) ? $option_name : $option ) . '</span>';
				$html .= '</label>';

				$i++;
			}

			$html .= '</div>'; //sc-radio-group

			$attr['currency'] = strtoupper( $sc_script_options['script']['currency'] );

			$args = $this->get_args( $id, $attr, $counter );

			// Incrememnt static counter
			$counter++;
			$this->total_fields();

			return '<div class="' . apply_filters( 'sc_form_group_class' , 'sc-form-group' ) . '">' . apply_filters( 'sc_stripe_radio', $html, $args ) . '</div>';
		}
		
		/*
		 * Calculates number of total fields added and returns a message if it is greater than the limit
		 * 
		 * @since 2.0.8
		 */
		public function total_fields( $reset = false ) {
			static $counter = 0;

			if ( $reset == true ) {
				$counter = 0;
				return '';
			}

			$counter++;

			if ( $counter > 20 ) {
				$counter = 0;

				Shortcode_Tracker::update_error_count();

				if ( current_user_can( 'manage_options' ) ) {
					echo '<p>' . __( 'Admin note: You have entered more fields than are currently allowed by Stripe. Please limit your fields to 20 or less.', 'stripe' ) . '</p>';
				}
			}
		}
		
		/**
		 * Function to set the id of the args array and return the modified array
		 */
		public function get_args( $id = '', $args = array(), $counter = '' ) {
	
			if ( ! empty( $id ) ) {
				$args['id'] = $id;
			}

			if ( ! empty( $counter ) ) {
				$args['unique_id'] = $counter;
			}

			return $args;
		}

		/**
		 * Return the current sc_id value
		 */
		public static function get_sc_id() {
			return self::$sc_id;
		}

		// Helper method for adding custom CSS classes to checkout form.
		public function get_form_classes() {
			// Set default class.
			$classes   = array();
			$classes[] = 'sc-checkout-form';

			// Allow filtering of classes and then return what's left.
			$classes = apply_filters( 'simpay_form_class', $classes );

			return trim( implode( ' ', array_map( 'trim', array_map( 'sanitize_html_class', array_unique( $classes ) ) ) ) );
		}

		// Helper method for adding custom CSS classes to payment button.
		public function get_payment_button_classes( $payment_button_style ) {
			// Set default class from plugin.
			$classes   = array();
			$classes[] = 'sc-payment-btn';

			// Also add default CSS class from Stripe unless option set to "none".
			if ( 'none' != $payment_button_style ) {
				$classes[] = 'stripe-button-el';
			}

			// Allow filtering of classes and then return what's left.
			$classes = apply_filters( 'simpay_payment_button_class', $classes );

			return trim( implode( ' ', array_map( 'trim', array_map( 'sanitize_html_class', array_unique( $classes ) ) ) ) );
		}
		
	    /**
		 * Return an instance of this class.
		 *
		 * @since     1.0.0
		 *
		 * @return    object    A single instance of this class.
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}
	}
}
