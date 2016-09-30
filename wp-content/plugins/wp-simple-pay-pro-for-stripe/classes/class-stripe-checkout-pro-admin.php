<?php

/**
 * Admin class - SP Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Stripe_Checkout_Pro_Admin' ) ) {

	class Stripe_Checkout_Pro_Admin extends Stripe_Checkout_Admin {

		// Class instance variable
		public static $instance = null;

		/**
		 * Class constructor
		 */
		private function __construct() {

			// Set the admin tabs
			add_filter( 'sc_admin_tabs', array( $this, 'set_admin_tabs' ) );

			// Set admin tab content
			add_action( 'sc_admin_tab_content', array( $this, 'set_admin_tab_content' ) );

			// Add plugin listing "Settings" action link.
			add_filter( 'plugin_action_links_' . plugin_basename( SC_DIR_PATH_PRO . 'stripe-checkout-pro.php' ), array(
				$this,
				'settings_link',
			) );


			add_action( 'sc_settings_tab_default', array( $this, 'pro_settings_tab_default' ) );

			// set the default settings for Pro
			add_action( 'sc_admin_defaults', array( $this, 'set_admin_defaults' ) );
		}

		/**
		 * Set the default options for a new install
		 */
		public function set_admin_defaults() {
			global $sc_options;

			$sc_options->add_setting( 'payment_button_style', 'stripe' );
			$sc_options->add_setting( 'sc_coup_apply_button_style', 'none' );
		}

		/**
		 * Add additional pro settings to default tab
		 */
		public function pro_settings_tab_default() {
			require_once( SC_DIR_PATH_PRO . 'views/admin-pro-tab-default.php' );
		}

		/**
		 * Set the tabs in the admin area
		 */
		public function set_admin_tabs( $tabs ) {

			$tabs['license-keys'] = __( 'Licenses', 'stripe' );

			return $tabs;
		}

		public function set_admin_tab_content() {
			global $sc_options;

			$sc_options->load_template( SC_DIR_PATH_PRO . 'views/admin-pro-tab-license-keys.php' );
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
