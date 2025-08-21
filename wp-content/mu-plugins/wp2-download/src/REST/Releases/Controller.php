<?php

/**
 * Summary of namespace WP2\Download\REST\Releases
 */

namespace WP2\Download\REST\Releases;

use WP2\Download\Config;
use WP2\Download\Core\Releases\Channels\Manager as Channel;

/**
 * REST API controller for release ingestion.
 *
 * @component_id rest_releases_controller
 * @namespace rest.releases
 * @type Controller
 * @note "Handles REST API routes for release ingestion."
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
                    '/ingest-release',
                    [
                        'methods' => 'POST',
                        'callback' => [$this, 'handle_ingest_release'],
                        'permission_callback' => [$this, 'permission_callback'],
                    ]
                );
            }
        );
    }

    public function permission_callback($request)
    {
        $token = (string) ($request->get_header('x-wp2-token') ?? '');
        $want = defined('WP2_HUB_INGEST_TOKEN') ? WP2_HUB_INGEST_TOKEN : '';
        return !empty($want) && hash_equals($want, $token);
    }

    public function handle_ingest_release($request)
    {
        $params = $request->get_json_params();
        $type = sanitize_key($params['type'] ?? '');
        $slug = sanitize_title($params['slug'] ?? '');
        $version = sanitize_text_field($params['version'] ?? '');
        $r2_key = sanitize_text_field($params['r2_key'] ?? '');
        $channel = sanitize_key($params['channel'] ?? Channel::STABLE);

        if (!Channel::is_valid($channel)) {
            $channel = Channel::STABLE;
        }

        if (!$type || !$slug || !$version || !$r2_key) {
            return $this->error_response('missing_parameters', 'Missing required parameters.', 400);
        }

        $parent_id = $this->get_or_create_parent_post($slug);
        if (is_wp_error($parent_id) || !$parent_id) {
            return $this->error_response('parent_package_error', 'Could not find or create parent package.', 500);
        }

        $release_id = $this->create_release_post($slug, $version, $parent_id);
        if (is_wp_error($release_id)) {
            return $this->error_response('release_post_error', 'Could not create release post.', 500);
        }

        $this->update_release_meta($release_id, $version, $r2_key, $channel);

        return new \WP_REST_Response(
            [
                'success' => true,
                'release_id' => $release_id,
            ],
            201
        );
    }

    private function error_response($code, $message, $status)
    {
        return new \WP_REST_Response(
            [
                'error' => [
                    'code' => $code,
                    'message' => $message,
                ],
            ],
            $status
        );
    }

    private function get_or_create_parent_post($slug)
    {
        $parent_post_type = Config::WP2_POST_TYPE_PLUGIN;
        $parent_post = get_page_by_path($slug, OBJECT, $parent_post_type);
        if ($parent_post) {
            return $parent_post->ID;
        }
        return wp_insert_post(
            [
                'post_type' => $parent_post_type,
                'post_title' => $slug,
                'post_name' => $slug,
                'post_status' => Config::WP2_POST_STATUS_PUBLISH,
            ]
        );
    }

    private function create_release_post($slug, $version, $parent_id)
    {
        return wp_insert_post(
            [
                'post_type' => Config::WP2_POST_TYPE_PLUGIN_REL,
                'post_parent' => $parent_id,
                'post_title' => "{$slug} - {$version}",
                'post_status' => Config::WP2_POST_STATUS_PUBLISH,
            ]
        );
    }

    private function update_release_meta($release_id, $version, $r2_key, $channel)
    {
        update_post_meta($release_id, Config::WP2_META_VERSION, $version);
        update_post_meta($release_id, Config::WP2_META_R2_FILE_KEY, $r2_key);
        update_post_meta($release_id, Config::WP2_META_CHANNEL, $channel);
    }
}
