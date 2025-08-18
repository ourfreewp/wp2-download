<?php
namespace WP2\Download\REST\System;

class Controller {
	public function register_routes() {
		add_action( 'rest_api_init', function () {
			register_rest_route( 'wp2/v1', '/run_health_check', [ 
				'methods' => 'POST',
				'callback' => [ $this, 'run_health_check' ],
				'permission_callback' => [ $this, 'permission_callback' ],
			] );
			register_rest_route( 'wp2/v1', '/run_all_health_checks', [ 
				'methods' => 'POST',
				'callback' => [ $this, 'run_all_health_checks' ],
				'permission_callback' => [ $this, 'permission_callback' ],
			] );
		} );
	}

	public function permission_callback() {
		return current_user_can( 'manage_options' );
	}

	public function run_health_check( $request ) {
		$nonce = $request->get_param( 'nonce' );
		if ( ! wp_verify_nonce( $nonce, 'wp2_hub_ajax' ) ) {
			return new \WP_REST_Response(
				[ 'error' => [ 'code' => 'invalid_nonce', 'message' => 'Invalid nonce' ] ],
				403
			);
		}
		// Delegate to Hub or Health system as needed
		if ( class_exists( 'WP2\Download\Admin\Hub' ) ) {
			$hub = \WP2\Download\Admin\Hub::get_instance();
			return $hub->ajax_run_health_check_rest( $request );
		}
		return new \WP_REST_Response(
			[ 'error' => [ 'code' => 'hub_unavailable', 'message' => 'Hub not available' ] ],
			500
		);
	}

	public function run_all_health_checks( $request ) {
		$nonce = $request->get_param( 'nonce' );
		if ( ! wp_verify_nonce( $nonce, 'wp2_hub_ajax' ) ) {
			return new \WP_REST_Response(
				[ 'error' => [ 'code' => 'invalid_nonce', 'message' => 'Invalid nonce' ] ],
				403
			);
		}
		if ( class_exists( 'WP2\Download\Admin\Hub' ) ) {
			$hub = \WP2\Download\Admin\Hub::get_instance();
			return $hub->ajax_run_all_health_checks_rest( $request );
		}
		return new \WP_REST_Response(
			[ 'error' => [ 'code' => 'hub_unavailable', 'message' => 'Hub not available' ] ],
			500
		);
	}
}
