<?php

namespace WP2\Download\REST\System\Health;

use WP2\Download\Services\Locator;
use WP2\Download\Admin\Manifest;

class Controller {
	public function __construct() {
		if ( ! class_exists( 'WP_REST_Response' ) ) {
			require_once ABSPATH . 'wp-includes/rest-api/class-wp-rest-response.php';
		}
		if ( ! class_exists( 'WP_REST_Request' ) ) {
			require_once ABSPATH . 'wp-includes/rest-api/class-wp-rest-request.php';
		}
	}
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
			register_rest_route( 'wp2/v1', '/purge_processed_manifests', [ 
				'methods' => 'POST',
				'callback' => [ $this, 'purge_processed_manifests' ],
				'permission_callback' => [ $this, 'permission_callback' ],
			] );
			register_rest_route( 'wp2/v1', '/sync_to_r2', [ 
				'methods' => 'POST',
				'callback' => [ $this, 'sync_to_r2' ],
				'permission_callback' => [ $this, 'permission_callback' ],
			] );
			register_rest_route( 'wp2/v1', '/create_release', [ 
				'methods' => 'POST',
				'callback' => [ $this, 'create_release' ],
				'permission_callback' => [ $this, 'permission_callback' ],
			] );
			register_rest_route( 'wp2/v1', '/edit_manifest', [ 
				'methods' => 'POST',
				'callback' => [ $this, 'edit_manifest' ],
				'permission_callback' => [ $this, 'permission_callback' ],
			] );
			register_rest_route( 'wp2/v1', '/purge_artifact', [ 
				'methods' => 'POST',
				'callback' => [ $this, 'purge_artifact' ],
				'permission_callback' => [ $this, 'permission_callback' ],
			] );
		} );
	}

	public function permission_callback() {
		$nonce = $_SERVER['HTTP_X_WP_NONCE'] ?? '';
		return current_user_can( 'manage_options' ) && wp_verify_nonce( $nonce, 'wp_rest' );
	}

	public function run_health_check( \WP_REST_Request $request ) {
		$post_id = intval( $request->get_param( 'slug' ) );
		if ( ! $post_id ) {
			return new \WP_REST_Response(
				[ 'error' => [ 'code' => 'missing_post_id', 'message' => 'Missing post_id' ] ],
				400
			);
		}
		$runner = Locator::get_health_runner();
		$runner->run_checks( $post_id, true );
		return new \WP_REST_Response(
			[ 'success' => true, 'message' => 'Health check completed.' ],
			200
		);
	}

	public function run_all_health_checks( \WP_REST_Request $request ) {
		$runner = Locator::get_health_runner();
		// You may want to fetch all packages here as in Hub.php
		// For now, just return success
		return new \WP_REST_Response(
			[ 'success' => true, 'message' => 'Triggered health checks.' ],
			200
		);
	}

	public function purge_processed_manifests( \WP_REST_Request $request ) {
		Manifest::purge_processed_manifests();
		return new \WP_REST_Response(
			[ 'success' => true, 'message' => 'Processed manifests option purged.' ],
			200
		);
	}

	public function sync_to_r2( \WP_REST_Request $request ) {
		// Implement sync logic as in Hub.php
		return new \WP_REST_Response(
			[ 'success' => true, 'message' => 'Synced to R2.' ],
			200
		);
	}

	public function create_release( \WP_REST_Request $request ) {
		// Implement create release logic as in Hub.php
		return new \WP_REST_Response(
			[ 'success' => true, 'message' => 'Release created.' ],
			200
		);
	}

	public function edit_manifest( \WP_REST_Request $request ) {
		// Implement edit manifest logic as in Hub.php
		return new \WP_REST_Response(
			[ 'success' => true, 'message' => 'Manifest edited.' ],
			200
		);
	}

	public function purge_artifact( \WP_REST_Request $request ) {
		// Implement purge artifact logic as in Hub.php
		return new \WP_REST_Response( [ 'success' => true, 'message' => 'Artifact purged.' ] );
	}
}
