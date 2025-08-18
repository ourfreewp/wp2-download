<?php
namespace WP2\Download\Health;


class Runner {
	private $checks = [];

	/**
	 * Register a health check instance.
	 * @param CheckInterface $check
	 */
	public function register_check( CheckInterface $check ): void {
		$this->checks[ $check->get_id()] = $check;
		\WP2\Download\Util\Logger::log( 'Runner: Registered health check ' . $check->get_id(), 'DEBUG' );
	}

	/**
	 * Get all registered health check IDs.
	 * @return array
	 */
	public function get_registered_check_ids(): array {
		return array_keys( $this->checks );
	}

	/**
	 * Runs a single, specific health check for a given package post.
	 * This method is called by the Action Scheduler handler.
	 * @param string $check_id
	 * @param int $post_id
	 */
	public function run_check( string $check_id, int $post_id ): void {
		$post = get_post( $post_id );

		if ( ! $post || ! isset( $this->checks[ $check_id ] ) ) {
			\WP2\Download\Util\Logger::log( 'Runner: Invalid post or check_id for run_check: ' . $check_id . ', post_id=' . $post_id, 'ERROR' );
			return;
		}

		$check = $this->checks[ $check_id ];
		$result = $check->run( $post, true ); // Force fresh data for scheduled checks

		// Update meta with the result for this specific check
		$all_results = get_post_meta( $post_id, 'wp2_health_check_results', true );
		if ( ! is_array( $all_results ) ) {
			$all_results = [];
		}
		$all_results[ $check_id ] = $result;

		update_post_meta( $post_id, 'wp2_health_check_results', $all_results );
		update_post_meta( $post_id, 'wp2_last_health_check', time() );
	}
}
