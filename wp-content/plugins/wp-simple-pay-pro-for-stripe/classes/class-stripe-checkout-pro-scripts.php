<?php

/**
 * Scripts class - SP Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Stripe_Checkout_Scripts' ) ) {

	class Stripe_Checkout_Scripts {

		// class instance variable
		public static $instance = null;

		private $min = '';

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 */
		private function __construct() {

			$this->min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			// Front-end JS/CSS
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_styles' ) );

			// Need to localize shortcode form data in wp_footer for Stripe_Checkout_Shortcodes class.
			// Using wp_enqueue_scripts is too early to read shortcodes in content.
			add_action( 'wp_footer', array( $this, 'localize_shortcode_script' ) );

			// Admin JS/CSS
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		}

		/**
		 * Enqueue Front-end Scripts
		 *
		 * @since 1.0.0
		 */
		public function enqueue_frontend_scripts() {

			global $base_class;

			$js_dir = SC_DIR_URL . 'assets/js/';

			// Localized PHP to JS global vars for front-end
			$localized_frontend_globals = apply_filters( 'simple_pay_global_script_vars', array(

				'ajaxurl'                       => admin_url( 'admin-ajax.php' ),
				'nonce'                         => wp_create_nonce( 'simple_pay_checkout_nonce' ),

				// Load i18n strings here
				'paymentSubmittingButtonLabel'  => __( 'Please wait...', 'stripe' ),
				'couponAmountOffText'           => __( 'off', 'stripe' ),
				'zeroAmountCheckoutButtonLabel' => __( 'Start Now', 'stripe' ),

				// Zero-decimal currencies array (maintained in PHP)
				'zeroDecimalCurrencies'         => Stripe_Checkout_Misc::zero_decimal_currencies(),

				// Set boolean values to string 'true' or 'false' to avoid localization stringifying to '1'.
				'scriptDebug'                   => ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? 'true' : 'false',
			) );

			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'stripe-checkout', 'https://checkout.stripe.com/checkout.js', array(), null, true );

			// Prefix local 3rd party libraries to prevent clashing.
			// Enqueued individually so we can dequeue if already enqueued by another plugin.
			wp_enqueue_script( $base_class->plugin_slug . '-accounting', $js_dir . 'vendor/accounting' . $this->min . '.js', array(), $base_class->version, true );
			wp_enqueue_script( $base_class->plugin_slug . '-parsley', $js_dir . 'vendor/parsley' . $this->min . '.js', array(), $base_class->version, true );
			wp_enqueue_script( $base_class->plugin_slug . '-moment', $js_dir . 'vendor/moment' . $this->min . '.js', array(), $base_class->version, true );
			wp_enqueue_script( $base_class->plugin_slug . '-pikaday', $js_dir . 'vendor/pikaday' . $this->min . '.js', array(), $base_class->version, true );
			wp_enqueue_script( $base_class->plugin_slug . '-pikaday-jquery', $js_dir . 'vendor/pikaday.jquery.js', array(), $base_class->version, true );

			/** Plugin compatibility fixes */

			// Dequeue moment.js if detected from Simple Calendar.
			// TODO Eventually remove reference to moment.js from FullCalendar add-on (removed in 1.0.2).
			if ( ( wp_script_is( 'simcal-moment', 'enqueued' ) ) || ( wp_script_is( 'simcal-fullcal-moment', 'enqueued' ) ) ) {
				wp_dequeue_script( $base_class->plugin_slug . '-moment' );
			}

			// Finally enqueue our main public JS file.
			wp_enqueue_script( $base_class->plugin_slug . '-public', $js_dir . 'pro-public' . $this->min . '.js', array(
				'jquery',
				'stripe-checkout',
			), $base_class->version, true );

			// Localize front-end global vars.
			wp_localize_script( $base_class->plugin_slug . '-public', 'simplePayFrontendGlobals', $localized_frontend_globals );
		}

		/**
		 * Enqueue Front-end Styles
		 *
		 * @since 1.0.0
		 */
		public function enqueue_frontend_styles() {

			global $base_class, $sc_options;

			// First check for disable CSS option
			if ( null !== $sc_options->get_setting_value( 'disable_css' ) ) {
				return;
			}

			$css_dir = SC_DIR_URL . 'assets/css/';

			wp_enqueue_style( 'stripe-checkout-button', 'https://checkout.stripe.com/v3/checkout/button.css', array(), $base_class->version );

			// Prefix local 3rd party libraries to prevent clashing.
			// Enqueued individually so we can dequeue if already enqueued by another plugin.
			wp_enqueue_style( $base_class->plugin_slug . '-pikaday', $css_dir . 'vendor/pikaday' . $this->min . '.css', array(), $base_class->version );
			wp_enqueue_style( $base_class->plugin_slug . '-public-lite', $css_dir . 'shared-public-main' . $this->min . '.css', array(), $base_class->version );

			// Finally enqueue our main public CSS file.
			wp_enqueue_style( $base_class->plugin_slug . '-public', $css_dir . 'pro-public' . $this->min . '.css', array(
				'stripe-checkout-button',
				$base_class->plugin_slug . '-public-lite',
			), $base_class->version );

			wp_enqueue_style( $base_class->plugin_slug . '-public' );
		}

		/**
		 * Localize script vars for processed by shortcodes.
		 *
		 * @since 1.0.0
		 */
		public function localize_shortcode_script() {

			// $script_vars contains all settings for each form.
			global $base_class, $script_vars;

			wp_localize_script( $base_class->plugin_slug . '-public', 'simplePayFormSettings', $script_vars );
		}

		/**
		 * Enqueue Admin Scripts
		 *
		 * @since 1.0.0
		 */
		public function enqueue_admin_scripts() {

			global $base_class;

			$js_dir = SC_DIR_URL . 'assets/js/';

			if ( Stripe_Checkout_Admin::get_instance()->viewing_this_plugin() ) {

				// Localized PHP to JS global vars for admin.
				$localized_admin_globals = array(

					// Load i18n strings here
					'licensesTabSaveButton' => __( 'Save & Activate', 'stripe' ),
					'otherTabsSaveButton'   => __( 'Save Changes', 'stripe' ),

					// Set boolean values to string 'true' or 'false' to avoid localization stringifying to '1'.
					'scriptDebug'           => ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? 'true' : 'false',
				);

				// Prefix local JS libraries to prevent clashing.
				wp_register_script( $base_class->plugin_slug . '-admin-lite', $js_dir . 'shared-admin-main' . $this->min . '.js', array(), $base_class->version, true );

				wp_register_script( $base_class->plugin_slug . '-admin', $js_dir . 'pro-admin' . $this->min . '.js', array(
					'jquery',
					$base_class->plugin_slug . '-admin-lite',
				), $base_class->version, true );

				wp_enqueue_script( $base_class->plugin_slug . '-admin' );

				// Localize admin global vars.
				wp_localize_script( $base_class->plugin_slug . '-admin', 'simplePayAdminGlobals', $localized_admin_globals );
			}
		}

		/**
		 * Enqueue Admin Styles
		 *
		 * @since 1.0.0
		 */
		public function enqueue_admin_styles() {

			global $base_class;

			$css_dir = SC_DIR_URL . 'assets/css/';

			if ( Stripe_Checkout_Admin::get_instance()->viewing_this_plugin() ) {

				wp_register_style( $base_class->plugin_slug . '-admin-lite', $css_dir . 'shared-admin-main' . $this->min . '.css', array(), $base_class->version );

				wp_register_style( $base_class->plugin_slug . '-admin', $css_dir . 'pro-admin' . $this->min . '.css', array(
					$base_class->plugin_slug . '-admin-lite',
				), $base_class->version );

				wp_enqueue_style( $base_class->plugin_slug . '-admin' );
			}
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
