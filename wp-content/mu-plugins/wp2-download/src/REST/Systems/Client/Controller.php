<?php
namespace WP2\Download\REST\Systems\Client;

use WP2\Download\Config;

/**
 * @component_id rest_client_controller
 * @namespace rest.client
 * @type Controller
 * @note "Handles REST API routes for client reporting."
 */
class Controller {
	public function register_routes() {
		add_action(
			'rest_api_init',
			function () {
				register_rest_route(
					'wp2/v1',
					'/report-in',
					array(
						'methods'             => 'POST',
						'callback'            => array( $this, 'handle_client_report' ),
						'permission_callback' => function () {
							return current_user_can( 'manage_options' );
						},
					)
				);
			}
		);
	}

	public function handle_client_report( $request ) {
		$params   = $request->get_json_params();
		$slug     = sanitize_title( $params['slug'] ?? '' );
		$version  = sanitize_text_field( $params['version'] ?? '' );
		$site_url = esc_url_raw( $params['site_url'] ?? '' );
		if ( ! $slug || ! $version || ! $site_url ) {
			return new \WP_REST_Response(
				array(
					'error' => array(
						'code'    => 'missing_parameters',
						'message' => 'Missing required parameters.',
					),
				),
				400
			);
		}
		$parent_post = get_page_by_path( $slug, OBJECT, Config::WP2_POST_TYPE_PLUGIN );
		if ( ! $parent_post ) {
			$parent_post = get_page_by_path( $slug, OBJECT, Config::WP2_POST_TYPE_THEME );
		}
		if ( ! $parent_post ) {
			$parent_post = get_page_by_path( $slug, OBJECT, Config::WP2_POST_TYPE_MU );
		}
		if ( ! $parent_post ) {
			return new \WP_REST_Response(
				array(
					'error' => array(
						'code'    => 'package_not_found',
						'message' => 'Package not found.',
					),
				),
				404
			);
		}
		$sites = get_post_meta( $parent_post->ID, Config::WP2_META_VERSION, true );
		if ( ! is_array( $sites ) ) {
			$sites = array();
		}
		$sites[ $site_url ] = array(
			'version'       => $version,
			'last_reported' => current_time( 'mysql' ),
		);
		update_post_meta( $parent_post->ID, Config::WP2_META_VERSION, $sites );
		return new \WP_REST_Response( array( 'success' => true ), 200 );
	}
}
