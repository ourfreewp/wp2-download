<?php
namespace WP2\Download\Modules\Health;

use WP2\Download\Services\Locator;

/**
 * @component_id health_scheduler
 * @namespace health
 * @type Service
 * @note "Schedules health checks using Action Scheduler."
 */
class Scheduler {
	const MAIN_HOOK       = 'wp2_schedule_all_health_checks';
	const INDIVIDUAL_HOOK = 'wp2_run_individual_health_check';
	const ACTION_GROUP    = 'wp2-download-health';

	public function register_hooks(): void {
		// Hook into Action Scheduler's initialization
		add_action( 'action_scheduler_init', array( $this, 'schedule_main_event' ) );

		// Add the handler for the main event
		add_action( self::MAIN_HOOK, array( $this, 'spawn_individual_check_actions' ) );

		// Add the handler for the individual check events
		add_action( self::INDIVIDUAL_HOOK, array( $this, 'handle_individual_check_action' ), 10, 2 );
	}

	public function schedule_main_event(): void {
		if ( ! function_exists( 'as_has_scheduled_action' ) ) {
			return;
		}
		if ( ! as_has_scheduled_action( self::MAIN_HOOK, array(), self::ACTION_GROUP ) ) {
			as_schedule_recurring_action( time(), DAY_IN_SECONDS, self::MAIN_HOOK, array(), self::ACTION_GROUP );
		}
	}

	public function spawn_individual_check_actions(): void {
		$packages_query = new \WP_Query(
			array(
				'post_type'      => array( 'wp2_plugin', 'wp2_theme', 'wp2_mu' ),
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);

		if ( ! $packages_query->have_posts() ) {
			return;
		}

		$health_runner = Locator::get_health_runner();
		$check_ids     = $health_runner->get_registered_check_ids();

		foreach ( $packages_query->posts as $post_id ) {
			foreach ( $check_ids as $check_id ) {
				// Enqueue an async action for each specific check on each package
				as_enqueue_async_action(
					self::INDIVIDUAL_HOOK,
					array(
						'check_id' => $check_id,
						'post_id'  => $post_id,
					),
					self::ACTION_GROUP
				);
			}
		}
	}

	public function handle_individual_check_action( string $check_id, int $post_id ): void {
		$runner = Locator::get_health_runner();
		$runner->run_check( $check_id, $post_id );
	}
}
