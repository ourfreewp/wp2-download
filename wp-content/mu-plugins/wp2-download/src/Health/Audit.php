<?php
namespace WP2\Download\Health;

use WP2\Download\Config as Config;
use Aws\S3\S3Client;

/**
 * Class Audit
 * Handles the auditing and healing of package releases.
 *
 * @package WP2\Download\Health
 */
class Audit {

	public const AUDIT_HOOK = 'wp2_run_package_audit';
	public const AUDIT_RESULTS_TRANSIENT = 'wp2_package_audit_results';

	public function register_hooks(): void {
		add_action( self::AUDIT_HOOK, [ $this, 'run_audit' ] );
	}

	/**
	 * The main audit job. Runs in the background via Action Scheduler.
	 */
	public function run_audit(): void {
		$manifest_packages = $this->get_packages_from_manifests();
		$r2_versions = $this->get_all_versions_from_r2();
		$audit_results = [];

		foreach ( $manifest_packages as $slug => $package ) {
			$db_versions = $this->get_versions_from_db( $package['type'], $slug );
			$cloud_versions = $r2_versions[ $slug ] ?? [];

			$missing_in_db = array_diff( $cloud_versions, $db_versions );

			if ( ! empty( $missing_in_db ) ) {
				$audit_results[ $slug ] = [ 
					'type' => $package['type'],
					'missing_versions' => array_values( $missing_in_db ),
				];
			}
		}

		set_transient( self::AUDIT_RESULTS_TRANSIENT, $audit_results, HOUR_IN_SECONDS );
	}

	/**
	 * Scans the data/packages directory to get a list of all declared packages.
	 */
	private function get_packages_from_manifests(): array {
		$packages = [];
		$base_dir = WPMU_PLUGIN_DIR . '/wp2-download/data/packages';
		if ( ! is_dir( $base_dir ) ) {
			return [];
		}

		$type_map = [ 
			'mu-plugins' => 'mu',
			'plugins' => 'plugin',
			'themes' => 'theme',
		];

		foreach ( $type_map as $dir_name => $type ) {
			$type_dir = "{$base_dir}/{$dir_name}";
			if ( ! is_dir( $type_dir ) ) {
				continue;
			}

			foreach ( new \DirectoryIterator( $type_dir ) as $package_dir ) {
				if ( $package_dir->isDot() || ! $package_dir->isDir() ) {
					continue;
				}
				$slug = $package_dir->getFilename();
				$packages[ $slug ] = [ 'type' => $type ];
			}
		}
		return $packages;
	}

	private function get_all_versions_from_r2(): array {
		if ( ! defined( 'WP2_DOWNLOAD_R2_BUCKET' ) ) {
			\WP2\Download\Util\Logger::log( 'R2 bucket not defined in Audit', 'ERROR' );
			return [];
		}
		try {
			$s3 = new S3Client( [ /* S3 config */] );
			$objects = $s3->getPaginator( 'ListObjectsV2', [ 'Bucket' => WP2_DOWNLOAD_R2_BUCKET ] );
		} catch (\Exception $e) {
			return [ 'error' => 'Failed to initialize S3 client or paginator', 'details' => $e->getMessage() ];
		}
		$versions = [];
		try {
			foreach ( $objects as $result ) {
				foreach ( $result['Contents'] ?? [] as $object ) {
					if ( preg_match( '/([a-z\-]+)-(\d+\.\d+\.\d+.*)\.zip$/', $object['Key'], $matches ) ) {
						$slug = $matches[1];
						$version = $matches[2];
						$versions[ $slug ][] = $version;
					}
				}
			}
		} catch (\Exception $e) {
			return [ 'error' => 'Failed to iterate S3 objects', 'details' => $e->getMessage() ];
		}
		return $versions;
	}

	private function get_versions_from_db( string $type, string $slug ): array {
		$parent_post_type = Config::WP2_POST_TYPE_PLUGIN;
		$parent_post = get_page_by_path( $slug, OBJECT, $parent_post_type );
		if ( ! $parent_post )
			return [];

		$release_post_type = Config::WP2_POST_TYPE_PLUGIN_REL;
		$release_query = new \WP_Query( [ 
			'post_type' => $release_post_type,
			'post_parent' => $parent_post->ID,
			'posts_per_page' => -1,
			'fields' => 'ids',
		] );

		$versions = [];
		foreach ( $release_query->posts as $post_id ) {
			$versions[] = get_post_meta( $post_id, Config::WP2_META_VERSION, true );
		}
		return $versions;
	}
}
