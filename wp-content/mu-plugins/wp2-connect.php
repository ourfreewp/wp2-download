<?php
/**
 * wp2-connect.php — WP2 Site Connector SDK (single-file MU plugin)
 *
 * Drop into wp-content/mu-plugins/wp2-connect.php
 *
 * Required in wp-config.php:
 * define( 'WP2_CONNECT_HUB_URL', 'https://your-hub-domain.com/wp-json/wp2/v1' );
 * define( 'WP2_CONNECT_REG_TOKEN', 'your-initial-registration-token' );
 *
 * Optional:
 * define( 'WP2_CONNECT_TIMEOUT', 20 ); // seconds
 */

namespace WP2\Connect;

// ABSPATH guard (after namespacing per project conventions)
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Site
 * Establishes a persistent site-level identity with the hub and injects mirrored updates.
 */
class Site {
	/** @var string Option key for the site API token */
	const OPTION_KEY = 'wp2_site_api_token';

	/** @var string Hub URL */
	private $hub_url;

	/** @var string Registration token (one-time exchange) */
	private $registration_token;

	/** @var string|null Cached site token for this request */
	private $site_api_token = null;

	/** @var array|null Cached mirrored update response for this request */
	private $mirror_cache = null;

	/**
	 * Initialize hooks.
	 * @return void
	 */
	public function init() {
		if ( ! defined( 'WP2_CONNECT_HUB_URL' ) || ! defined( 'WP2_CONNECT_REG_TOKEN' ) ) {
			return; // not configured
		}

		$this->hub_url            = untrailingslashit( (string) WP2_CONNECT_HUB_URL );
		$this->registration_token = (string) WP2_CONNECT_REG_TOKEN;

		// Ensure site is registered as early as possible in admin requests.
		add_action( 'admin_init', [ $this, 'maybe_register_site' ] );

		// Inject mirrored updates from hub.
		add_filter( 'pre_set_site_transient_update_plugins', [ $this, 'check_for_mirrored_updates' ] );
		add_filter( 'pre_set_site_transient_update_themes', [ $this, 'check_for_mirrored_updates' ] );

		// Add auth header for downloads served by hub (secure gateway).
		add_filter( 'http_request_args', [ $this, 'maybe_add_auth_headers' ], 10, 2 );

		// Optional daily heartbeat/report (non-blocking).
		add_action( 'wp2_connect_daily', [ $this, 'daily_heartbeat' ] );
		if ( ! wp_next_scheduled( 'wp2_connect_daily' ) ) {
			wp_schedule_event( time() + rand( 300, 1800 ), 'daily', 'wp2_connect_daily' );
		}
	}

	/**
	 * Attempt site registration if we do not yet have a token.
	 * @return void
	 */
	public function maybe_register_site() {
		if ( $this->get_site_api_token() ) {
			return;
		}
		$this->register_site();
	}

	/**
	 * Perform one-time registration with the hub to exchange for a durable site token.
	 * @return bool True on success.
	 */
	public function register_site() {
		$payload = [ 
			'site' => home_url(),
			'wp'   => get_bloginfo( 'version' ),
			'php'  => PHP_VERSION,
		];

		$response = $this->post_json( '/sites/register', $payload, [ 
			'X-WP2-Registration-Token' => $this->registration_token,
		] );

		if ( ! is_array( $response ) || empty( $response['token'] ) ) {
			return false;
		}

		$token = (string) $response['token'];
		update_option( self::OPTION_KEY, $token, true ); // autoload
		$this->site_api_token = $token;
		return true;
	}

	/**
	 * Public accessor for other code to use the site token.
	 * @return string|null
	 */
	public static function get_site_api_token() {
		$token = get_option( self::OPTION_KEY );
		return $token ? (string) $token : null;
	}

	/**
	 * Filter: inject hub-provided mirrored updates into transient (plugins or themes).
	 * @param object $transient
	 * @return object
	 */
	public function check_for_mirrored_updates( $transient ) {
		$token = $this->ensure_site_token();
		if ( ! $token ) {
			return $transient; // not registered yet
		}

		// Avoid multiple requests per page load; cache combined result.
		if ( null === $this->mirror_cache ) {
			$packages = $this->collect_installed_packages();
			if ( empty( $packages ) ) {
				return $transient;
			}

			$body               = [ 'packages' => $packages ];
			$resp               = $this->post_json( '/updates/check-mirror', $body, [ 'X-WP2-Site-Api-Token' => $token ] );
			$this->mirror_cache = is_array( $resp ) ? $resp : [];
		}

		$updates = $this->mirror_cache;
		if ( empty( $updates ) ) {
			return $transient;
		}

		// Merge plugin updates.
		if ( isset( $updates['plugins'] ) && is_array( $updates['plugins'] ) ) {
			if ( ! isset( $transient->response ) ) {
				$transient->response = [];
			}

			foreach ( $updates['plugins'] as $slug => $data ) {
				$data = is_array( $data ) ? $data : [];
				if ( empty( $data['new_version'] ) || empty( $data['package_url'] ) || empty( $data['plugin'] ) ) {
					continue;
				}

				$plugin_basename                         = (string) $data['plugin']; // expected dir/file.php from hub
				$transient->response[ $plugin_basename ] = (object) [ 
					'slug'        => sanitize_key( $slug ),
					'plugin'      => $plugin_basename,
					'new_version' => sanitize_text_field( (string) $data['new_version'] ),
					'package'     => esc_url_raw( (string) $data['package_url'] ),
					'url'         => esc_url_raw( (string) ( $data['url'] ?? home_url() ) ),
				];
			}
		}

		// Merge theme updates.
		if ( isset( $updates['themes'] ) && is_array( $updates['themes'] ) ) {
			if ( ! isset( $transient->response ) ) {
				$transient->response = [];
			}

			foreach ( $updates['themes'] as $stylesheet => $data ) {
				$data = is_array( $data ) ? $data : [];
				if ( empty( $data['new_version'] ) || empty( $data['package_url'] ) ) {
					continue;
				}
				$transient->response[ $stylesheet ] = [ 
					'theme'       => sanitize_key( $stylesheet ),
					'new_version' => sanitize_text_field( (string) $data['new_version'] ),
					'package'     => esc_url_raw( (string) $data['package_url'] ),
					'url'         => esc_url_raw( (string) ( $data['url'] ?? home_url() ) ),
				];
			}
		}

		return $transient;
	}

