<?php
namespace WP2\Download\Modules\Health;

use Aws\S3\S3Client;
use WP2\Download\Config;

/**
 * @component_id health_audit
 * @namespace health
 * @type Service
 * @note "Handles auditing and healing of package releases."
 */
class Audit {

	public const AUDIT_HOOK              = 'wp2_run_package_audit';
	public const AUDIT_RESULTS_TRANSIENT = 'wp2_package_audit_results';

	/**
	 * Schedule the audit job using Action Scheduler.
	 */
	public function schedule_audit(): void {
		if ( function_exists( 'as_has_scheduled_action' ) && function_exists( 'as_enqueue_async_action' ) ) {
			if ( ! as_has_scheduled_action( self::AUDIT_HOOK, array(), 'wp2-download-audit' ) ) {
				as_schedule_recurring_action( time(), DAY_IN_SECONDS, self::AUDIT_HOOK, array(), 'wp2-download-audit' );
			}
		}
		add_action( self::AUDIT_HOOK, array( $this, 'run_audit' ) );
	}

	/**
	 * The main audit job. Runs in the background via Action Scheduler.
	 * Logs start, completion, and errors.
	 */
	public function run_audit(): void {
		\WP2\Download\Utils\Logger::log( 'Audit: Starting audit job.', 'DEBUG' );
		$manifest_packages = $this->get_packages_from_manifests();
		$r2_versions       = $this->get_all_versions_from_r2();
		$audit_results     = array();

		foreach ( $manifest_packages as $slug => $package ) {
			$db_versions    = $this->get_versions_from_db( $package['type'], $slug );
			$cloud_versions = $r2_versions[ $slug ] ?? array();

			$missing_in_db = array_diff( $cloud_versions, $db_versions );

			if ( ! empty( $missing_in_db ) ) {
				\WP2\Download\Utils\Logger::log( "Audit: Missing in DB for {$slug}: " . implode( ', ', $missing_in_db ), 'WARNING' );
				$audit_results[ $slug ] = array(
					'type'             => $package['type'],
					'missing_versions' => array_values( $missing_in_db ),
					'db_versions'      => $db_versions,
					'cloud_versions'   => $cloud_versions,
				);
			} else {
				$audit_results[ $slug ] = array(
					'type'             => $package['type'],
					'missing_versions' => array(),
					'db_versions'      => $db_versions,
					'cloud_versions'   => $cloud_versions,
				);
			}
		}

		set_transient( self::AUDIT_RESULTS_TRANSIENT, $audit_results, HOUR_IN_SECONDS );
		\WP2\Download\Utils\Logger::log( 'Audit: Completed audit job.', 'DEBUG' );
	}

	/**
	 * Scans the data/packages directory to get a list of all declared packages.
	 */
	private function get_packages_from_manifests(): array {
		$packages = array();
		$base_dir = WPMU_PLUGIN_DIR . '/wp2-download/data/packages';
		if ( ! is_dir( $base_dir ) ) {
			return array();
		}

		$type_map = array(
			'mu-plugins' => 'mu',
			'plugins'    => 'plugin',
			'themes'     => 'theme',
		);

		foreach ( $type_map as $dir_name => $type ) {
			$type_dir = "{$base_dir}/{$dir_name}";
			if ( ! is_dir( $type_dir ) ) {
				continue;
			}

			foreach ( new \DirectoryIterator( $type_dir ) as $package_dir ) {
				if ( $package_dir->isDot() || ! $package_dir->isDir() ) {
					continue;
				}
				$slug              = $package_dir->getFilename();
				$packages[ $slug ] = array( 'type' => $type );
			}
		}
		return $packages;
	}

	private function get_all_versions_from_r2(): array {
		if ( ! defined( 'WP2_DOWNLOAD_R2_BUCKET' ) ) {
			\WP2\Download\Utils\Logger::log( 'R2 bucket not defined in Audit', 'ERROR' );
			return array();
		}
		try {
			$s3      = new S3Client( array( /* S3 config */ ) );
			$objects = $s3->getPaginator( 'ListObjectsV2', array( 'Bucket' => WP2_DOWNLOAD_R2_BUCKET ) );
		} catch ( \Exception $e ) {
			return array(
				'error'   => 'Failed to initialize S3 client or paginator',
				'details' => $e->getMessage(),
			);
		}
		$versions = array();
		try {
			foreach ( $objects as $result ) {
				foreach ( $result['Contents'] ?? array() as $object ) {
					if ( preg_match( '/([a-z\-]+)-(\d+\.\d+\.\d+.*)\.zip$/', $object['Key'], $matches ) ) {
						$slug                = $matches[1];
						$version             = $matches[2];
						$versions[ $slug ][] = $version;
					}
				}
			}
		} catch ( \Exception $e ) {
			return array(
				'error'   => 'Failed to iterate S3 objects',
				'details' => $e->getMessage(),
			);
		}
		return $versions;
	}

	private function get_versions_from_db( string $type, string $slug ): array {
		$parent_post_type = Config::WP2_POST_TYPE_PLUGIN;
		$parent_post      = get_page_by_path( $slug, OBJECT, $parent_post_type );
		if ( ! $parent_post ) {
			return array();
		}

		$release_post_type = Config::WP2_POST_TYPE_PLUGIN_REL;
		$release_query     = new \WP_Query(
			array(
				'post_type'      => $release_post_type,
				'post_parent'    => $parent_post->ID,
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);

		$versions = array();
		foreach ( $release_query->posts as $post_id ) {
			$versions[] = get_post_meta( $post_id, Config::WP2_META_VERSION, true );
		}
		return $versions;
	}
}
