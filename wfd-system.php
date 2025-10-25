<?php
/**
 * Plugin Name:       Waterfilter Direct System
 * Plugin URI:        https://waterfilter.direct
 * Description:       Custom B2B distribution management system for Waterfilter Direct.
 * Version:           3.0.2
 * Author:            Waterfilter Direct
 * Author URI:        https://waterfilter.direct
 * License:           Proprietary
 * Text Domain:       wfd-system
 * Domain Path:       /languages
 */

define( 'WFD_SYSTEM_VERSION', '3.0.2' );
define( 'WFD_SYSTEM_PATH', plugin_dir_path( __FILE__ ) );
define( 'WFD_SYSTEM_URL', plugin_dir_url( __FILE__ ) );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once WFD_SYSTEM_PATH . 'modules/utils.php';

/**
 * Load individual system modules.
 */
function wfd_system_load_modules() {
	$modules = array(
		'account-manager.php',
		'invoices-core.php',
		'invoice-email-system.php',
		'notifications.php',
		'utils.php',
	);

	foreach ( $modules as $module ) {
		$module_path = WFD_SYSTEM_PATH . 'modules/' . $module;

		if ( file_exists( $module_path ) ) {
			require_once $module_path;
		}
	}
}
add_action( 'plugins_loaded', 'wfd_system_load_modules', 1 );

/**
 * Plugin activation hook.
 */
function wfd_system_activate() {
	require_once WFD_SYSTEM_PATH . 'modules/invoices-core.php';
	require_once WFD_SYSTEM_PATH . 'modules/invoice-email-system.php';
	wfd_register_invoice_post_type();
	wfd_register_invoice_statuses();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'wfd_system_activate' );

/**
 * Plugin deactivation hook.
 */
function wfd_system_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'wfd_system_deactivate' );
