<?php
// wp-content/mu-plugins/wp2-download/src/REST/Connections/Controller.php
namespace WP2\Download\REST\Connections;

defined( 'ABSPATH' ) || exit();

use WP2\Download\Services\Locator;

/**
 * @component_id rest_connections_controller
 * @namespace rest.connections
 * @type Controller
 * @note "Handles REST API routes for connection testing."
 */
class Controller {
	/**
	 * Register REST routes for connection testing.
	 */
	public static function register_routes() {
		register_rest_route(
			'wp2-download/v1',
			'/test-connection',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'test_connection' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
				'args'                => array(
					'service' => array(
						'type'     => 'string',
						'required' => true,
						'enum'     => array( 'storage', 'development', 'licensing', 'analytics' ),
					),
				),
			)
		);
	}

	/**
	 * Test the connection for a given service.
	 *
	 * @param \WP_REST_Request $request
	 * @return \WP_REST_Response
	 */
	public static function test_connection( $request ) {
		$service = $request->get_param( 'service' );
		$result  = array(
			'ok'      => false,
			'message' => '',
		);

		switch ( $service ) {
			case 'storage':
				$adapter           = Locator::storage();
				$ok                = $adapter && method_exists( $adapter, 'connect' ) ? $adapter->connect() : false;
				$result['ok']      = $ok;
				$result['message'] = $ok ? 'Storage connection successful.' : 'Storage connection failed.';
				break;
			case 'development':
				$adapter           = Locator::development();
				$ok                = $adapter && method_exists( $adapter, 'connect' ) ? $adapter->connect() : false;
				$result['ok']      = $ok;
				$result['message'] = $ok ? 'Development connection successful.' : 'Development connection failed.';
				break;
			case 'licensing':
				$adapter           = Locator::licensing();
				$ok                = $adapter && method_exists( $adapter, 'connect' ) ? $adapter->connect() : false;
				$result['ok']      = $ok;
				$result['message'] = $ok ? 'Licensing connection successful.' : 'Licensing connection failed.';
				break;
			case 'analytics':
				$adapter           = Locator::analytics();
				$ok                = $adapter && method_exists( $adapter, 'connect' ) ? $adapter->connect() : false;
				$result['ok']      = $ok;
				$result['message'] = $ok ? 'Analytics connection successful.' : 'Analytics connection failed.';
				break;
			default:
				$result['message'] = 'Unknown service.';
		}

		return new \WP_REST_Response( $result );
	}
}

add_action( 'rest_api_init', array( __NAMESPACE__ . '\Controller', 'register_routes' ) );
