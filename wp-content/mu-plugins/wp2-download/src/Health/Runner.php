<?php
namespace WP2\Download\Health;

class Runner {

	public const ACTION_HOOK = 'wp2_run_package_health_checks';
	private $checks = [];

	public function register_hooks(): void {
		add_action( self::ACTION_HOOK, [ $this, 'run_checks' ] );
	}

	public function register_check( CheckInterface $check ): void {
		$this->checks[ $check->get_id()] = $check;
	}

	/**
	 * Class Runner
	 * Manages and executes health checks for package posts.
	 *
	 * @package WP2\Download\Health
	 */
	public function run_checks( int $post_id, bool $force = false ): void {
	\WP2\Download\Util\Logger::log('Runner::run_checks called for post_id=' . $post_id . ', force=' . ($force ? 'true' : 'false'), 'DEBUG');
	$post = get_post( $post_id );
	\WP2\Download\Util\Logger::log('Runner::run_checks got post: ' . print_r($post, true), 'DEBUG');
		if ( ! $post || empty( $this->checks ) ) {
			error_log('DEBUG: Runner::run_checks early return: post or checks missing.');
			return;
		}

		$results = [];
		foreach ( $this->checks as $check_id => $check ) {
			error_log('DEBUG: Running health check: ' . $check_id . ' for post_id=' . $post_id);
			$result = $check->run( $post, $force );
			error_log('DEBUG: Result for ' . $check_id . ': ' . print_r($result, true));
			$results[ $check_id ] = $result;
		}

		error_log('DEBUG: All health check results for post_id=' . $post_id . ': ' . print_r($results, true));
		update_post_meta( $post_id, 'wp2_health_check_results', $results );
		update_post_meta( $post_id, 'wp2_last_health_check', time() );
	}
}
