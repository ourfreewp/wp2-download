<?php
namespace WP2\Download\REST\Jobs;

use WP2\Download\Admin\Jobs;

defined( 'ABSPATH' ) || exit;

/**
 * @component_id rest_jobs_controller
 * @namespace rest.jobs
 * @type Controller
 * @note "REST controller for managing scheduled jobs (actions)."
 */
class Controller {
	/**
	 * Register REST routes.
	 */
	public function register_routes() {
		register_rest_route(
			'wp2-download/v1',
			'/jobs',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_jobs' ),
				'permission_callback' => array( $this, 'permissions' ),
			)
		);
		register_rest_route(
			'wp2-download/v1',
			'/jobs/(?P<id>\\d+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_job' ),
				'permission_callback' => array( $this, 'permissions' ),
			)
		);
		register_rest_route(
			'wp2-download/v1',
			'/jobs/(?P<id>\\d+)/unschedule',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'unschedule_job' ),
				'permission_callback' => array( $this, 'permissions' ),
			)
		);
	}

	/**
	 * Permissions for jobs endpoints.
	 */
	public function permissions() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * List jobs.
	 */
	public function get_jobs( $request ) {
		$jobs_admin = new Jobs();
		$args       = array();
		if ( $request->get_param( 'status' ) ) {
			$args['status'] = $request->get_param( 'status' );
		}
		if ( $request->get_param( 'group' ) ) {
			$args['group'] = $request->get_param( 'group' );
		}
		$jobs = $jobs_admin->get_jobs( $args );
		return rest_ensure_response( $jobs );
	}

	/**
	 * Get a single job, with drilldown to package/adapter if present.
	 */
	public function get_job( $request ) {
		$id         = (int) $request['id'];
		$jobs_admin = new Jobs();
		$jobs       = $jobs_admin->get_jobs();
		foreach ( $jobs as $job ) {
			if ( $job['ID'] == $id ) {
				$response = $job;
				// Add package/adapter drilldown if present
				if ( ! empty( $job['args']['package'] ) ) {
					$response['package'] = $job['args']['package'];
				}
				if ( ! empty( $job['args']['adapter'] ) ) {
					$response['adapter'] = $job['args']['adapter'];
				}
				return rest_ensure_response( $response );
			}
		}
		return new \WP_Error( 'not_found', 'Job not found', array( 'status' => 404 ) );
	}

	/**
	 * Unschedule a job.
	 */
	public function unschedule_job( $request ) {
		$id         = (int) $request['id'];
		$jobs_admin = new Jobs();
		$success    = $jobs_admin->unschedule_job( $id );
		if ( $success ) {
			return rest_ensure_response( array( 'success' => true ) );
		}
		return new \WP_Error( 'unschedule_failed', 'Could not unschedule job', array( 'status' => 400 ) );
	}
}
