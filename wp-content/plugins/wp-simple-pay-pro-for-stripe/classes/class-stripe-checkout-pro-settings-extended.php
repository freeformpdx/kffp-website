<?php

/**
 * Further extension of MM settings class extension - SP Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Stripe_Checkout_Pro_Settings_Extended' ) ) {

	class Stripe_Checkout_Pro_Settings_Extended extends Stripe_Checkout_Settings_Extended {

		/**
		 * Class constructor
		 *
		 * @param string $option This is the name of the option that will be used in the database
		 */
		public function __construct( $option ) {
			parent::__construct( $option );
		}

		/* sc_live_mode_toggle() in Stripe_Checkout_Settings_Extended */

		/**
		 * Register the license key field callback for EDDSL
		 */
		public function license_key( $id, $license_status, $license_error = '' ) {

			// $id: 'main_license_key'
			$license_key_setting_id = $this->get_setting_id( $id ); // sc_settings[main_license_key]
			$license_key            = trim( $this->get_setting_value( $id ) );

			// Default license status to invalid.
			$license_class = 'sc-invalid';

			$html = '<div class="sc-license-wrap">' . "\n";

			$html .= '<input type="text" class="sc-license-input regular-text" id="' . $license_key_setting_id . '" ' . 'name="' . $license_key_setting_id . '" value="' . sanitize_text_field( $license_key ) . '"/>' . "\n";
			
			// Generic "find your license key" text & link.
			$find_license_message = ' ' .
			                        __( 'You can find your license key by logging into', 'stripe' ) .
			                        sprintf( ' <a href="%1$s" target="_blank">%2$s</a> ', SC_WEBSITE_BASE_URL . 'my-account/', __( 'your account', 'stripe' ) ) .
			                        __( 'or referencing your purchase email.', 'stripe' );

			if ( ! empty( $license_key ) ) {

				// For now let's just read saved license status, not check every time this page is loaded.
				switch ( $license_status ) {
					case 'valid':
						$license_class   = 'sc-valid';
						$license_message = __( 'Your license key is valid and active.', 'stripe' );
						break;
					case 'inactive':
						$license_message = __( 'Your license key appears to be valid, but not activated properly. Please verify and save again, then conctact support if problems persist.', 'stripe' );
						$license_message .= $find_license_message;
						break;
					case 'deactivated':
						$license_message = __( 'Your license key has been deactivated. Please activate a valid license key to continue receiving updates.', 'stripe' );
						$license_message .= $find_license_message;
						break;
					default:
						$license_message = __( 'Your license key appears to be invalid or expired. Please verify and save again, then conctact support if problems persist.', 'stripe' );
						$license_message .= $find_license_message;
				}

				// Add dashicon to end of input box unless blank.
				$dashicons_class = ( 'valid' == $license_status ? 'dashicons-yes' : 'dashicons-no' );
				$html .= '<span class="dashicons ' . $dashicons_class . ' ' . $license_class . '"></span>' . "\n";

				// Output deactivate button on right of dashicon only if valid license.
				if ( 'valid' == $license_status ) {
					$html .= '<input type="submit" name="simplepay_main_license_deactivate" class="button button-secondary" value="' . __( 'Deactivate License', 'stripe' ) . '" />';
				}

			} else {
				$license_message = __( 'Please enter a valid license key.', 'stripe' );
				$license_message .= $find_license_message;
			}

			// Include response status & error from EDDSL API check when WP_DEBUG set to true.
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				if ( ! empty( $license_status ) ) {
					$license_message .= '<br />' . __( 'Return code: ', 'stripe' ) . $license_status;
				}
				if ( ! empty( $license_error ) ) {
					$license_message .= '<br />' . __( 'Error code: ', 'stripe' ) . $license_error;
				};
			}

			$html .= '<p class="description">';
			$html .= '<span class="sc-license-message ' . $license_class . '">' . $license_message . '</span>';
			$html .= '</p>' . "\n";

			$html .= '</div>';

			// Nonce for security.
			wp_nonce_field( 'simplepay_license_key_nonce', 'simplepay_license_key_nonce' );

			echo $html;
		}
	}
}
