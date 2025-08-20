<?php
/**
 * 
 * @package category WP2\Update
 */

namespace WP2\Update;

// ABSPATH guard (after namespacing for full-file snippets).
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Api_Client
 * Handles hub communication with transient caching.
 */
class Api_Client {
	/** @var string */
	private $hub_url;
	/** @var string */
	private $slug;
	/** @var string plugin|theme|mu-plugin */
	private $type;
	/** @var int */
	private $timeout = 20;

	/**
	 * @param string $hub_url Hub base URL (no trailing slash required).
	 * @param string $slug    Package slug/basename.
	 * @param string $type    One of plugin|theme|mu-plugin.
	 */
	public function __construct( $hub_url, $slug, $type ) {
		$this->hub_url = untrailingslashit( (string) $hub_url );
		$this->slug    = sanitize_key( (string) $slug );
		$this->type    = sanitize_key( (string) $type );
	}

	/**
	 * Build common request headers (auth optional).
	 * @return array
	 */
	private function build_headers() {
		$headers = [ 
			'Accept'     => 'application/json',
			'User-Agent' => 'WP2-Update-SDK/' . get_bloginfo( 'version' ) . ' (' . home_url() . ')',
		];

		// Prefer the site-wide token from the Connector SDK if available.
		if ( class_exists( '\WP2\Connect\Site' ) && \WP2\Connect\Site::get_site_api_token() ) {
			$headers['X-WP2-Site-Api-Token'] = \WP2\Connect\Site::get_site_api_token();
		} elseif ( defined( 'WP2_AUTH_TOKEN' ) && WP2_AUTH_TOKEN ) {
			$headers['Authorization'] = 'Bearer ' . WP2_AUTH_TOKEN;
		}
		return $headers;
	}

	/**
	 * GET request helper.
	 * @param string $path Relative path beginning with '/'.
	 * @param array  $query Optional query args.
	 * @return array|false
	 */
	private function get( $path, $query = [] ) {
		$url = $this->hub_url . $path;
		if ( $query ) {
			$url = add_query_arg( array_map( 'rawurlencode', $query ), $url );
		}
		$response = wp_remote_get( $url, [ 
			'headers' => $this->build_headers(),
			'timeout' => $this->timeout,
		] );
		if ( is_wp_error( $response ) ) {
			return false;
		}
		$code = wp_remote_retrieve_response_code( $response );
		if ( $code < 200 || $code >= 300 ) {
			return false;
		}
		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );
		return is_array( $data ) ? $data : false;
	}

	/**
	 * Retrieve the latest manifest for this package (cached for 1 hour).
	 * Expected keys: version, download_url, signature, name, author, homepage, changelog
	 * @return array|false
	 */
	public function get_manifest() {
		$cache_key = 'wp2_manifest_' . md5( $this->slug . '|' . $this->type );
		$cached    = get_transient( $cache_key );
		if ( is_array( $cached ) ) {
			return $cached;
		}

		$query = [ 
			'type' => $this->type,
			'slug' => $this->slug,
			'site' => home_url(),
			'wp'   => get_bloginfo( 'version' ),
			'php'  => PHP_VERSION,
		];

		$data = $this->get( '/manifest/' . rawurlencode( $this->type ) . '/' . rawurlencode( $this->slug ), $query );
		if ( ! is_array( $data ) ) {
			return false;
		}

		set_transient( $cache_key, $data, HOUR_IN_SECONDS );
		return $data;
	}

	/**
	 * Report adoption / installed version.
	 * Silent fire-and-forget.
	 * @param string $version Installed version string.
	 * @return void
	 */
	public function report_in( $version ) {
		$url  = $this->hub_url . '/report';
		$body = [ 
			'slug'    => $this->slug,
			'type'    => $this->type,
			'version' => (string) $version,
			'site'    => home_url(),
			'wp'      => get_bloginfo( 'version' ),
			'php'     => PHP_VERSION,
		];
		wp_remote_post( $url, [ 
			'headers' => $this->build_headers(),
			'timeout' => $this->timeout,
			'body'    => $body,
		] );
	}

	/**
	 * Whether the given URL is served by this hub (for header injection / interception).
	 * @param string $url
	 * @return bool
	 */
	public function is_hub_url( $url ) {
		$hub = wp_parse_url( $this->hub_url );
		$u   = wp_parse_url( (string) $url );
		if ( empty( $hub['host'] ) || empty( $u['host'] ) ) {
			return false;
		}
		return strtolower( $hub['host'] ) === strtolower( $u['host'] );
	}
}

/**
 * Class Signature_Verifier
 * Performs SHA256 verification of a package using a base64 signature.
 */
