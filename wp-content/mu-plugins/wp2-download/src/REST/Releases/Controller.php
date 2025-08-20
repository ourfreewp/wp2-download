<?php
namespace WP2\Download\REST\Releases;

use WP2\Download\Config;
use WP2\Download\Release\Channel;

/**
 * @component_id rest_releases_controller
 * @namespace rest.releases
 * @type Controller
 * @note "Handles REST API routes for release ingestion."
 */
class Controller {
	public function register_routes() {
		add_action(
			'rest_api_init',
			function () {
				register_rest_route(
					'wp2/v1',
					'/ingest-release',
					array(
						'methods'             => 'POST',
						'callback'            => array( $this, 'handle_ingest_release' ),
						'permission_callback' => array( $this, 'permission_callback' ),
					)
				);
			}
		);
	}

	public function permission_callback( $request ) {
		$token = (string) ( $request->get_header( 'x-wp2-token' ) ?? '' );
		$want  = defined( 'WP2_HUB_INGEST_TOKEN' ) ? WP2_HUB_INGEST_TOKEN : '';
		return ! empty( $want ) && hash_equals( $want, $token );
	}

	public function handle_ingest_release( $request ) {
		$params  = $request->get_json_params();
		$type    = sanitize_key( $params['type'] ?? '' );
		$slug    = sanitize_title( $params['slug'] ?? '' );
		$version = sanitize_text_field( $params['version'] ?? '' );
		$r2_key  = sanitize_text_field( $params['r2_key'] ?? '' );
		$channel = sanitize_key( $params['channel'] ?? Channel::STABLE );
		if ( ! Channel::is_valid( $channel ) ) {
			$channel = Channel::STABLE;
		}
		if ( ! $type || ! $slug || ! $version || ! $r2_key ) {
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
		$parent_post_type = Config::WP2_POST_TYPE_PLUGIN;
		$parent_post      = get_page_by_path( $slug, OBJECT, $parent_post_type );
		$parent_id        = $parent_post ? $parent_post->ID : wp_insert_post(
			array(
				'post_type'   => $parent_post_type,
				'post_title'  => $slug,
				'post_name'   => $slug,
				'post_status' => Config::WP2_POST_STATUS_PUBLISH,
			)
		);
		if ( is_wp_error( $parent_id ) || ! $parent_id ) {
			return new \WP_REST_Response(
				array(
					'error' => array(
						'code'    => 'parent_package_error',
						'message' => 'Could not find or create parent package.',
					),
				),
				500
			);
		}
		$release_id = wp_insert_post(
			array(
				'post_type'   => Config::WP2_POST_TYPE_PLUGIN_REL,
				'post_parent' => $parent_id,
				'post_title'  => "{$slug} - {$version}",
				'post_status' => Config::WP2_POST_STATUS_PUBLISH,
			)
		);
		if ( is_wp_error( $release_id ) ) {
			return new \WP_REST_Response(
				array(
					'error' => array(
						'code'    => 'release_post_error',
						'message' => 'Could not create release post.',
					),
				),
				500
			);
		}
		update_post_meta( $release_id, Config::WP2_META_VERSION, $version );
		update_post_meta( $release_id, Config::WP2_META_R2_FILE_KEY, $r2_key );
		update_post_meta( $release_id, Config::WP2_META_CHANNEL, $channel );
		return new \WP_REST_Response(
			array(
				'success'    => true,
				'release_id' => $release_id,
			),
			201
		);
	}
}
