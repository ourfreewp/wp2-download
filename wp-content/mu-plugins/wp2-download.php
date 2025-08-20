<?php
/**
 * Plugin Name: WP2 Application: Downloads & Updates
 * Description: The complete, self-contained application for handling all package distribution.
 * Version: 1.0.0
 *
 * @package category WP2\Download
 **/

defined( 'ABSPATH' ) || exit;

// WP2_DOWNLOAD_PATH to wp2/download.

if ( ! defined( 'WP2_DOWNLOAD_PATH' ) ) {
	define( 'WP2_DOWNLOAD_PATH', __DIR__ . '/wp2-download/' );
}

// Define WP2_DOWNLOAD_URL for asset URLs (mu-plugin compatible).
if ( ! defined( 'WP2_DOWNLOAD_URL' ) ) {
	// For mu-plugins, plugin_dir_url(__FILE__) points to the mu-plugins directory.
	define( 'WP2_DOWNLOAD_URL', __DIR__ . '/wp2-download/' );
}

// Load the main application logic (APIs, CPTs, Download Gateway).
if ( file_exists( WP2_DOWNLOAD_PATH . 'init.php' ) ) {
	require_once WP2_DOWNLOAD_PATH . 'init.php';
}

// Load the self-updater client.
if ( file_exists( WP2_DOWNLOAD_PATH . 'updater.php' ) ) {
	require_once WP2_DOWNLOAD_PATH . 'updater.php';
}