class Signature_Verifier {
	/**
	 * @param string $package_path Local file path to the downloaded archive.
	 * @param string $signature    Base64-encoded signature string.
	 * @param string $public_key   PEM public key.
	 * @return bool
	 */
	public static function verify( $package_path, $signature, $public_key ) {
		if ( ! is_readable( $package_path ) || '' === (string) $signature || '' === (string) $public_key ) {
			return false;
		}
		$data = file_get_contents( $package_path );
		if ( false === $data ) {
			return false;
		}
		$pub_key = openssl_pkey_get_public( (string) $public_key );
		if ( ! $pub_key ) {
			return false;
		}
		$sig    = base64_decode( (string) $signature, true );
		$result = openssl_verify( $data, $sig, $pub_key, OPENSSL_ALGO_SHA256 );
		if ( function_exists( 'openssl_free_key' ) ) {
			openssl_free_key( $pub_key );
		}
		return ( 1 === $result );
	}
}

/**
 * Class Updater
 * Orchestrates update lifecycle and verification.
 */
class Updater {
	/** @var Api_Client */
	private $api_client;
	/** @var Signature_Verifier */
	private $signature_verifier;
	/** @var string */
	private $package_file;
	/** @var string */
	private $package_slug;   // theme: stylesheet dir; plugin/mu: plugin_basename (dir/file.php)
	/** @var string */
	private $package_type;   // theme|plugin|mu-plugin

	/**
	 * @param string $package_file Absolute path to the main file.
	 */
	public function __construct( $package_file ) {
		$this->package_file       = (string) $package_file;
		$this->signature_verifier = new Signature_Verifier();
		$this->determine_package_type_and_slug();
		if ( defined( 'WP2_HUB_URL' ) ) {
			$this->api_client = new Api_Client( WP2_HUB_URL, $this->package_slug, $this->package_type );
		}
	}

	/**
	 * Register hooks. No-ops if required constants are missing.
	 * @return void
	 */
	public function init() {
		if ( ! defined( 'WP2_HUB_URL' ) || ! defined( 'WP2_PUBLIC_KEY' ) ) {
			return; // Not configured.
		}

		if ( $this->package_type === 'theme' ) {
			add_filter( 'pre_set_site_transient_update_themes', [ $this, 'filter_update_transient' ] );
		} else {
			add_filter( 'pre_set_site_transient_update_plugins', [ $this, 'filter_update_transient' ] );
			add_filter( 'plugins_api', [ $this, 'filter_plugin_info' ], 10, 3 );
		}

		// Inject auth headers if WordPress downloads package directly from hub.
		add_filter( 'http_request_args', [ $this, 'maybe_add_auth_headers' ], 10, 2 );

		// Intercept download to verify signature before install.
		add_filter( 'upgrader_pre_download', [ $this, 'intercept_download_for_verification' ], 10, 4 );

		add_action( 'upgrader_process_complete', [ $this, 'after_update' ], 10, 2 );

		// Light-touch daily adoption ping.
		add_action( 'wp2_sdk_daily_report', [ $this, 'daily_report' ] );
		if ( ! wp_next_scheduled( 'wp2_sdk_daily_report' ) ) {
			wp_schedule_event( time() + rand( 300, 1800 ), 'daily', 'wp2_sdk_daily_report' );
		}
	}

	/**
	 * Determine type and slug from path.
	 * @return void
	 */
	private function determine_package_type_and_slug() {
		$path = wp_normalize_path( $this->package_file );

		if ( false !== strpos( $path, wp_normalize_path( get_theme_root() ) ) ) {
			$this->package_type = 'theme';
			$this->package_slug = basename( dirname( $path ) ); // stylesheet dir
			return;
		}

		if ( false !== strpos( $path, wp_normalize_path( WP_PLUGIN_DIR ) ) ) {
			$this->package_type = 'plugin';
			$this->package_slug = plugin_basename( $path ); // dir/file.php
			return;
		}

		$this->package_type = 'mu-plugin';
		$this->package_slug = basename( $path );
	}

	/**
	 * Update transient injector for plugin/theme updates.
	 * @param object $transient
	 * @return object
	 */
	public function filter_update_transient( $transient ) {
		if ( ! $this->api_client ) {
			return $transient;
		}
		$manifest = $this->api_client->get_manifest();
		if ( ! is_array( $manifest ) || empty( $manifest['version'] ) || empty( $manifest['download_url'] ) ) {
			return $transient;
		}

		$current = $this->get_current_version();
		if ( ! $current || version_compare( (string) $manifest['version'], (string) $current, '>' ) ) {
			$download = esc_url_raw( (string) $manifest['download_url'] );
			$home     = ! empty( $manifest['homepage'] ) ? esc_url_raw( (string) $manifest['homepage'] ) : home_url();

			if ( $this->package_type === 'theme' ) {
				if ( ! isset( $transient->response ) ) {
					$transient->response = [];
				}
				$transient->response[ $this->package_slug ] = [ 
					'theme'       => $this->package_slug,
					'new_version' => sanitize_text_field( (string) $manifest['version'] ),
					'package'     => $download,
					'url'         => $home,
				];
			} else {
				if ( ! isset( $transient->response ) ) {
					$transient->response = [];
				}
				$transient->response[ $this->package_slug ] = (object) [ 
					'slug'        => sanitize_text_field( $this->normalize_plugin_slug_for_api() ),
					'plugin'      => $this->package_slug,
					'new_version' => sanitize_text_field( (string) $manifest['version'] ),
					'package'     => $download,
					'url'         => $home,
				];
			}
		}
		return $transient;
	}