	/**
	 * Add site token header to HTTP requests when URL is on the hub domain.
	 * @param array  $args
	 * @param string $url
	 * @return array
	 */
	public function maybe_add_auth_headers( $args, $url ) {
		$token = $this->ensure_site_token();
		if ( ! $token || ! $this->is_hub_url( $url ) ) {
			return $args;
		}
		if ( empty( $args['headers'] ) || ! is_array( $args['headers'] ) ) {
			$args['headers'] = [];
		}
		$args['headers']['X-WP2-Site-Api-Token'] = $token;
		return $args;
	}

	/**
	 * Optional daily heartbeat to keep hub stats fresh.
	 * @return void
	 */
	public function daily_heartbeat() {
		$token = $this->ensure_site_token();
		if ( ! $token ) {
			return;
		}
		$this->post_json( '/sites/heartbeat', [ 'site' => home_url() ], [ 'X-WP2-Site-Api-Token' => $token ] );
	}

	/**
	 * Ensure token is loaded (register if needed) and return it.
	 * @return string|null
	 */
	private function ensure_site_token() {
		if ( is_string( $this->site_api_token ) && $this->site_api_token !== '' ) {
			return $this->site_api_token;
		}
		$token = get_option( self::OPTION_KEY );
		if ( $token ) {
			$this->site_api_token = (string) $token;
			return $this->site_api_token;
		}
		// Try to register now (non-fatal if fails).
		$this->register_site();
		return $this->site_api_token;
	}

	/**
	 * Build the complete list of installed plugins and themes with versions.
	 * @return array{plugins: array, themes: array}
	 */
	private function collect_installed_packages() {
		$packages = [ 'plugins' => [], 'themes' => [] ];

		// Plugins
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$all_plugins = function_exists( 'get_plugins' ) ? get_plugins() : [];
		foreach ( $all_plugins as $basename => $headers ) {
			$version               = isset( $headers['Version'] ) ? (string) $headers['Version'] : '0.0.0';
			$slug                  = $this->plugin_slug_from_basename( $basename );
			$packages['plugins'][] = [ 
				'type'    => 'plugin',
				'slug'    => $slug,
				'plugin'  => $basename, // keep original for mapping back
				'version' => $version,
			];
		}

		// Themes
		$themes = wp_get_themes();
		foreach ( $themes as $stylesheet => $theme ) {
			$packages['themes'][] = [ 
				'type'    => 'theme',
				'slug'    => (string) $stylesheet,
				'version' => (string) $theme->get( 'Version' ),
			];
		}

		return $packages;
	}

	/**
	 * Minimal POST JSON helper.
	 * @param string $path  Hub path starting with '/'
	 * @param array  $body  JSON-serializable body
	 * @param array  $extra_headers Extra headers
	 * @return array|false
	 */
	private function post_json( $path, array $body, array $extra_headers = [] ) {
		$timeout = defined( 'WP2_CONNECT_TIMEOUT' ) ? (int) WP2_CONNECT_TIMEOUT : 20;
		$url     = $this->hub_url . $path;
		$headers = array_merge( [ 
			'Accept'       => 'application/json',
			'Content-Type' => 'application/json; charset=utf-8',
			'User-Agent'   => 'WP2-Connect/' . get_bloginfo( 'version' ) . ' (' . home_url() . ')',
		], $extra_headers );

		$response = wp_remote_post( $url, [ 
			'headers' => $headers,
			'timeout' => $timeout,
			'body'    => wp_json_encode( $body ),
		] );
		if ( is_wp_error( $response ) ) {
			return false;
		}
		$code = wp_remote_retrieve_response_code( $response );
		if ( $code < 200 || $code >= 300 ) {
			return false;
		}
		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		return is_array( $data ) ? $data : false;
	}

	/**
	 * Check if a given URL belongs to the configured hub.
	 * @param string $url
	 * @return bool
	 */
	private function is_hub_url( $url ) {
		$hub = wp_parse_url( $this->hub_url );
		$u   = wp_parse_url( (string) $url );
		if ( empty( $hub['host'] ) || empty( $u['host'] ) ) {
			return false;
		}
		return strtolower( $hub['host'] ) === strtolower( $u['host'] );
	}

	/**
	 * From plugin basename (dir/file.php) to canonical slug (dir part).
	 * @param string $basename
	 * @return string
	 */
	private function plugin_slug_from_basename( $basename ) {
		$parts = explode( '/', (string) $basename );
		return sanitize_key( $parts[0] );
	}
}

// Bootstrap
( new Site() )->init();
