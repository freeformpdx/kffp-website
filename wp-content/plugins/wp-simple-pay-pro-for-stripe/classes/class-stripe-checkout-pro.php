<?php

/**
 * Main class - SP Pro - Extends shared base class
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Stripe_Checkout_Pro' ) ) {

	class Stripe_Checkout_Pro extends Stripe_Checkout {

		/**
		 * Plugin version, used for cache-busting of style and script file references.
		 *
		 * @since   2.0.0
		 *
		 * @var     string
		 */
		public $version = null;

		/**
		 * Unique identifier for your plugin.
		 *
		 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
		 * match the Text Domain file header in the main plugin file.
		 *
		 * @since    2.0.0
		 *
		 * @var      string
		 */
		public $plugin_slug = 'stripe-checkout-pro';

		/**
		 * Instance of this class.
		 *
		 * @since    2.0.0
		 *
		 * @var      object
		 */
		protected static $instance = null;

		/**
		 * Slug of the plugin screen.
		 *
		 * @since    2.0.0
		 *
		 * @var      string
		 */
		protected $plugin_screen_hook_suffix = null;


		/**
		 * Initialize the plugin by setting localization, filters, and administration functions.
		 *
		 * @since     2.0.0
		 */
		public function __construct() {

			parent::__construct();

			$this->version = SIMPAY_PRO_VERSION;
		}

		/**
		 * Register the settings and load settings class.
		 * Extends SP Lite register_settings().
		 */
		public function register_settings() {
			global $sc_options;

			// We load the extended class here so that it will load all of the class functions all the way back to the base
			$sc_options = new Stripe_Checkout_Pro_Settings_Extended( 'sc_settings' );
		}

		/**
		 * Include required files (admin and frontend).
		 */
		public function includes() {

			parent::includes();

			// Shortcode tracker not needed in Lite, but IS shared with Subscriptions add-on.
			require_once( SC_DIR_PATH_PRO . 'classes/class-mm-shortcode-tracker.php' );

			require_once( SC_DIR_PATH_PRO . 'classes/class-stripe-checkout-pro-functions.php' );
			require_once( SC_DIR_PATH_PRO . 'classes/class-stripe-checkout-pro-scripts.php' );
			require_once( SC_DIR_PATH_PRO . 'classes/class-stripe-checkout-pro-settings-extended.php' );
			require_once( SC_DIR_PATH_PRO . 'classes/class-stripe-checkout-pro-shortcodes.php' );

			// Admin side
			require_once( SC_DIR_PATH_PRO . 'classes/class-stripe-checkout-pro-admin.php' );
			require_once( SC_DIR_PATH_PRO . 'classes/class-stripe-checkout-pro-licenses.php' );
		}

		/**
		 * Get the instance for all the included classes.
		 * Overrides SP Lite init().
		 */
		public function init() {

			Stripe_Checkout_Scripts::get_instance();
			Stripe_Checkout_Shortcodes::get_instance();

			if ( is_admin() ) {
				Stripe_Checkout_Admin::get_instance();

				Stripe_Checkout_Pro_Admin::get_instance();
				Stripe_Checkout_Pro_Licenses::get_instance();

				Stripe_Checkout_Notices::get_instance();
				Stripe_Checkout_System_Status::get_instance();
			} else {
				Stripe_Checkout_Misc::get_instance();
			}

			// Need to leave outside of is_admin check or the AJAX will not work properly
			Stripe_Checkout_Pro_Functions::get_instance();
		}

		/**
		 * Return localized plugin & menu titles.
		 * Overrides SP Lite functions.
		 *
		 * @since     1.0.0
		 *
		 * @return    string
		 */
		public static function get_plugin_title() {
			return __( 'WP Simple Pay Pro for Stripe', 'stripe' );
		}

		public static function get_plugin_menu_title() {
			return __( 'Simple Pay Pro', 'stripe' );
		}

		public static function get_plugin_slug() {
			return self::get_instance()->plugin_slug;
		}

		/**
		 * Return an instance of this class.
		 *
		 * @since     2.0.0
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

		/**
		 * Fired when the plugin is activated.
		 *
		 * @since    2.0.0
		 */
		public static function activate() {
			// Add value to indicate that we should show admin install notice.
			update_option( 'sc_show_admin_install_notice', 1 );
		}
	}
}