	/**
	 * Provide plugin info (Details modal) for plugins.
	 * @param mixed  $result
	 * @param string $action
	 * @param object $args
	 * @return mixed
	 */
	public function filter_plugin_info( $result, $action, $args ) {
		if ( 'plugin_information' !== $action ) {
			return $result;
		}
		$requested = isset( $args->slug ) ? (string) $args->slug : '';
		if ( $requested && $requested !== $this->normalize_plugin_slug_for_api() && $requested !== $this->package_slug ) {
			return $result;
		}

		if ( ! $this->api_client ) {
			return $result;
		}
		$manifest = $this->api_client->get_manifest();
		if ( ! is_array( $manifest ) ) {
			return $result;
		}

		return (object) [ 
			'name'          => sanitize_text_field( $manifest['name'] ?? $this->package_slug ),
			'slug'          => $this->normalize_plugin_slug_for_api(),
			'version'       => sanitize_text_field( $manifest['version'] ?? '' ),
			'author'        => wp_kses_post( $manifest['author'] ?? '' ),
			'homepage'      => esc_url_raw( $manifest['homepage'] ?? '' ),
			'sections'      => [ 'changelog' => wp_kses_post( $manifest['changelog'] ?? '' ) ],
			'download_link' => esc_url_raw( $manifest['download_url'] ?? '' ),
			'external'      => true,
		];
	}

	/**
	 * After successful update, clear caches and report in.
	 * @param \WP_Upgrader $upgrader
	 * @param array         $options
	 * @return void
	 */
	public function after_update( $upgrader, $options ) {
		if ( empty( $options['action'] ) || 'update' !== $options['action'] ) {
			return;
		}
		if ( empty( $options['type'] ) || $options['type'] !== $this->package_type ) {
			return;
		}
		// Clear manifest cache for next check.
		delete_transient( 'wp2_manifest_' . md5( $this->package_slug . '|' . $this->package_type ) );
		if ( $this->api_client ) {
			$this->api_client->report_in( $this->get_current_version() );
		}
	}

	/**
	 * Intercept downward download to verify signature for our hub packages.
	 * If we return a string path, core uses it directly and skips its own download.
	 *
	 * @param bool|\WP_Error $reply
	 * @param string          $package URL to package zip.
	 * @param \WP_Upgrader   $upgrader
	 * @param array           $hook_extra
	 * @return bool|\WP_Error|string
	 */
	public function intercept_download_for_verification( $reply, $package, $upgrader, $hook_extra ) {
		if ( ! $this->api_client || ! $this->api_client->is_hub_url( (string) $package ) ) {
			return $reply; // Not ours.
		}

		$manifest = $this->api_client->get_manifest();
		if ( ! is_array( $manifest ) || empty( $manifest['signature'] ) ) {
			return new \WP_Error( 'wp2_missing_signature', __( 'WP2 hub did not provide a signature for this package.', 'wp2' ) );
		}

		// Download to temp file.
		$tmp = download_url( $package, 60 );
		if ( is_wp_error( $tmp ) ) {
			return $tmp;
		}

		$ok = Signature_Verifier::verify( $tmp, (string) $manifest['signature'], (string) WP2_PUBLIC_KEY );
		if ( ! $ok ) {
			@unlink( $tmp );
			return new \WP_Error( 'wp2_bad_signature', __( 'WP2 package signature verification failed.', 'wp2' ) );
		}

		// Hand temp file back to the upgrader.
		return $tmp;
	}

	/**
	 * Add Authorization header for hub downloads (when core performs them).
	 * @param array  $args
	 * @param string $url
	 * @return array
	 */
	public function maybe_add_auth_headers( $args, $url ) {
		if ( ! $this->api_client || ! $this->api_client->is_hub_url( (string) $url ) ) {
			return $args;
		}
		if ( defined( 'WP2_AUTH_TOKEN' ) && WP2_AUTH_TOKEN ) {
			if ( empty( $args['headers'] ) || ! is_array( $args['headers'] ) ) {
				$args['headers'] = [];
			}
			$args['headers']['Authorization'] = 'Bearer ' . WP2_AUTH_TOKEN;
		}
		return $args;
	}

