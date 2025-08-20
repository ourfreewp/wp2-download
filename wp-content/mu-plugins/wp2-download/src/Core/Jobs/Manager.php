<?php
namespace WP2\Download\Core\Jobs;

defined( 'ABSPATH' ) || exit;

/**
 * @component_id jobs_manager
 * @namespace jobs
 * @type Service
 * @note "Manager for scheduled actions (jobs) using Action Scheduler."
 */
class Manager {
	/**
	 * Get scheduled actions.
	 *
	 * @param array  $args Arguments for as_get_scheduled_actions.
	 * @param string $return_format Format: OBJECT, ARRAY_A, or ids.
	 * @return array
	 */
	public function get_scheduled_actions( array $args = array(), string $return_format = 'OBJECT' ): array {
		if ( ! did_action( 'action_scheduler_init' ) ) {
			return array();
		}
		return as_get_scheduled_actions( $args, $return_format );
	}

	/**
	 * Schedule a single action.
	 */
	public function schedule_single_action( int $timestamp, string $hook, array $args = array(), string $group = '', bool $unique = false, int $priority = 10 ): int {
		if ( ! did_action( 'action_scheduler_init' ) ) {
			return 0;
		}
		return as_schedule_single_action( $timestamp, $hook, $args, $group, $unique, $priority );
	}

	/**
	 * Schedule a recurring action.
	 */
	public function schedule_recurring_action( int $timestamp, int $interval, string $hook, array $args = array(), string $group = '', bool $unique = false, int $priority = 10 ): int {
		if ( ! did_action( 'action_scheduler_init' ) ) {
			return 0;
		}
		return as_schedule_recurring_action( $timestamp, $interval, $hook, $args, $group, $unique, $priority );
	}

	/**
	 * Schedule a cron action.
	 */
	public function schedule_cron_action( int $timestamp, string $schedule, string $hook, array $args = array(), string $group = '', bool $unique = false, int $priority = 10 ): int {
		if ( ! did_action( 'action_scheduler_init' ) ) {
			return 0;
		}
		return as_schedule_cron_action( $timestamp, $schedule, $hook, $args, $group, $unique, $priority );
	}

	/**
	 * Enqueue an async action.
	 */
	public function enqueue_async_action( string $hook, array $args = array(), string $group = '', bool $unique = false, int $priority = 10 ): int {
		if ( ! did_action( 'action_scheduler_init' ) ) {
			return 0;
		}
		return as_enqueue_async_action( $hook, $args, $group, $unique, $priority );
	}

	/**
	 * Unschedule the next occurrence of an action.
	 */
	public function unschedule_action( string $hook, array $args = array(), string $group = '' ): void {
		if ( ! did_action( 'action_scheduler_init' ) ) {
			return;
		}
		as_unschedule_action( $hook, $args, $group );
	}

	/**
	 * Unschedule all occurrences of an action.
	 */
	public function unschedule_all_actions( string $hook, array $args = array(), string $group = '' ) {
		if ( ! did_action( 'action_scheduler_init' ) ) {
			return null;
		}
		return as_unschedule_all_actions( $hook, $args, $group );
	}

	/**
	 * Get the next scheduled timestamp for an action.
	 */
	public function next_scheduled_action( string $hook, array $args = array(), string $group = '' ) {
		if ( ! did_action( 'action_scheduler_init' ) ) {
			return false;
		}
		return as_next_scheduled_action( $hook, $args, $group );
	}

	/**
	 * Check if an action is scheduled.
	 */
	public function has_scheduled_action( string $hook, array $args = array(), string $group = '' ): bool {
		if ( ! did_action( 'action_scheduler_init' ) ) {
			return false;
		}
		return as_has_scheduled_action( $hook, $args, $group );
	}

	/**
	 * Check if Action Scheduler supports a feature.
	 */
	public function supports( string $feature ): bool {
		return function_exists( 'as_supports' ) ? as_supports( $feature ) : false;
	}
}
