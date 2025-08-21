<?php

/**
 * Summary of namespace WP2\Download\REST\Systems\Client
 */

namespace WP2\Download\REST\Systems\Client;

use WP2\Download\Config;

/**
 * REST API controller for client reporting.
 *
 * @component_id rest_client_controller
 * @namespace rest.client
 * @type Controller
 * @note "Handles REST API routes for client reporting."
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
                    '/report-in',
                    [
                        'methods' => 'POST',
                        'callback' => [$this, 'handle_client_report'],
                        'permission_callback' => function () {
                            return current_user_can('manage_options');
                        },
                    ]
                );
            }
        );
    }

    public function handle_client_report($request)
    {
        $params = $request->get_json_params();
        $slug = sanitize_title($params['slug'] ?? '');
        $version = sanitize_text_field($params['version'] ?? '');
        $site_url = esc_url_raw($params['site_url'] ?? '');
        if (!$slug || !$version || !$site_url) {
            return new \WP_REST_Response(
                [
                    'error' => [
                        'code' => 'missing_parameters',
                        'message' => 'Missing required parameters.',
                    ],
                ],
                400
            );
        }
        $parent_post = get_page_by_path($slug, OBJECT, Config::WP2_POST_TYPE_PLUGIN);
        if (!$parent_post) {
            $parent_post = get_page_by_path($slug, OBJECT, Config::WP2_POST_TYPE_THEME);
        }
        if (!$parent_post) {
            $parent_post = get_page_by_path($slug, OBJECT, Config::WP2_POST_TYPE_MU);
        }
        if (!$parent_post) {
            return new \WP_REST_Response(
                [
                    'error' => [
                        'code' => 'package_not_found',
                        'message' => 'Package not found.',
                    ],
                ],
                404
            );
        }
        $sites = get_post_meta($parent_post->ID, Config::WP2_META_VERSION, true);
        if (!is_array($sites)) {
            $sites = [];
        }
        $sites[$site_url] = [
            'version' => $version,
            'last_reported' => current_time('mysql'),
        ];
        update_post_meta($parent_post->ID, Config::WP2_META_VERSION, $sites);
        return new \WP_REST_Response(['success' => true], 200);
    }
}