	/**
	 * Current installed version.
	 * @return string
	 */
	private function get_current_version() {
		if ( 'theme' === $this->package_type ) {
			$theme = wp_get_theme( $this->package_slug );
			return (string) $theme->get( 'Version' );
		}
		// Plugins + MU plugins share plugin header format.
		$data = get_file_data( $this->package_file, [ 'Version' => 'Version' ], 'plugin' );
		return isset( $data['Version'] ) ? (string) $data['Version'] : '0.0.0';
	}

	/**
	 * Normalize plugin slug for plugins_api responses (strip ".php").
	 * @return string
	 */
	private function normalize_plugin_slug_for_api() {
		// For plugins, WordPress often expects folder slug. Fall back to basename without extension.
		if ( 'plugin' !== $this->package_type && 'mu-plugin' !== $this->package_type ) {
			return $this->package_slug;
		}
		$parts = explode( '/', $this->package_slug );
		if ( count( $parts ) > 1 ) {
			return $parts[0];
		}
		return preg_replace( '/\.php$/', '', $this->package_slug );
	}

	/**
	 * Cron-driven update flow for MU plugins (no WP UI integration).
	 * @return void
	 */
	public function check_for_updates() {
		if ( ! $this->api_client || 'mu-plugin' !== $this->package_type ) {
			return;
		}
		$manifest = $this->api_client->get_manifest();
		if ( ! is_array( $manifest ) || empty( $manifest['version'] ) || empty( $manifest['download_url'] ) || empty( $manifest['signature'] ) ) {
			return;
		}
		$current = $this->get_current_version();
		if ( $current && version_compare( (string) $manifest['version'], (string) $current, '<=' ) ) {
			return; // Up-to-date.
		}

		// Download package.
		$tmp = download_url( (string) $manifest['download_url'], 60 );
		if ( is_wp_error( $tmp ) ) {
			return;
		}

		// Verify.
		$ok = Signature_Verifier::verify( $tmp, (string) $manifest['signature'], (string) WP2_PUBLIC_KEY );
		if ( ! $ok ) {
			@unlink( $tmp );
			return;
		}

		// Extract and overwrite into mu-plugins directory.
		global $wp_filesystem;
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
		WP_Filesystem();
		if ( ! $wp_filesystem ) {
			@unlink( $tmp );
			return;
		}

		$dest = trailingslashit( WPMU_PLUGIN_DIR );
		// Create a temp extraction dir.
		$working_dir = trailingslashit( get_temp_dir() ) . 'wp2_mu_' . wp_generate_password( 8, false );
		wp_mkdir_p( $working_dir );

		if ( ! function_exists( 'unzip_file' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
		$unzipped = unzip_file( $tmp, $working_dir );
		@unlink( $tmp );
		if ( is_wp_error( $unzipped ) ) {
			return;
		}

		// Find top-level extracted folder or files.
		$entries = list_files( $working_dir, 1 );
		if ( empty( $entries ) ) {
			$this->rmdir_recursive( $working_dir );
			return;
		}

		// Copy contents to MU plugin dir.
		foreach ( (array) scandir( $working_dir ) as $item ) {
			if ( '.' === $item || '..' === $item ) {
				continue;
			}
			$src = trailingslashit( $working_dir ) . $item;
			$dst = trailingslashit( $dest ) . $item;
			// Remove old target if exists.
			if ( $wp_filesystem->exists( $dst ) ) {
				$wp_filesystem->delete( $dst, true );
			}
			// Copy new.
			if ( is_dir( $src ) ) {
				copy_dir( $src, $dst );
			} else {
				$wp_filesystem->copy( $src, $dst, true, FS_CHMOD_FILE );
			}
		}

		// Cleanup.
		$this->rmdir_recursive( $working_dir );

		// Report in after update.
		if ( $this->api_client ) {
			$this->api_client->report_in( $this->get_current_version() );
		}
	}

	/**
	 * Daily reporting hook.
	 * @return void
	 */
	public function daily_report() {
		if ( $this->api_client ) {
			$this->api_client->report_in( $this->get_current_version() );
		}
	}

	/**
	 * Recursively delete a directory (fails silently).
	 * @param string $dir
	 * @return void
	 */
	private function rmdir_recursive( $dir ) {
		if ( ! is_dir( $dir ) ) {
			return;
		}
		$items = scandir( $dir );
		foreach ( $items as $item ) {
			if ( '.' === $item || '..' === $item ) {
				continue;
			}
			$path = $dir . DIRECTORY_SEPARATOR . $item;
			if ( is_dir( $path ) ) {
				$this->rmdir_recursive( $path );
			} else {
				@unlink( $path );
			}
		}
		@rmdir( $dir );
	}
}