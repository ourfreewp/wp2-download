<?php
// wp-content/mu-plugins/wp2-download/src/Services/Locator.php
namespace WP2\Download\Services;

defined( 'ABSPATH' ) || exit();


use WP2\Download\Origin\Adapters\ConnectionInterface;
use WP2\Download\Origin\Adapters\Composer\Adapter as ComposerAdapter;
use WP2\Download\Origin\Adapters\Github\Adapter as GithubAdapter;
use WP2\Download\Origin\Adapters\GoogleDrive\Adapter as GoogleDriveAdapter;
use WP2\Download\Origin\Adapters\Storage\Adapter as StorageAdapter;
use WP2\Download\Origin\Adapters\WP\Adapter as WPAdapter;

class Locator {
	// Service Locator for adapters and origins.
	/**
	 * Singleton Health Runner instance.
	 *
	 * @var \WP2\Download\Health\Runner|null
	 */
	private static $health_runner = null;


	/**
	 * Map of adapter instances keyed by kind.
	 *
	 * @var array<string,ConnectionInterface>
	 */
	protected static array $origins = [];

	/**
	 * List available storage adapters.
	 *
	 * @return array<string>
	 */
	public static function list_storage_adapters(): array {
		if ( defined( 'WP2_DOWNLOAD_PATH' ) ) {
			$dir = WP2_DOWNLOAD_PATH . '/src/Storage/Adapters';
		}
		return self::scan_adapter_dir( $dir );
	}

	/**
	 * List available development adapters.
	 *
	 * @return array<string>
	 */
	public static function list_development_adapters(): array {
		if ( defined( 'WP2_DOWNLOAD_PATH' ) ) {
			$dir = WP2_DOWNLOAD_PATH . '/src/Development/Adapters';
		}
		return self::scan_adapter_dir( $dir );
	}

	/**
	 * List available licensing adapters.
	 *
	 * @return array<string>
	 */
	public static function list_licensing_adapters(): array {
		if ( defined( 'WP2_DOWNLOAD_PATH' ) ) {
			$dir = WP2_DOWNLOAD_PATH . '/src/Licensing/Adapters';
		}
		return self::scan_adapter_dir( $dir );
	}

	/**
	 * List available analytics adapters.
	 *
	 * @return array<string>
	 */
	public static function list_analytics_adapters(): array {
		if ( defined( 'WP2_DOWNLOAD_PATH' ) ) {
			$dir = WP2_DOWNLOAD_PATH . '/src/Analytics/Adapters';
		}
		return self::scan_adapter_dir( $dir );
	}

	/**
	 * List supported origin kinds.
	 *
	 * @return array<string>
	 */
	public static function list_origin_kinds(): array {
		$kinds = [ 'composer', 'github', 'gdrive', 'hub', 'wporg' ];
		sort( $kinds, SORT_STRING );
		return $kinds;
	}

	/**
	 * Get the selected adapter slug for a given service.
	 *
	 * @param string $service Service key: analytics|licensing|storage|development.
	 * @return string
	 */
	private static function get_selected_adapter_slug( string $service ): string {
		$option_key = 'wp2_download_' . $service . '_adapter';
		$val = get_option( $option_key );
		return ( is_string( $val ) && $val !== '' ) ? $val : 'unset';
	}

	/**
	 * Build a fully-qualified class name for an adapter file slug.
	 *
	 * @param string $service Service key.
	 * @param string $adapter_slug Adapter file name without extension.
	 * @return string FQCN.
	 */
	private static function build_adapter_fqcn( string $service, string $adapter_slug ): string {
		$namespace_map = [ 
			'analytics' => '\\WP2\\Download\\Analytics\\Adapters',
			'licensing' => '\\WP2\\Download\\Licensing\\Adapters',
			'storage' => '\\WP2\\Download\\Storage\\Adapters',
			'development' => '\\WP2\\Download\\Development\\Adapters',
		];
		$ns = $namespace_map[ $service ] ?? '';
		return $ns . '\\' . $adapter_slug;
	}

	/**
	 * Build a fully-qualified class name for an origin kind.
	 *
	 * Origins use a dedicated resolver because they live in sub-namespaces
	 * with Adapter.php suffixes rather than flat adapter files.
	 *
	 * @param string $kind Origin kind.
	 * @return string FQCN or empty string if not found.
	 */
	private static function build_origin_fqcn( string $kind ): string {
		$map = [ 
			'composer' => '\\WP2\\Download\\Origin\\Adapters\\Composer\\Adapter',
			'github' => '\\WP2\\Download\\Origin\\Adapters\\Github\\Adapter',
			'gdrive' => '\\WP2\\Download\\Origin\\Adapters\\GoogleDrive\\Adapter',
			'hub' => '\\WP2\\Download\\Origin\\Adapters\\Storage\\Adapter',
			'wporg' => '\\WP2\\Download\\Origin\\Adapters\\WP\\Adapter',
		];
		return $map[ $kind ] ?? '';
	}

