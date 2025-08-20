<?php
namespace WP2\Download\REST\Packages;

use WP2\Download\Config;

/**
 * @component_id rest_packages_controller
 * @namespace rest.packages
 * @type Controller
 * @note "Handles REST API routes for package info."
 */
class Controller {
	public function register_routes() {
		add_action(
			'rest_api_init',
			function () {
				register_rest_route(
					'wp2/v1',
					'/packages/(?P<type>[a-z\-]+)/(?P<slug>[a-z0-9\-]+)',
					array(
						'methods'             => 'GET',
						'callback'            => array( $this, 'get_latest_package_info' ),
						'permission_callback' => function () {
							return current_user_can( 'manage_options' );
						},
					)
				);
			}
		);
	}

	public function get_latest_package_info( $request ) {
		$type             = $request->get_param( 'type' );
		$slug             = $request->get_param( 'slug' );
		$parent_post_type = Config::WP2_POST_TYPE_PLUGIN;
		$parent_post      = get_page_by_path( $slug, OBJECT, $parent_post_type );
		if ( ! $parent_post || Config::WP2_POST_STATUS_PUBLISH !== $parent_post->post_status ) {
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
		$release_post_type = Config::WP2_POST_TYPE_PLUGIN_REL;
		$release_query     = new \WP_Query(
			array(
				'post_type'      => $release_post_type,
				'post_status'    => Config::WP2_POST_STATUS_PUBLISH,
				'post_parent'    => $parent_post->ID,
				'posts_per_page' => 1,
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);
		if ( ! $release_query->have_posts() ) {
			return new \WP_REST_Response(
				array(
					'error' => array(
						'code'    => 'no_releases_found',
						'message' => 'No releases found.',
					),
				),
				404
			);
		}
		$release_post = $release_query->posts[0];
		$version      = get_post_meta( $release_post->ID, Config::WP2_META_VERSION, true );
		return new \WP_REST_Response( array( 'version' => $version ), 200 );
	}
}
