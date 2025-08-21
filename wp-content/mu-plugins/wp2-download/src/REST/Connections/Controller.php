<?php

/**
 * Summary of namespace WP2\Download\REST\Connections
 */

namespace WP2\Download\REST\Connections;

use WP2\Download\Services\Locator;

/**
 * REST API controller for managing connections.
 *
 * @component_id rest_connections_controller
 * @namespace rest.connections
 * @type Controller
 * @note "Handles REST API routes for connection testing."
 */
class Controller
{
    /**
     * Register REST routes for connection testing.
     */
    public static function register_routes()
    {
        register_rest_route(
            'wp2-download/v1',
            '/test-connection',
            [
                'methods' => 'POST',
                'callback' => [__CLASS__, 'test_connection'],
                'permission_callback' => function () {
                    return current_user_can('manage_options');
                },
                'args' => [
                    'service' => [
                        'type' => 'string',
                        'required' => true,
                        'enum' => ['storage', 'development', 'licensing', 'analytics'],
                    ],
                ],
            ]
        );
    }

    /**
     * Test the connection for a given service.
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public static function test_connection($request)
    {
        $service = $request->get_param('service');
        $result = [
            'ok' => false,
            'message' => '',
        ];

        $services = [
            'storage' => [
                'method' => 'storage',
                'success' => 'Storage connection successful.',
                'failure' => 'Storage connection failed.',
            ],
            'development' => [
                'method' => 'development',
                'success' => 'Development connection successful.',
                'failure' => 'Development connection failed.',
            ],
            'licensing' => [
                'method' => 'licensing',
                'success' => 'Licensing connection successful.',
                'failure' => 'Licensing connection failed.',
            ],
            'analytics' => [
                'method' => 'analytics',
                'success' => 'Analytics connection successful.',
                'failure' => 'Analytics connection failed.',
            ],
        ];

        if (!isset($services[$service])) {
            $result['message'] = 'Unknown service.';
            return new \WP_REST_Response($result);
        }

        $result = self::check_service_connection($service, $services[$service]);
        return new \WP_REST_Response($result);
    }

    /**
     * Checks the connection for a specific service.
     *
     * @param string $service
     * @param array $service_config
     * @return array
     */
    private static function check_service_connection($service, $service_config)
    {
        $adapter = Locator::{ $service_config['method'] }();
        $ok = $adapter && method_exists($adapter, 'connect') ? $adapter->connect() : false;

        return [
            'ok' => $ok,
            'message' => $ok ? $service_config['success'] : $service_config['failure'],
        ];
    }
}
