<?php
/**
 * Application entrypoint (MU loader target).
 *
 * - Loads Composer autoloader for WP2\Download classes.
 * - Hands off to the namespaced core bootstrap in src/Init.php.
 *
 * @package WP2\Download
 */

defined( 'ABSPATH' ) || exit;

// Define plugin path and URL constants for use in admin and asset loading.
if ( ! defined( 'WP2_DOWNLOAD_PATH' ) ) {
	define( 'WP2_DOWNLOAD_PATH', __DIR__ . '/' );
}
if ( ! defined( 'WP2_DOWNLOAD_URL' ) ) {
	// Try to get plugin URL from WordPress API if available, fallback to relative.
	if ( function_exists( 'plugins_url' ) ) {
		define( 'WP2_DOWNLOAD_URL', rtrim( plugins_url( '', __FILE__ ), '/' ) . '/' );
	} else {
		define( 'WP2_DOWNLOAD_URL', '/wp-content/mu-plugins/wp2-download/' );
	}
}

// define WP2_VENDOR_PATH
if ( ! defined( 'WP2_VENDOR_PATH' ) ) {
	define( 'WP2_VENDOR_PATH',
		dirname( __DIR__, 2 ) . '/wp2/vendor/' 
	);
}



// Composer autoload (required for PSR-4 classes).
$autoload_path = WP2_VENDOR_PATH . 'autoload.php';

error_log('[WP2][DEBUG] Autoload: ' . $autoload_path);

if ( ! file_exists( $autoload_path ) ) {
	return;
}

require_once $autoload_path;

// Core bootstrap (kept thin; orchestrates feature inits).
require_once __DIR__ . '/src/Init.php';

\WP2\Download\wp2_download_bootstrap();