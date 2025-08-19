<?php
namespace WP2\Download\Health\Checks\CloudflareR2;

use WP2\Download\Health\BaseCheck;
use Aws\S3\S3Client;

/**
 * @component_id health_r2_artifact_check
 * @namespace health.checks.cloudflarer2
 * @type Check
 * @note "Verifies R2 artifacts for all releases."
 */
class ArtifactCheck extends BaseCheck {

	/**
	 * Returns the check ID.
	 *
	 * @return string
	 */
	public function get_id(): string {
		return 'r2_artifact_check';
	}

	/**
	 * Runs the health check for R2 artifacts.
	 *
	 * @param \WP_Post $package_post The package post object.
	 * @param bool $force Whether to bypass any caches.
	 * @return array Health check result.
	 */
	protected function perform_check( bool $force = false ) {
		$type = str_replace( 'wp2_', '', $this->package_post->post_type );
		$release_query = new \WP_Query( [ 
			'post_type' => "wp2_{$type}_rel",
			'post_parent' => $this->package_post->ID,
			'posts_per_page' => -1,
		] );

		// If no releases, return success
		if ( ! $release_query->have_posts() ) {
			return [ 'status' => 'success', 'data' => [ 'missing_artifacts' => [] ] ];
		}

		// Check R2 configuration
		if ( ! defined( 'WP2_DOWNLOAD_R2_BUCKET' ) || ! defined( 'WP2_DOWNLOAD_R2_ACCOUNT_ID' ) || ! defined( 'WP2_DOWNLOAD_R2_ACCESS_KEY' ) || ! defined( 'WP2_DOWNLOAD_R2_SECRET_KEY' ) ) {
			return [ 
				'status' => 'error',
				'message' => 'R2 bucket is not configured.',
				'details' => [ 
					'WP2_DOWNLOAD_R2_BUCKET' => defined( 'WP2_DOWNLOAD_R2_BUCKET' ) ? WP2_DOWNLOAD_R2_BUCKET : null,
					'WP2_DOWNLOAD_R2_ACCOUNT_ID' => defined( 'WP2_DOWNLOAD_R2_ACCOUNT_ID' ) ? WP2_DOWNLOAD_R2_ACCOUNT_ID : null,
					'WP2_DOWNLOAD_R2_ACCESS_KEY' => defined( 'WP2_DOWNLOAD_R2_ACCESS_KEY' ) ? WP2_DOWNLOAD_R2_ACCESS_KEY : null,
					'WP2_DOWNLOAD_R2_SECRET_KEY' => defined( 'WP2_DOWNLOAD_R2_SECRET_KEY' ) ? WP2_DOWNLOAD_R2_SECRET_KEY : null
				]
			];
		}

		try {
			$s3 = new S3Client( [ 
				'region' => 'auto',
				'endpoint' => defined( 'WP2_DOWNLOAD_R2_S3_ENDPOINT' ) ? WP2_DOWNLOAD_R2_S3_ENDPOINT : '',
				'version' => 'latest',
				'credentials' => [ 
					'key' => defined( 'WP2_DOWNLOAD_R2_ACCESS_KEY' ) ? WP2_DOWNLOAD_R2_ACCESS_KEY : '',
					'secret' => defined( 'WP2_DOWNLOAD_R2_SECRET_KEY' ) ? WP2_DOWNLOAD_R2_SECRET_KEY : '',
				],
			] );
		} catch (\Exception $e) {
			return [ 
				'status' => 'error',
				'message' => 'Failed to initialize S3 client.',
				'details' => $e->getMessage()
			];
		}

		$missing = [];
		foreach ( $release_query->posts as $release_post ) {
			$r2_key = get_post_meta( $release_post->ID, 'wp2_r2_file_key', true );
			try {
				if ( $r2_key && ! $s3->doesObjectExist( WP2_DOWNLOAD_R2_BUCKET, $r2_key ) ) {
					$missing[] = get_post_meta( $release_post->ID, 'wp2_version', true );
				}
			} catch (\Exception $e) {
				$missing[] = [ 
					'version' => get_post_meta( $release_post->ID, 'wp2_version', true ),
					'error' => $e->getMessage(),
					'r2_key' => $r2_key
				];
			}
		}

		$status = empty( $missing ) ? 'success' : 'error';
		return [ 'status' => $status, 'data' => [ 'missing_artifacts' => $missing ] ];
	}
}
