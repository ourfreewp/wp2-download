<?php
/**
 * Class ThemeUpdater
 * Handles self-hosted updates for distributed WordPress themes.
 *
 * @package WP2\Download\Client
 */
namespace WP2\Download\Client\Theme;

/**
 * Handles self-hosted updates for distributed WordPress themes.
 *
 * This class should be packaged with any client theme.
 */
class Updater {
	/**
	 * Verifies the downloaded package using a public key and signature.
	 * @param string $package_path Path to the downloaded package file.
	 * @param string $signature The cryptographic signature from the hub.
	 * @param string $public_key_path Path to the public key file.
	 * @return bool True if valid, false otherwise.
	 */
	private function verify_package_signature( $package_path, $signature, $public_key_path ) {
		if ( ! file_exists( $public_key_path ) || ! file_exists( $package_path ) ) {
			return false;
		}
		$public_key = file_get_contents( $public_key_path );
		$package_data = file_get_contents( $package_path );
		$verified = openssl_verify( $package_data, base64_decode( $signature ), $public_key, OPENSSL_ALGO_SHA256 );
		return $verified === 1;
	}


	/**
	 * @var string Slug for the theme.
	 */
	private $slug;

	/**
	 * @var string API URL for the hub.
	 */
	private $api_url;

	/**
	 * @var string Cache key for transient storage.
	 */
	private $cache_key;

	/**
	 * @var bool Whether caching is allowed.
	 */
	private $cache_allowed = false; // Set to true for production

	/**
	 * ThemeUpdater constructor.
	 *
	 * @param string $hub_url Hub API URL.
	 */
	public function __construct( string $hub_url ) {
		$this->slug = get_stylesheet(); // For themes, the slug is the directory name
		$this->api_url = rtrim( $hub_url, '/' );
		$this->cache_key = 'wp2_updater_' . $this->slug;

		$this->register_hooks();
		$this->schedule_reporting();
	}

	/**
	 * Schedules weekly reporting to the hub.
	 *
	 * @return void
	 */
	private function schedule_reporting() {
		$hook = 'wp2_client_report_in_' . $this->slug;
		if ( ! wp_next_scheduled( $hook ) ) {
			wp_schedule_event( time() + rand( 0, WEEK_IN_SECONDS ), 'weekly', $hook );
		}
		add_action( $hook, [ $this, 'send_report' ] );
	}

	/**
	 * Sends a usage report to the hub.
	 *
	 * @return void
	 */
	public function send_report() {
		$report_url = rtrim( $this->api_url, '/' ) . '/report-in';
		$theme = wp_get_theme();
		wp_remote_post( $report_url, [ 
			'timeout' => 10,
			'body' => [ 
				'slug' => $this->slug,
				'version' => $theme->get( 'Version' ),
				'site_url' => home_url(),
			]
		] );
	}

	private function register_hooks(): void {
		// The core hook for theme updates
		add_filter( 'site_transient_update_themes', [ $this, 'filter_update_transient' ] );
		add_action( 'upgrader_process_complete', [ $this, 'purge_cache' ], 10, 2 );
	}

	private function request_manifest() {
		$remote = get_transient( $this->cache_key );

		if ( false === $remote || ! $this->cache_allowed ) {
			$remote = wp_remote_get( $this->api_url, [ 
				'timeout' => 10,
				'headers' => [ 'Accept' => 'application/json' ],
			] );

			if ( is_wp_error( $remote ) || 200 !== wp_remote_retrieve_response_code( $remote ) ) {
				return false;
			}

			set_transient( $this->cache_key, $remote, DAY_IN_SECONDS );
		}

		return json_decode( wp_remote_retrieve_body( $remote ) );
	}

	public function filter_update_transient( $transient ) {
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		$remote = $this->request_manifest();
		$current_version = $transient->checked[ $this->slug ] ?? '0.0.0';

		if ( $remote && version_compare( $remote->version, $current_version, '>' ) ) {
			$package_url = $remote->download_url;
			$signature = $remote->signature ?? '';
			$public_key_path = WP_CONTENT_DIR . '/mu-plugins/wp2-download/assets/public_key.pem';
			$tmp_package = download_url( $package_url );
			if ( is_wp_error( $tmp_package ) || ! $signature || ! $this->verify_package_signature( $tmp_package, $signature, $public_key_path ) ) {
				error_log( 'WP2 Theme Updater: Package signature verification failed.' );
				return $transient;
			}
			$transient->response[ $this->slug ] = [ 
				'theme' => $this->slug,
				'new_version' => $remote->version,
				'url' => isset( $remote->links->homepage ) ? $remote->links->homepage : '',
				'package' => $package_url,
			];
		}

		return $transient;
	}

	public function purge_cache( $upgrader, $options ): void {
		if ( $this->cache_allowed && 'update' === $options['action'] && 'theme' === $options['type'] ) {
			delete_transient( $this->cache_key );
		}
	}
}