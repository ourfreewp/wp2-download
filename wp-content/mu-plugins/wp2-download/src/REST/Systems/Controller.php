<?php

/**
 * Summary of namespace WP2\Download\REST\Systems
 */

namespace WP2\Download\REST\Systems;

/**
 * REST API controller for system operations.
 *
 * @component_id rest_system_controller
 * @namespace rest.system
 * @type Controller
 * @note "Handles REST API routes for system operations."
 */
class Controller
{
    public function register_routes()
    {
        add_action(
            'rest_api_init',
            function () {
                register_rest_route(
                    'wp2/v1',
                    '/run_health_check',
                    [
                        'methods' => 'POST',
                        'callback' => [$this, 'run_health_check'],
                        'permission_callback' => [$this, 'permission_callback'],
                    ]
                );
                register_rest_route(
                    'wp2/v1',
                    '/run_all_health_checks',
                    [
                        'methods' => 'POST',
                        'callback' => [$this, 'run_all_health_checks'],
                        'permission_callback' => [$this, 'permission_callback'],
                    ]
                );
            }
        );
    }

    public function permission_callback()
    {
        return current_user_can('manage_options');
    }

    public function run_health_check($request)
    {
        $nonce = $request->get_param('nonce');
        if (!wp_verify_nonce($nonce, 'wp2_hub_ajax')) {
            return new \WP_REST_Response(
                [
                    'error' => [
                        'code' => 'invalid_nonce',
                        'message' => 'Invalid nonce',
                    ],
                ],
                403
            );
        }
        // Enqueue individual health check for all registered checks.
        $post_id = $request->get_param('post_id');
        $runner = \WP2\Download\Services\Locator::get_health_runner();
        $check_ids = $runner->get_registered_check_ids();
        foreach ($check_ids as $check_id) {
            if (function_exists('as_enqueue_async_action')) {
                \WP2\Download\Modules\Health\Scheduler::INDIVIDUAL_HOOK;
                as_enqueue_async_action(
                    \WP2\Download\Modules\Health\Scheduler::INDIVIDUAL_HOOK,
                    [
                        'check_id' => $check_id,
                        'post_id' => $post_id,
                    ],
                    \WP2\Download\Modules\Health\Scheduler::ACTION_GROUP
                );
            }
        }
        return new \WP_REST_Response(
            [
                'success' => true,
                'message' => 'Health check scheduled.',
            ],
            200
        );
    }

    public function run_all_health_checks($request)
    {
        $nonce = $request->get_param('nonce');
        if (!wp_verify_nonce($nonce, 'wp2_hub_ajax')) {
            return new \WP_REST_Response(
                [
                    'error' => [
                        'code' => 'invalid_nonce',
                        'message' => 'Invalid nonce',
                    ],
                ],
                403
            );
        }
        // Enqueue the main Action Scheduler event for all health checks.
        if (function_exists('as_enqueue_async_action')) {
            as_enqueue_async_action(\WP2\Download\Modules\Health\Scheduler::MAIN_HOOK, [], \WP2\Download\Modules\Health\Scheduler::ACTION_GROUP);
        }
        return new \WP_REST_Response(
            [
                'success' => true,
                'message' => 'Scheduled all health checks.',
            ],
            200
        );
    }
}