	/**
	 * Resolve and instantiate an adapter for a service.
	 *
	 * @param string $service Service key.
	 * @return object|null
	 */
	private static function resolve_adapter_instance( string $service ) {
		$slug = self::get_selected_adapter_slug( $service );
		if ( $slug === 'unset' ) {
			return null;
		}
		$fqcn = self::build_adapter_fqcn( $service, $slug );
		if ( class_exists( $fqcn ) ) {
			return new $fqcn();
		}
		return null;
	}

	/**
	 * Get an origin adapter by kind.
	 *
	 * @param string $kind Origin kind key.
	 * @return ConnectionInterface|null
	 */
	/**
	 * Get an origin adapter by kind. Lazily instantiate if not registered.
	 *
	 * @param string $kind Origin kind key.
	 * @return ConnectionInterface|null
	 */
	public static function origin( string $kind ): ?ConnectionInterface {
		$kind = strtolower( trim( $kind ) );
		if ( isset( self::$origins[ $kind ] ) ) {
			return self::$origins[ $kind ];
		}
		$fqcn = self::build_origin_fqcn( $kind );
		if ( $fqcn && class_exists( $fqcn ) ) {
			self::$origins[ $kind ] = new $fqcn();
			return self::$origins[ $kind ];
		}
		return null;
	}

	/**
	 * Get a human-friendly label for an origin adapter kind.
	 *
	 * @param string $kind Origin kind.
	 * @return string
	 */
	public static function get_origin_adapter_label( string $kind ): string {
		$adapter = self::origin( $kind );
		if ( $adapter && method_exists( $adapter, 'get_label' ) ) {
			return $adapter->get_label();
		}
		return ucfirst( $kind );
	}

	/**
	 * Bootstrap and register all adapters.
	 *
	 * @return void
	 */
	public static function register_default_origins(): void {
		self::$origins = [ 
			'composer' => new ComposerAdapter(),
			'github' => new GithubAdapter(),
			'gdrive' => new GoogleDriveAdapter(),
			'storage' => new StorageAdapter(),
			'wporg' => new WPAdapter(),
		];
	}


	/**
	 * Get active licensing adapter instance.
	 *
	 * @return object|null
	 */
	public static function licensing() {
		static $instance = null;
		if ( ! isset( $instance ) ) {
			$instance = self::resolve_adapter_instance( 'licensing' );
		}
		return $instance;
	}

	/**
	 * Get active analytics adapter instance.
	 *
	 * @return object|null
	 */
	public static function analytics() {
		static $instance = null;
		if ( ! isset( $instance ) ) {
			$instance = self::resolve_adapter_instance( 'analytics' );
		}
		return $instance;
	}

	/**
	 * Get active storage adapter instance.
	 *
	 * @return object|null
	 */
	public static function storage() {
		static $instance = null;
		if ( ! isset( $instance ) ) {
			$instance = self::resolve_adapter_instance( 'storage' );
		}
		return $instance;
	}

	/**
	 * Get active development adapter instance.
	 *
	 * @return object|null
	 */
	public static function development() {
		static $instance = null;
		if ( ! isset( $instance ) ) {
			$instance = self::resolve_adapter_instance( 'development' );
		}
		return $instance;
	}

	/**
	 * Helper to scan adapter directory for PHP files.
	 *
	 * @param string $dir Directory path.
	 * @return array<string>
	 */
	private static function scan_adapter_dir( string $dir ): array {
		$adapters = [];
		if ( is_dir( $dir ) ) {
			foreach ( glob( $dir . '/*Adapter.php' ) as $file ) {
				$adapters[] = basename( $file, '.php' );
			}
		}
		sort( $adapters, SORT_NATURAL | SORT_FLAG_CASE );
		return $adapters;
	}



	/**
	 * Get the singleton Health Runner instance.
	 *
	 * @return \WP2\Download\Health\Runner
	 */
	public static function get_health_runner() {
		if ( null === self::$health_runner ) {
			self::$health_runner = new \WP2\Download\Health\Runner();
		}
		return self::$health_runner;
	}


}