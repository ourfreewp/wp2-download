<?php
namespace WP2\Download\Core\Storage\Adapters\CloudflareR2;

use Aws\S3\S3Client;

/**
 * @component_id gateway_cloudflarer2
 * @namespace Gateways
 * @type Gateway
 * @note "Serves package downloads from Cloudflare R2."
 */
class Gateway {
	public static function serve() {
		global $wp_query;
		$type    = $wp_query->get( 'wp2_package_type' );
		$slug    = $wp_query->get( 'wp2_package_slug' );
		$version = $wp_query->get( 'wp2_package_version' );
		if ( ! $type || ! $slug || ! $version ) {
			return;
		}
		$r2_key = self::get_r2_key_for_release( $type, $slug, $version );
		if ( ! $r2_key ) {
			$wp_query->set_404();
			status_header( 404 );
			return;
		}
		// Credentials should be read from Config
		if ( ! defined( 'WP2_DOWNLOAD_R2_ACCOUNT_ID' ) || ! defined( 'WP2_DOWNLOAD_R2_ACCESS_KEY' ) || ! defined( 'WP2_DOWNLOAD_R2_SECRET_KEY' ) || ! defined( 'WP2_DOWNLOAD_R2_BUCKET' ) ) {
			status_header( 503 );
			echo 'Error: Download service is not configured correctly.';
			exit;
		}
		try {
			$s3           = new S3Client(
				array(
					'region'      => 'auto',
					'endpoint'    => defined( 'WP2_DOWNLOAD_R2_S3_ENDPOINT' ) ? WP2_DOWNLOAD_R2_S3_ENDPOINT : '',
					'version'     => 'latest',
					'credentials' => array(
						'key'    => defined( 'WP2_DOWNLOAD_R2_ACCESS_KEY' ) ? WP2_DOWNLOAD_R2_ACCESS_KEY : '',
						'secret' => defined( 'WP2_DOWNLOAD_R2_SECRET_KEY' ) ? WP2_DOWNLOAD_R2_SECRET_KEY : '',
					),
				)
			);
			$command      = $s3->getCommand(
				'GetObject',
				array(
					'Bucket' => WP2_DOWNLOAD_R2_BUCKET,
					'Key'    => $r2_key,
				)
			);
			$presignedUrl = (string) $s3->createPresignedRequest( $command, '+5 minutes' )->getUri();
			wp_redirect( $presignedUrl );
			exit;
		} catch ( \AwsException $e ) {
			$wp_query->set_404();
			status_header( 404 );
			return;
		}
	}

	public static function get_r2_key_for_release( $type, $slug, $version ) {
		$allowed_types = array( 'plugin', 'theme', 'mu' );
		if ( ! in_array( $type, $allowed_types, true ) ) {
			return null;
		}
		$parent_post_type = 'wp2_' . sanitize_key( $type );
		$parent_post      = get_page_by_path( $slug, OBJECT, $parent_post_type );
		if ( ! $parent_post || 'publish' !== $parent_post->post_status ) {
			return null;
		}
		$release_post_type = $parent_post_type . '_rel';
		$release_query     = new \WP_Query(
			array(
				'post_type'      => $release_post_type,
				'post_status'    => 'publish',
				'post_parent'    => $parent_post->ID,
				'posts_per_page' => 1,
				'meta_query'     => array(
					array(
						'key'     => 'wp2_version',
						'value'   => $version,
						'compare' => '=',
					),
				),
			)
		);
		if ( ! $release_query->have_posts() ) {
			return null;
		}
		$release_post_id = $release_query->posts[0]->ID;
		$r2_key          = get_post_meta( $release_post_id, 'wp2_r2_file_key', true );
		return $r2_key ?: null;
	}
}
