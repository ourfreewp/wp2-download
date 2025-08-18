<?php
namespace WP2\Download\Health\Checks\Github;

use WP2\Download\Health\BaseCheck;

/**
 * Class GitHubCheck
 * Health check for GitHub repository status.
 *
 * @package WP2\Download\Health\Checks
 */
class RepoCheck extends BaseCheck {

	public function get_id(): string {
		return 'github_check';
	}

	protected function perform_check( bool $force = false ) {
		$links = $this->get_meta( 'wp2_links', true );
		$repo_url = $links['repository'] ?? null;

		\WP2\Download\Util\Logger::log( "GitHubCheck: repo_url=$repo_url", 'DEBUG' );

		if ( ! $repo_url || ! preg_match( '/^https?:\/\/github\.com\/([^\/]+\/[^\/]+)$/', $repo_url, $matches ) ) {
			\WP2\Download\Util\Logger::log( "GitHubCheck: Invalid or missing repo_url for post_id=" . ( $this->package_post->ID ?? 'unknown' ), 'ERROR' );
			return [ 
				'status' => 'error',
				'message' => 'Invalid or missing GitHub repository URL.',
				'repo_url' => $repo_url,
				'hint' => 'Expected format: https://github.com/owner/repo',
				'error' => 'Invalid or missing GitHub repository URL.',
				'api_response' => null,
			];
		}

		$repo_slug = $matches[1];
		$transient_key = 'wp2_github_check_' . md5( $repo_slug );

		// Always bypass cache if force is true
		if ( $force ) {
			delete_transient( $transient_key );
		}
		$cached_data = get_transient( $transient_key );
		if ( false !== $cached_data && ! $force ) {
			\WP2\Download\Util\Logger::log( "GitHubCheck: Using cached data for $repo_slug", 'DEBUG' );
			$this->update_meta_from_data( $cached_data, $repo_url );
			return [ 
				'status' => 'success',
				'source' => 'cache',
				'repo_url' => $repo_url,
				'data' => $cached_data,
				'api_response' => $cached_data,
			];
		}

		$api_url = "https://api.github.com/repos/{$repo_slug}";
		\WP2\Download\Util\Logger::log( "GitHubCheck: Preparing API request to $api_url for repo_url=$repo_url", 'DEBUG' );
		$args = [ 'timeout' => 15 ];
		if ( defined( 'WP2_GITHUB_PAT' ) && WP2_GITHUB_PAT ) {
			$args['headers'] = [ 'Authorization' => 'Bearer ' . WP2_GITHUB_PAT ];
			\WP2\Download\Util\Logger::log( "GitHubCheck: Using PAT for $repo_slug", 'DEBUG' );
		} else {
			\WP2\Download\Util\Logger::log( "GitHubCheck: No PAT defined for $repo_slug", 'WARNING' );
		}

		\WP2\Download\Util\Logger::log( "GitHubCheck: API request url=$api_url args=" . var_export( $args, true ), 'DEBUG' );
		$response = wp_remote_get( $api_url, $args );

		\WP2\Download\Util\Logger::log( "GitHubCheck: API response for $api_url: " . var_export( $response, true ), 'DEBUG' );

		if ( is_wp_error( $response ) ) {
			$error_msg = $response->get_error_message();
			\WP2\Download\Util\Logger::log( "GitHub API ERROR for {$repo_slug}: $error_msg | Args: " . var_export( $args, true ), 'ERROR' );
			return [ 
				'status' => 'error',
				'message' => 'Failed to connect to GitHub API.',
				'details' => $error_msg,
				'repo_url' => $repo_url,
				'repo_slug' => $repo_slug,
				'api_url' => $api_url,
				'args' => $args,
				'error' => $error_msg,
				'api_response' => null,
			];
		}
		$http_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $http_code ) {
			\WP2\Download\Util\Logger::log( "GitHub API HTTP ERROR for {$repo_slug}: HTTP $http_code | Args: " . var_export( $args, true ), 'ERROR' );
			\WP2\Download\Util\Logger::log( "GitHub API response body for {$repo_slug}: " . wp_remote_retrieve_body( $response ), 'ERROR' );
			return [ 
				'status' => 'error',
				'message' => 'GitHub API returned unexpected HTTP code.',
				'http_code' => $http_code,
				'repo_url' => $repo_url,
				'repo_slug' => $repo_slug,
				'api_url' => $api_url,
				'args' => $args,
				'response_body' => wp_remote_retrieve_body( $response ),
				'error' => 'GitHub API returned HTTP ' . $http_code,
				'api_response' => wp_remote_retrieve_body( $response ),
			];
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		\WP2\Download\Util\Logger::log( "GitHubCheck: API success data=" . var_export( $data, true ), 'DEBUG' );
		$this->update_meta_from_data( $data, $repo_url );
		set_transient( $transient_key, $data, HOUR_IN_SECONDS );
		return [ 
			'status' => 'success',
			'source' => 'api',
			'repo_url' => $repo_url,
			'data' => $data,
			'api_url' => $api_url,
			'args' => $args,
			'api_response' => $data,
		];
	}

	/**
	 * Helper function to update post meta from GitHub API data.
	 */
	private function update_meta_from_data( array $data, string $repo_url = '' ): void {
		$github_data = [ 
			'repo_url' => $repo_url,
			'stars' => intval( $data['stargazers_count'] ?? 0 ),
			'watchers' => intval( $data['watchers_count'] ?? 0 ),
			'open_issues' => intval( $data['open_issues_count'] ?? 0 ),
			'last_push' => sanitize_text_field( $data['pushed_at'] ?? '' ),
		];
		$this->update_meta( 'wp2_github_data', $github_data );
		$this->update_meta( 'wp2_description', sanitize_textarea_field( $data['description'] ?? '' ) );
	}
}
