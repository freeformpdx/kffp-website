<?php

/**
 * Extended functions class - SP Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Stripe_Checkout_Pro_Functions' ) ) {
	class Stripe_Checkout_Pro_Functions extends Stripe_Checkout_Functions {

		// class instance
		protected static $instance = null;

		// Stripe token
		protected static $token = false;

		/*
		 * Class constructor
		 */
		private function __construct() {

			// We only want to run the charge if the Token is set
			if ( isset( $_POST['stripeToken'] ) && isset( $_POST['wp-simple-pay'] ) ) {
				self::$token = true;
				add_action( 'init', array( $this, 'charge_card' ) );
			}

			parent::load_library();

			add_filter( 'the_content', array( $this, 'show_payment_details' ), 11 );

			add_filter( 'sc_payment_details_error', array( $this, 'default_error_html' ), 9, 2 );

			add_filter( 'sc_meta_values', array( $this, 'add_shipping_meta' ) );

			add_action( 'wp_ajax_scp_get_coupon', array( $this, 'coup_ajax_check' ) );
			add_action( 'wp_ajax_nopriv_scp_get_coupon', array( $this, 'coup_ajax_check' ) );

			add_filter( 'sc_meta_values', array( $this, 'add_coupon_meta' ) );

			add_filter( 'sc_meta_values', array( $this, 'sc_cf_checkout_meta' ) );
		}

		/**
		 * Function to show the payment details after the purchase
		 *
		 * @since 2.0.0
		 */
		public static function show_payment_details( $content ) {

			$details_placement = ( isset( $_GET['details_placement'] ) ? $_GET['details_placement'] : 'above' );

			// Since this is a GET query arg I reset it here in case someone tries to submit it again with their own string written in the URL.
			// This helps ensure it can only be set to below or above.
			$details_placement = ( $details_placement == 'below' ? 'below' : 'above' );

			$is_above = ( $details_placement == 'below' ? 0 : 1 );

			$charge_response = null;

			if ( in_the_loop() && is_main_query() ) {
				global $sc_options;

				$html = '';

				$test_mode = ( isset( $_GET['test_mode'] ) ? 'true' : 'false' );

				parent::set_key( $test_mode );

				// PRO ONLY: Check for error code.
				if ( isset( $_GET['error_code'] ) ) {

					if ( isset( $_GET['charge'] ) ) {
						$charge = esc_html( $_GET['charge'] );
					} else {
						$charge = '';
					}

					if ( $is_above ) {
						$content = apply_filters( 'sc_payment_details_error', $html, $charge ) . $content;
					} else {
						$content = $content . apply_filters( 'sc_payment_details_error', $html, $charge );
					}
				}

				// Successful charge output.
				if ( isset( $_GET['charge'] ) && ! isset( $_GET['charge_failed'] ) ) {

					$charge_id = esc_html( $_GET['charge'] );

					// https://stripe.com/docs/api/php#charges
					$charge_response = \Stripe\Charge::retrieve( $charge_id );

					if ( null === $sc_options->get_setting_value( 'disable_success_message' ) ) {

						$html = '<div class="sc-payment-details-wrap">' . "\n";

						$html .= '<p>' . __( 'Congratulations. Your payment went through!', 'stripe' ) . '</p>' . "\n";
						$html .= '<p>' . "\n";

						if ( ! empty( $charge_response->description ) ) {
							$html .= __( "Here's what you purchased:", 'stripe' ) . '<br/>' . "\n";
							$html .= esc_html( $charge_response->description ) . '<br/>' . "\n";
						}

						if ( isset( $_GET['store_name'] ) && ! empty( $_GET['store_name'] ) ) {
							$html .= __( 'From: ', 'stripe' ) . esc_html( $_GET['store_name'] ) . '<br/>' . "\n";
						}

						$html .= '<br/>' . "\n";
						$html .= '<strong>' . __( 'Total Paid: ', 'stripe' ) . Stripe_Checkout_Misc::to_formatted_amount( $charge_response->amount, $charge_response->currency ) . ' ' . strtoupper( $charge_response->currency ) . '</strong>' . "\n";

						$html .= '</p>' . "\n";

						$html .= '<p>' . sprintf( __( 'Your transaction ID is: %s', 'stripe' ), $charge_response->id ) . '</p>' . "\n";

						$html .= '</div>' . "\n";

						if ( $is_above ) {
							$content = apply_filters( 'sc_payment_details', $html, $charge_response ) . $content;
						} else {
							$content = $content . apply_filters( 'sc_payment_details', $html, $charge_response );
						}

					}

					do_action( 'sc_after_charge', $charge_response );
				}
			}

			return $content;
		}


		/**
		 * Function that will actually charge the customers credit card
		 *
		 * @since 2.0.0
		 */
		public static function charge_card() {

			if ( self::$token && wp_verify_nonce( $_POST['wp-simple-pay-pro-nonce'], 'charge_card' ) ) {
				global $sc_options;

				// Set redirect
				$redirect      = $_POST['sc-redirect'];
				$fail_redirect = $_POST['sc-redirect-fail'];
				$failed        = null;

				// Get the credit card details submitted by the form
				$token             = $_POST['stripeToken'];
				$payment_email     = $_POST['stripeEmail'];
				$amount            = $_POST['sc-amount'];
				$description       = ( isset( $_POST['sc-description'] ) ? $_POST['sc-description'] : '' );
				$store_name        = ( isset( $_POST['sc-name'] ) ? $_POST['sc-name'] : '' );
				$currency          = ( isset( $_POST['sc-currency'] ) ? $_POST['sc-currency'] : '' );
				$test_mode         = ( isset( $_POST['sc_test_mode'] ) ? $_POST['sc_test_mode'] : 'false' );
				$details_placement = ( isset( $_POST['sc-details-placement'] ) ? $_POST['sc-details-placement'] : '' );
				$customer          = null;
				$charge            = array();

				parent::set_key( $test_mode );

				// We allow a spot to hook in, but the hook is responsible for all of the code.
				// If the action is non-existant, then we run a default for the button.
				// Used by subscriptions add-on (overrides base plugin charge process below).
				if ( has_action( 'sc_do_charge' ) ) {

					do_action( 'sc_do_charge' );

				} else {

					try {
						// Init metadata object with filter allowing it to be overridden/added to.
						$meta = apply_filters( 'sc_meta_values', array() );

						// Init customer ID with filter allowing it to be overridden.
						$customer_id = apply_filters( 'sc_customer_id', null );

						// Create new customer unless there's an existing customer ID set through filters.
						if ( empty( $customer_id ) ) {
							$customer = \Stripe\Customer::create( array(
								'email' => $payment_email,
								'card'  => $token,
							) );
						} else {
							$customer = \Stripe\Customer::retrieve( $customer_id );
						}

						$charge_args = array(
							'amount'   => $amount, // amount in cents, again
							'currency' => $currency,
							'customer' => $customer->id,
							'metadata' => $meta,
						);

						if ( ! empty( $description ) ) {
							$charge_args['description'] = $description;
						}

						$charge = \Stripe\Charge::create( $charge_args );

						// Add Stripe charge ID to querystring.
						// From https://developer.wordpress.org/reference/functions/add_query_arg/:
						// Values are expected to be encoded appropriately with urlencode() or rawurlencode().
						$query_args = array(
							'charge'     => $charge->id,
							'store_name' => rawurlencode( $store_name ),
						);

						$failed = false;

					} catch ( \Stripe\Error\Card $e ) {
						// Something else happened, completely unrelated to Stripe

						$redirect = $fail_redirect;

						$failed = true;

						$e = $e->getJsonBody();

						$query_args = array(
							'charge'        => $e['error']['charge'],
							'error_code'    => $e['error']['type'],
							'charge_failed' => true,
						);
					}

					unset( $_POST['stripeToken'] );

					do_action( 'sc_redirect_before', $failed );

					if ( $test_mode == 'true' ) {
						$query_args['test_mode'] = 'true';
					}

					if ( 'below' == $details_placement ) {
						$query_args['details_placement'] = $details_placement;
					}

					self::$token = false;

					$redirect_url = esc_url_raw( add_query_arg( apply_filters( 'sc_redirect_args', $query_args, $charge ), apply_filters( 'sc_redirect', $redirect, $failed ) ) );

					wp_redirect( $redirect_url );

					exit;
				}
			}
		}

		/**
		 * Error message output
		 */
		function default_error_html( $html, $charge ) {

			$html = '<div class="sc-payment-details-wrap sc-payment-details-error">' . "\n";
			$html .= '<p>' . __( 'Sorry, but there has been an error processing your payment.', 'stripe' ) . '</p>' . "\n";

			// Display charge failure message if exists.
			if ( ! empty( $charge ) ) {
				$charge = \Stripe\Charge::retrieve( $charge );
				$html .= '<p>' . $charge->failure_message . '</p>' . "\n";
			}

			// Display base error message if exists to site admin only.
			if ( current_user_can( 'manage_options' ) ) {
				$html .= '<h6>' . __( 'Admin note: Please visit the Logs area in your Stripe dashboard to view error details.', 'stripe' ) . '</h6>' . "\n";
			}

			$html .= '</div>' . "\n";

			return $html;
		}

		/**
		 * Function to handle AJAX request for coupon check
		 *
		 * @since 2.0.0
		 */
		function coup_ajax_check() {

			global $sc_options;

			$json   = '';
			$code   = $_POST['coupon'];
			$amount = $_POST['amount'];

			$json['coupon']['code'] = $code;

			$test_mode = $_POST['test_mode'];

			Stripe_Checkout_Functions::set_key( $test_mode );

			try {
				$coupon = \Stripe\Coupon::retrieve( trim( $code ) );

				if ( ! empty( $coupon->percent_off ) ) {
					$json['coupon']['amountOff'] = $coupon->percent_off;
					$json['coupon']['type']      = 'percent';

					if ( $coupon->percent_off == 100 ) {
						$amount = 0;
					} else {
						$amount = round( ( $amount * ( ( 100 - $coupon->percent_off ) / 100 ) ) );
					}
				} else if ( ! empty( $coupon->amount_off ) ) {
					$json['coupon']['amountOff'] = $coupon->amount_off;
					$json['coupon']['type']      = 'amount';

					$amount = $amount - $coupon->amount_off;

					if ( $amount < 0 ) {
						$amount = 0;
					}
				}

				// Set message to amount now before checking for errors
				$json['success'] = true;
				$json['message'] = $amount;

				if ( $amount < 50 ) {
					$json['success'] = false;
					$json['message'] = __( 'Coupon entered puts the total below the required minimum amount.', 'stripe' );
				}

			} catch ( Exception $e ) {
				// an exception was caught, so the code is invalid
				$json['success'] = false;
				$json['message'] = __( 'Invalid coupon code.', 'stripe' );
			}

			// Return as JSON
			echo json_encode( $json );

			die();
		}

		/*
		 * Function to add coupon meta to dashboard
		 */
		function add_coupon_meta( $meta ) {
			if ( isset( $_POST['sc_coup_coupon_applied'] ) && ! empty( $_POST['sc_coup_coupon_applied'] ) ) {
				$meta['Coupon Code'] = $_POST['sc_coup_coupon_applied'];
			}

			return $meta;
		}

		/**
		 * Function to handle adding the coupon as meta data in Stripe Dashboard
		 *
		 * @since 2.0.0
		 */
		public function add_shipping_meta( $meta ) {
			if ( isset( $_POST['sc-shipping-name'] ) ) {

				// Add Shipping Name as an item
				$meta['Shipping Name'] = $_POST['sc-shipping-name'];

				// Show address on two lines: Address 1 and Address 2 in Stripe dashboard -> payments 
				$meta['Shipping Address 1'] = $_POST['sc-shipping-address'];
				$meta['Shipping Address 2'] = $_POST['sc-shipping-zip'] . ', ' . $_POST['sc-shipping-city'] . ', ' . $_POST['sc-shipping-state'] . ', ' . $_POST['sc-shipping-country'];
			}

			return $meta;
		}

		/**
		 * Send post meta
		 * 
		 * @since 2.0.0
		 */
		function sc_cf_checkout_meta( $meta ) {

			if ( isset( $_POST['sc_form_field'] ) ) {
				foreach ( $_POST['sc_form_field'] as $k => $v ) {
					if ( ! empty( $v ) ) {

						// Truncate metadata key to 40 characters and value to 500 characters.
						// https://stripe.com/docs/api#metadata

						$k = substr( $k, 0, 40 );
						$v = substr( $v, 0, 500 );

						$meta[ $k ] = $v;
					}
				}
			}

			return $meta;
		}

		// Return instance of this class
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}
	}
}
