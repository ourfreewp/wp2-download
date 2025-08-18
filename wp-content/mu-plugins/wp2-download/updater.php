<?php
namespace WP2\Download;

use WP2\Download\Util\Logger;

defined( 'ABSPATH' ) || exit;

const UPDATER_TYPE = 'mu';
const UPDATER_SLUG = 'wp2-download';
const UPDATER_ACTION_HOOK = 'wp2_self_update_check';

// Use 'plugins_loaded' which runs after Action Scheduler is available.
add_action( 'plugins_loaded', __NAMESPACE__ . '\schedule_updater' );
add_action( UPDATER_ACTION_HOOK, __NAMESPACE__ . '\check_for_updates' );

/**
 * Schedule the recurring update check with Action Scheduler.
 */
function schedule_updater(): void {
	// Ensure Action Scheduler functions exist before calling them.
	if ( ! function_exists( 'as_next_scheduled_action' ) ) {
		return;
	}

	// Schedule the check to run daily, but only if one is not already scheduled.
	if ( false === as_next_scheduled_action( UPDATER_ACTION_HOOK ) ) {
		as_schedule_recurring_action( time(), DAY_IN_SECONDS, UPDATER_ACTION_HOOK );
	}
}

function check_for_updates(): void {
	if ( ! function_exists( 'get_plugin_data' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}
	$plugin_file = WPMU_PLUGIN_DIR . '/' . UPDATER_SLUG . '.php';
	$plugin_data = get_plugin_data( $plugin_file );
	$current_version = $plugin_data['Version'] ?? '0.0.0';

	$api_url = home_url( '/wp-json/wp2/v1/packages/' . UPDATER_TYPE . '/' . UPDATER_SLUG );
	$response = wp_remote_get( $api_url, [ 'timeout' => 15 ] );

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		Logger::log( 'Self-updater API check failed: ' . ( is_wp_error( $response ) ? $response->get_error_message() : 'Invalid response code.' ), 'ERROR' );
		return;
	}

	$data = json_decode( wp_remote_retrieve_body( $response ), true );
	$latest_version = $data['version'] ?? '0.0.0';

	if ( version_compare( $current_version, $latest_version, '>=' ) ) {
		return;
	}

	perform_update( $latest_version );
}

function perform_update( string $new_version ): bool {
	Logger::log( "Starting self-update to version {$new_version}." );
	require_once ABSPATH . 'wp-admin/includes/file.php';
	WP_Filesystem();
	global $wp_filesystem;

	// Check if destination directory is writable
	if ( ! is_writable( WPMU_PLUGIN_DIR ) ) {
		Logger::log( 'Self-update failed: Destination directory is not writable.', 'ERROR' );
		return false;
	}

	// Use the public download gateway URL to get the file
	$download_url = home_url( '/wp2-download/' . UPDATER_TYPE . '/' . UPDATER_SLUG . '/' . $new_version );
	// Force HTTPS for all downloads
	if ( strpos( $download_url, 'http://' ) === 0 ) {
		$download_url = 'https://' . substr( $download_url, 7 );
	}

	$tmp_zip = download_url( $download_url, 300 );
	if ( is_wp_error( $tmp_zip ) ) {
		Logger::log( 'Self-update failed: Could not download package. ' . $tmp_zip->get_error_message(), 'ERROR' );
		return false;
	}

	$tmp_dir = get_temp_dir();
	$unzip_result = unzip_file( $tmp_zip, $tmp_dir );
	if ( is_wp_error( $unzip_result ) ) {
		Logger::log( 'Self-update failed: Could not unzip package. ' . $unzip_result->get_error_message(), 'ERROR' );
		@unlink( $tmp_zip );
		return false;
	}

	// Correctly define source and destination paths based on the repo structure
	$source_dir = trailingslashit( $tmp_dir ) . 'wp-content/mu-plugins/wp2-download/';
	$source_file = trailingslashit( $tmp_dir ) . 'wp-content/mu-plugins/wp2-download.php';
	$dest_dir = WPMU_PLUGIN_DIR . '/wp2-download/';
	$dest_file = WPMU_PLUGIN_DIR . '/wp2-download.php';

	// Replace the old files
	$wp_filesystem->rmdir( $dest_dir, true ); // Delete old directory
	$wp_filesystem->move( $source_dir, $dest_dir, true ); // Move new directory
	$wp_filesystem->move( $source_file, $dest_file, true ); // Move new loader file

	// Clean up
	$wp_filesystem->rmdir( $tmp_dir, true );

	if ( function_exists( 'opcache_reset' ) ) {
		opcache_reset();
	}

	Logger::log( "Self-update to version {$new_version} completed successfully." );
	return true;
}