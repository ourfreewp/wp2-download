<?php
/**
 * Plugin Name: WP2 Application: Downloads & Updates
 * Description: The complete, self-contained application for handling all package distribution.
 * Version: 1.0.0
 *
 * @package category WP2\Download
 **/

defined( 'ABSPATH' ) || exit;

// Load the main application logic (APIs, CPTs, Download Gateway).
$init = __DIR__ . '/wp2-download/init.php';

if ( file_exists( $init ) ) {
	require_once $init;
}

// Load the self-updater client.
$updater = __DIR__ . '/wp2-download/updater.php';

if ( file_exists( $updater ) ) {
	require_once $updater;
}
