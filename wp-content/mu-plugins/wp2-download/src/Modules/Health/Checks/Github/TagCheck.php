<?php
namespace WP2\Download\Modules\Health\Checks\Github;

use WP2\Download\Health\BaseCheck;

/**
 * @component_id health_github_tag_check
 * @namespace health.checks.github
 * @type Check
 * @note "Detects new, unreleased tags in GitHub repositories."
 */
class TagCheck extends BaseCheck {

	/**
	 * Returns the check ID.
	 *
	 * @return string
	 */
	public function get_id(): string {
		return 'github_tag_check';
	}

	/**
	 * Runs the health check for new GitHub tags.
	 *
	 * @param \WP_Post $package_post The package post object.
	 * @param bool     $force Whether to bypass any caches.
	 * @return array Health check result.
	 */
	protected function perform_check( bool $force = false ) {
		$links    = $this->get_meta( 'wp2_links', true );
		$repo_url = $links['repository'] ?? null;

		// Validate GitHub repository URL
		if ( ! $repo_url || ! preg_match( '/^https?:\/\/github\.com\/([^\/]+\/[^\/]+)$/', $repo_url, $matches ) ) {
			return array(
				'status'   => 'error',
				'message'  => 'Invalid or missing GitHub repository URL.',
				'repo_url' => $repo_url,
				'hint'     => 'Expected format: https://github.com/owner/repo',
			);
		}

		$repo_slug     = $matches[1];
		$transient_key = 'wp2_github_tag_check_' . md5( $repo_slug );

		// Always bypass cache if force is true
		if ( $force ) {
			delete_transient( $transient_key );
		}
		$cached_data = get_transient( $transient_key );
		if ( false !== $cached_data && ! $force ) {
			return $cached_data;
		}

		$api_url = "https://api.github.com/repos/{$repo_slug}/tags";
		$args    = array( 'timeout' => 15 );
		// Support private repo access via PAT
		if ( defined( 'WP2_GITHUB_PAT' ) && WP2_GITHUB_PAT ) {
			$args['headers'] = array( 'Authorization' => 'Bearer ' . WP2_GITHUB_PAT );
		}

		$response = wp_remote_get( $api_url, $args );

		if ( is_wp_error( $response ) ) {
			$error_msg = $response->get_error_message();
			return array(
				'status'    => 'error',
				'message'   => 'Failed to connect to GitHub API for tags.',
				'details'   => $error_msg,
				'repo_slug' => $repo_slug,
			);
		}
		$http_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $http_code ) {
			return array(
				'status'    => 'error',
				'message'   => 'GitHub API returned unexpected HTTP code for tags.',
				'http_code' => $http_code,
				'repo_slug' => $repo_slug,
			);
		}

		$tags = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( empty( $tags ) || ! isset( $tags[0]['name'] ) ) {
			$result = array(
				'status' => 'success',
				'data'   => array(
					'latest_tag' => null,
					'is_pending' => false,
				),
			);
			set_transient( $transient_key, $result, HOUR_IN_SECONDS );
			return $result;
		}

		$latest_tag = ltrim( $tags[0]['name'], 'v' );

		$latest_db_rel     = $this->get_latest_db_rel();
		$latest_db_version = $latest_db_rel ? get_post_meta( $latest_db_rel->ID, 'wp2_version', true ) : '0.0.0';

		$is_pending = version_compare( $latest_tag, $latest_db_version, '>' );
		$result     = array(
			'status' => 'success',
			'data'   => array(
				'latest_tag' => $latest_tag,
				'is_pending' => $is_pending,
			),
		);
		set_transient( $transient_key, $result, HOUR_IN_SECONDS );
		return $result;
	}

	/**
	 * Gets the latest release post from the database.
	 *
	 * @return \WP_Post|null Latest release post or null if not found.
	 */
	private function get_latest_db_rel(): ?\WP_Post {
		$type          = str_replace( 'wp2_', '', $this->package_post->post_type );
		$release_query = new \WP_Query(
			array(
				'post_type'      => "wp2_{$type}_rel",
				'post_parent'    => $this->package_post->ID,
				'posts_per_page' => 1,
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);
		return $release_query->have_posts() ? $release_query->posts[0] : null;
	}
}
