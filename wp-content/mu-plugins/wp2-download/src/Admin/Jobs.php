<?php
namespace WP2\Download\Admin;

use WP2\Download\Jobs\Manager;

defined( 'ABSPATH' ) || exit;

/**
 * Admin Jobs logic for listing and managing scheduled actions.
 */
class Jobs {
	/**
	 * Jobs manager instance.
	 * @var Manager
	 */
	protected $manager;

	public function __construct() {
		$this->manager = new Manager();
	}

	/**
	 * Get jobs for display in admin UI.
	 *
	 * @param array $args Optional filter args (hook, group, status, etc.)
	 * @return array
	 */
	public function get_jobs( array $args = [] ): array {
		// Default: show pending and in-progress jobs, 20 per page
		$defaults = [ 
			'status' => '', // Show all statuses
			'per_page' => 20,
			'orderby' => 'date',
			'order' => 'DESC',
		];
		$args = array_merge( $defaults, $args );
		return $this->manager->get_scheduled_actions( $args, 'ARRAY_A' );
	}

	/**
	 * Get available statuses for jobs table filter.
	 * @return array
	 */
	public function get_statuses(): array {
		return [ 
			'pending' => __( 'Pending', 'wp2-download' ),
			'in-progress' => __( 'In Progress', 'wp2-download' ),
			'complete' => __( 'Complete', 'wp2-download' ),
			'failed' => __( 'Failed', 'wp2-download' ),
			'canceled' => __( 'Canceled', 'wp2-download' ),
		];
	}

	/**
	 * Unschedule a job by action ID.
	 * @param int $action_id
	 * @return bool
	 */
	public function unschedule_job( int $action_id ): bool {
		if ( ! did_action( 'action_scheduler_init' ) ) {
			return false;
		}
		if ( function_exists( 'as_unschedule_action_by_id' ) ) {
			as_unschedule_action_by_id( $action_id );
			return true;
		}
		return false;
	}
}