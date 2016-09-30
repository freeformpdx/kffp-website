<?php

/**
 * Upgrade class - SP Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Stripe_Checkout_Upgrade' ) ) {
	class Stripe_Checkout_Upgrade {
		
		/**
		 * Class instance variable.
		 */
		protected static $instance = null;

		/**
		 * Class constructor.
		 */
		private function __construct() {

			global $base_class;

			$old_version = get_option( 'sc_version' );

			if ( ! empty( $old_version ) ) {

				if ( version_compare( $old_version, '2.3.0', '<' ) ) {
					add_action( 'admin_init', array( $this, 'v230_upgrade' ), 11 );
				}

				if ( version_compare( $old_version, '2.3.4', '<' ) ) {
					add_action( 'admin_init', array( $this, 'v234_upgrade' ), 12 );
				}

				if ( version_compare( $old_version, '2.4.0-beta1', '<' ) ) {
					add_action( 'admin_init', array( $this, 'v240_upgrade' ), 13 );
				}
			}

			$new_version = $base_class->version;

			// TODO This option update is not always getting run.
			update_option( 'sc_version', $new_version );
			add_option( 'sc_upgrade_has_run', 1 );
		}

		/**
		 * Run upgrade routine for version 2.4.0.
		 */
		public function v240_upgrade() {
			global $sc_options;

			// Move license key setting name.
			$sc_options->add_setting( 'main_license_key', $sc_options->get_setting_value( 'sc_license_key' ) );
			$sc_options->delete_setting( 'sc_license_key' );

			// Move license status setting name.
			update_option( 'simplepay_main_license_status', get_option( 'sc_license' ) );
			delete_option( 'sc_license' );

			// Save "had upgrade" option in last upgrade routine to run.
			add_option( 'sc_had_upgrade', 1 );

			// Remove other old options now unused.
			delete_option( 'sc_edd_licenses' );
		}

		/**
		 * Run upgrade routine for version 2.3.4
		 */
		public function v234_upgrade() {

			// Remove old options
			delete_option( 'sc_settings_master' );
			delete_option( 'sc_settings_default' );
			delete_option( 'sc_settings_keys' );
			delete_option( 'sc_show_admin_install_notice' );
			delete_option( 'sc_has_run' );
			delete_option( 'sc_upgrade_has_run' );
			delete_option( 'sc_settings_licenses' );
			delete_option( 'sc_licenses' );
		}
		
		/**
		 * Run upgrade routine for version 2.3.0
		 */
		public function v230_upgrade() {
	
			global $sc_options;

			// sc_settings_master holds a merge of all settings arrays tied to the Stripe plugin. This includes any settings that are implemented by users.
			$master = get_option( 'sc_settings_master' );
			
			// We need to manually set these because the old version doesn't necessarily save them if the user hasn't saved any settings
			$apply_button = false;
			$payment_button = false;
			
			if ( ! ( false === $master ) ) {
				// Loop through the old settings and add them to the new structure
				foreach ( $master as $option => $value ) {
					$sc_options->add_setting( $option, $value );

					if ( $option == 'sc_coup_apply_button_style' ) {
						$apply_button = true;
					}

					if ( $option == 'payment_button_style' ) {
						$payment_button = true;
					}
				}

				if ( ! $apply_button ) {
					$sc_options->add_setting( 'sc_coup_apply_button_style', 'none' );
				}

				if ( ! $payment_button ) {
					$sc_options->add_setting( 'payment_button_style', 'stripe' );
				}
			}
			
			$old_licenses = get_option( 'sc_licenses' );
			
			update_option( 'sc_license', $old_licenses );
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
	
	Stripe_Checkout_Upgrade::get_instance();
}
