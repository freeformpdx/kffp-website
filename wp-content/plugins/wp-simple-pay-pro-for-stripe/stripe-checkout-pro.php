<?php
/**
 * Plugin Name: WP Simple Pay Pro for Stripe
 * Plugin URI:  https://wpsimplepay.com
 * Description: Add high conversion Stripe checkout forms to your WordPress site and start accepting payments in minutes. **Pro Version**
 * Author:      Moonstone Media
 * Author URI:  https://wpsimplepay.com
 * Version:     2.4.7
 * Text Domain: stripe
 * Domain Path: /i18n
 *
 * @copyright   2014-2016 Moonstone Media/Phil Derksen. All rights reserved.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Setup plugin constants.

// Plugin version
if ( ! defined( 'SIMPAY_PRO_VERSION' ) ) {
	define( 'SIMPAY_PRO_VERSION', '2.4.7' );
}

// Plugin folder path
// TODO SIMPAY_PLUGIN_DIR
// TODO Combine both here?
if ( ! defined( 'SC_DIR_PATH' ) ) {
	define( 'SC_DIR_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'SC_DIR_PATH_PRO' ) ) {
	define( 'SC_DIR_PATH_PRO', plugin_dir_path( __FILE__ ) );
}

// Plugin folder URL
// TODO SIMPAY_PLUGIN_URL
if ( ! defined( 'SC_DIR_URL' ) ) {
	define( 'SC_DIR_URL', plugin_dir_url( __FILE__ ) );
}

// Plugin root file
// TODO SIMPAY_PLUGIN_FILE
if ( ! defined( 'SC_PLUGIN_FILE' ) ) {
	define( 'SC_PLUGIN_FILE', __FILE__ );
}

// Base URL
// TODO SIMPAY_BASE_URL
if ( ! defined( 'SC_WEBSITE_BASE_URL' ) ) {
	define( 'SC_WEBSITE_BASE_URL', 'https://wpsimplepay.com/' );
}

// Plugin requirements class.
require_once 'classes/wp-requirements.php';

// Check plugin requirements before loading plugin.
$this_plugin_checks = new SimPay_WP_Requirements( 'WP Simple Pay Pro for Stripe', plugin_basename( __FILE__ ), array(
	'PHP'        => '5.3.3',
	'WordPress'  => '4.2',
	'Extensions' => array(
		'curl',
		'json',
		'mbstring',
	),
) );
if ( $this_plugin_checks->pass() === false ) {
	$this_plugin_checks->halt();

	return;
}

// Load the plugin main class (and base class before it).
require_once SC_DIR_PATH_PRO . 'classes/class-stripe-checkout-shared.php';
require_once SC_DIR_PATH_PRO . 'classes/class-stripe-checkout-pro.php';

// Register hook that is fired when the plugin is activated.
register_activation_hook( SC_PLUGIN_FILE, array( 'Stripe_Checkout_Pro', 'activate' ) );

// Create a global instance of our main class for this plugin so we can use it throughout all the other classes.
global $base_class;

// Let's get going finally!
$base_class = Stripe_Checkout_Pro::get_instance();
