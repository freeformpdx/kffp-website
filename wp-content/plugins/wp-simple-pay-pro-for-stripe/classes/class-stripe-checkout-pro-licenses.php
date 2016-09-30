<?php

/**
 * License class - SP Pro
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Stripe_Checkout_Pro_Licenses' ) ) {

	class Stripe_Checkout_Pro_Licenses {

		private static $instance = null;

		private $version;
		private $license_key;
		private $item_id;
		private $item_name;
		private $author;
		private $api_url;
		private $file;

		/**
		 * Class constructor.
		 */
		private function __construct() {
			global $sc_options;

			$this->version = SIMPAY_PRO_VERSION;

			// EDD software licensing store URL same as base website URL unless set via constant.
			// Used mainly for testing against staging or test store site.
			// Remember to turn OFF WP Engine password protection on staging sites!
			if ( ! defined( 'SIMPAY_EDD_STORE_URL' ) ) {
				define( 'SIMPAY_EDD_STORE_URL', SC_WEBSITE_BASE_URL );
			}

			// Key off item ID (post ID) for this download on server instead of item name.
			// But pass the item name anyhow since this can be toggled with constants on the server.
			// Constant EDD_BYPASS_NAME_CHECK set to true on server.
			// Constant EDD_BYPASS_ITEM_ID_CHECK also set to true on server, but for the auto updater to work.
			// Need item ID of this (base) plugin for child license activation.
			$this->item_id   = 1021;
			$this->item_name = 'WP Simple Pay Pro for Stripe';
			$this->author    = 'Moonstone Media';
			$this->api_url   = SIMPAY_EDD_STORE_URL;
			$this->file      = SC_PLUGIN_FILE;

			// Due to our current plugin structure, at this point the main license key will be saved to the
			// settings arrray, and the main license status will be saved to a main option outside the array.
			$this->license_key = $sc_options->get_setting_value( 'main_license_key' );

			// Setup includes & hooks.
			$this->includes();
			$this->hooks();
		}

		/**
		 * Allow add-ons to retrieve bundle license key from base plugin.
		 */
		public function get_bundle_license_key() {
			return $this->license_key;
		}

		/**
		 * Include the updater class.
		 *
		 */
		private function includes() {
			// Include the EDDSL plugin updater class unmodified from source.
			if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
				require_once SC_DIR_PATH_PRO . 'classes/EDD_SL_Plugin_Updater.php';
			}
		}

		/**
		 * Setup hooks.
		 *
		 */
		private function hooks() {
			// Register EDDSL plugin auto-updater with top priority.
			add_action( 'admin_init', array( $this, 'auto_updater' ), 0 );

			// Activate license on settings save.
			add_action( 'admin_init', array( $this, 'activate_license' ) );

			// Deactivate license key.
			add_action( 'admin_init', array( $this, 'deactivate_license' ) );

			// Display admin notice for invalid or missing license key.
			add_action( 'admin_notices', array( $this, 'invalid_license_key_notice' ) );
		}

		/**
		 * Auto updater.
		 *
		 */
		public function auto_updater() {

			// Check for valid license.
			if ( 'valid' !== get_option( 'simplepay_main_license_status' ) ) {
				return;
			}

			// Setup the updater.
			// Pass in item ID and item name, but don't key off of item name (see notes in constructor).
			// Constant EDD_BYPASS_NAME_CHECK set to true on server.
			$edd_sl_updater = new EDD_SL_Plugin_Updater( $this->api_url, $this->file, array(
				'version'   => $this->version,
				'license'   => $this->license_key,
				'item_id'   => $this->item_id,
				'item_name' => $this->item_name,
				'author'    => $this->author,
			) );
		}

		/**
		 * Activate the bundle license key which in turn activates child license keys.
		 *
		 */
		public function activate_license() {

			// Verify existence of settings array.
			if ( ! isset( $_POST['sc_settings'] ) ) {
				return;
			}

			// Verify nonce for security.
			if ( ! isset( $_REQUEST['simplepay_license_key_nonce'] ) || ! wp_verify_nonce( $_REQUEST['simplepay_license_key_nonce'], 'simplepay_license_key_nonce' ) ) {
				return;
			}

			// Verify the deactivate button was NOT clicked.
			if ( isset( $_POST['simplepay_main_license_deactivate'] ) ) {
				return;
			}

			// If key is blank delete license status & error altogether.
			if ( empty( $_POST['sc_settings']['main_license_key'] ) ) {
				$this->delete_license_status_error_options();

				return;
			}

			// Set the new license key from $_POST child setting value.
			// Sanitize (which also trims) the new license key and use as current license key.
			$new_license_key = sanitize_text_field( $_POST['sc_settings']['main_license_key'] );

			// Reset license status in prep for new activation.
			// TODO Consider not activating if license key hasn't changed here,
			// but don't want to get stuck with inactive site, thus preventing upgrades.
			$this->delete_license_status_error_options();

			/*
			// If license key hasn't changed and is already valid, then don't re-activate.
			if ( $new_license_key !== $this->license_key ) {
				$this->delete_license_status_error_options();
			}

			$license_status = get_option( 'simplepay_main_license_status' );

			if ( is_object( $license_status ) && ( 'valid' === $license_status ) ) {
				return;
			}
			*/

			// At this point set the current license key same as the entered one.
			$this->license_key = $new_license_key;

			// Data to send to the API.
			// Pass in item ID and item name, but don't key off of item name (see notes in constructor).
			// Constant EDD_BYPASS_NAME_CHECK set to true on server.
			$api_params = array(
				'edd_action' => 'activate_license',
				'license'    => $this->license_key,
				'item_id'    => $this->item_id,
				'item_name'  => urlencode( $this->item_name ),
				'url'        => home_url(),
			);

			// Call the API.
			$response = wp_remote_post( $this->api_url, array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params,
			) );

			// Make sure there are no errors.
			if ( is_wp_error( $response ) ) {
				return;
			}

			// Clear transient to tell WP to look for updates.
			// This is same behaviour as instructed to do in EDD SL sample code, but only while testing.
			// See top of EDD_SL_Plugin_Updater.php.
			set_site_transient( 'update_plugins', null );

			// Decode license data.
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			// $license_data->license will be either 'valid' or 'invalid'.
			// Could also check $license_data->success (true or false).
			if ( ! empty( $license_data ) && isset( $license_data->license ) ) {

				$this->activate_bundle_child_licenses( $license_data );
			}
		}

		/**
		 * Activate child license keys of a bundle.
		 */
		private function activate_bundle_child_licenses( $license_data ) {

			// Init license status & error if any here.
			$license_status = $license_data->license;
			$license_error  = ( isset( $license_data->error ) ? $license_data->error : '' );

			// Loop through bundled license keys child collection from custom activation response.
			// Assuming "master" license key is bundle key, but could be one of the child keys.
			if ( isset( $license_data->bundled_license_downloads ) ) {

				foreach ( $license_data->bundled_license_downloads as $child_download ) {

					// This will also attempt to activate the bundle license key, which isn't allowable, but shouldn't hurt anything.
					// Note that site count doesn't always increment correctly.
					$api_params = array(
						'edd_action' => 'activate_license',
						'license'    => ( isset( $child_download->license_key ) ? $child_download->license_key : '' ),
						'item_id'    => ( isset( $child_download->download_id ) ? $child_download->download_id : '' ),
						'item_name'  => ( isset( $child_download->download_name ) ? $child_download->download_name : '' ),
						'url'        => home_url(),
					);

					$response = wp_remote_post( $this->api_url, array(
						'timeout'   => 15,
						'sslverify' => false,
						'body'      => $api_params,
					) );

					if ( is_wp_error( $response ) ) {
						return;
					}

					$child_license_data = json_decode( wp_remote_retrieve_body( $response ) );

					// Store only the status of Pro (base) activation.
					// Match by item_id of this plugin set in the constructor.
					if ( isset( $child_download->download_id ) && ( $this->item_id == $child_download->download_id ) ) {

						if ( ! empty( $child_license_data ) && isset( $child_license_data->license ) ) {

							$license_status = $child_license_data->license;
							$license_error  = ( isset( $child_license_data->error ) ? $child_license_data->error : '' );
						}
					}
				}
			}

			// Save license status & error code.
			update_option( 'simplepay_main_license_status', $license_status );
			update_option( 'simplepay_main_license_error', $license_error );
		}

		/**
		 * Deactivate the bundle license key which in turn deactivates child license keys.
		 * Similar to activate_license.
		 *
		 */
		public function deactivate_license() {

			// Check for deactivate button click.
			if ( ! isset( $_POST['simplepay_main_license_deactivate'] ) ) {
				return;
			}

			if ( ! isset( $_REQUEST['simplepay_license_key_nonce'] ) || ! wp_verify_nonce( $_REQUEST['simplepay_license_key_nonce'], 'simplepay_license_key_nonce' ) ) {
				return;
			}

			// If key is blank we can't deactivate.
			// Go ahead and delete license status & error.
			if ( empty( $_POST['sc_settings']['main_license_key'] ) ) {
				$this->delete_license_status_error_options();

				return;
			}

			$new_license_key = sanitize_text_field( $_POST['sc_settings']['main_license_key'] );

			if ( empty( $new_license_key ) ) {
				return;
			}

			$this->license_key = $new_license_key;

			$api_params = array(
				'edd_action' => 'deactivate_license',
				'license'    => $this->license_key,
				'item_id'    => $this->item_id,
				'item_name'  => urlencode( $this->item_name ),
				'url'        => home_url(),
			);

			$response = wp_remote_post( $this->api_url, array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params,
			) );

			if ( is_wp_error( $response ) ) {
				return;
			}

			set_site_transient( 'update_plugins', null );

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( ! empty( $license_data ) && isset( $license_data->license ) ) {

				$this->deactivate_bundle_child_licenses( $license_data );
			}
		}

		/**
		 * Deactivate child license keys of bundle.
		 * Similar to activate_bundle_child_licenses.
		 */
		private function deactivate_bundle_child_licenses( $license_data ) {
			global $sc_options;

			$license_status = $license_data->license;
			$license_error  = ( isset( $license_data->error ) ? $license_data->error : '' );

			if ( isset( $license_data->bundled_license_downloads ) ) {

				foreach ( $license_data->bundled_license_downloads as $child_download ) {

					$api_params = array(
						'edd_action' => 'deactivate_license',
						'license'    => ( isset( $child_download->license_key ) ? $child_download->license_key : '' ),
						'item_id'    => ( isset( $child_download->download_id ) ? $child_download->download_id : '' ),
						'item_name'  => ( isset( $child_download->download_name ) ? $child_download->download_name : '' ),
						'url'        => home_url(),
					);

					$response = wp_remote_post( $this->api_url, array(
						'timeout'   => 15,
						'sslverify' => false,
						'body'      => $api_params,
					) );

					if ( is_wp_error( $response ) ) {
						return;
					}

					$child_license_data = json_decode( wp_remote_retrieve_body( $response ) );

					if ( isset( $child_download->download_id ) && ( $this->item_id == $child_download->download_id ) ) {

						if ( ! empty( $child_license_data ) && isset( $child_license_data->license ) ) {

							$license_status = $child_license_data->license;
							$license_error  = ( isset( $child_license_data->error ) ? $child_license_data->error : '' );
						}
					}
				}
			}

			// Save license status & error code.
			update_option( 'simplepay_main_license_status', $license_status );
			update_option( 'simplepay_main_license_error', $license_error );

			// TODO Delete saved license key from settings (blank out).
			// Timing is still off: View renders before license key is deleted from settings.
			if ( 'deactivated' == $license_status ) {
				//$sc_options->add_setting( 'main_license_key', '' );
			}
		}

		/**
		 * Delete license status & error options from settings (blank them out).
		 */
		private function delete_license_status_error_options() {
			update_option( 'simplepay_main_license_status', '' );
			update_option( 'simplepay_main_license_error', '' );
		}

		/**
		 * Display admin notice for invalid or missing license key.
		 */
		public function invalid_license_key_notice() {
			global $sc_options;

			$main_license_key    = $sc_options->get_setting_value( 'main_license_key' );
			$main_license_status = get_option( 'simplepay_main_license_status' );
			$simplepay_admin     = Stripe_Checkout_Admin::get_instance();

			if ( $simplepay_admin->viewing_this_plugin() && ( empty( $main_license_key ) || ( 'valid' !== $main_license_status ) ) ) {
				?>
				<div class="error notice">
					<p>
						<?php _e( 'Your WP Simple Pay Pro license key is invalid, inactive or missing. Valid license keys are required for access to automatic upgrades and premium support.', 'stripe' ); ?>
						<br />
						<?php
						// Show "below" message unless on licenses tab.
						if ( ! ( isset( $_GET['tab'] ) && ( 'licenses' == $_GET['tab'] ) ) ) {
							// Render link to Support tab on other plugin tabs.
							echo '<a class="simple-pay-licenses-tab-link" href="' . esc_url( add_query_arg( array(
									'page' => Stripe_Checkout_Pro::get_plugin_slug(),
								), admin_url( 'admin.php' ) ) ) . '#license-keys">' . __( 'Go to the licenses page.', 'stripe' ) . '</a>' . "\n";
						}
						?>
					</p>
				</div>
				<?php
			}
		}

		/**
		 * Return an instance of this class.
		 *
		 * @access  public
		 * @return  object  A single instance of this class
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

