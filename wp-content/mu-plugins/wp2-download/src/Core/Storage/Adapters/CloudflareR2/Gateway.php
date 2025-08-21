<?php

/**
 * Summary of namespace WP2\Download\Core\Storage\Adapters\CloudflareR2
 */

namespace WP2\Download\Core\Storage\Adapters\CloudflareR2;

use Aws\S3\S3Client;

/**
 * Gateway for Cloudflare R2 storage.
 *
 * @component_id gateway_cloudflarer2
 * @namespace Gateways
 * @type Gateway
 * @note "Serves package downloads from Cloudflare R2."
 */
class Gateway
{
    public static function serve()
    {
        global $wp_query;
        $type = $wp_query->get('wp2_package_type');
        $slug = $wp_query->get('wp2_package_slug');
        $version = $wp_query->get('wp2_package_version');
        if (!$type || !$slug || !$version) {
            return;
        }
        $r2_key = self::get_r2_key_for_release($type, $slug, $version);
        if (!$r2_key) {
            $wp_query->set_404();
            status_header(404);
            return;
        }
        // Credentials should be read from Config.
        if (!defined('WP2_DOWNLOAD_R2_ACCOUNT_ID') || !defined('WP2_DOWNLOAD_R2_ACCESS_KEY') || !defined('WP2_DOWNLOAD_R2_SECRET_KEY') || !defined('WP2_DOWNLOAD_R2_BUCKET')) {
            status_header(503);
            echo 'Error: Download service is not configured correctly.';
            exit;
        }
        try {
            $s3 = new S3Client(
                [
                    'region' => 'auto',
                    'endpoint' => defined('WP2_DOWNLOAD_R2_S3_ENDPOINT') ? WP2_DOWNLOAD_R2_S3_ENDPOINT : '',
                    'version' => 'latest',
                    'credentials' => [
                        'key' => defined('WP2_DOWNLOAD_R2_ACCESS_KEY') ? WP2_DOWNLOAD_R2_ACCESS_KEY : '',
                        'secret' => defined('WP2_DOWNLOAD_R2_SECRET_KEY') ? WP2_DOWNLOAD_R2_SECRET_KEY : '',
                    ],
                ]
            );
            $command = $s3->getCommand(
                'GetObject',
                [
                    'Bucket' => WP2_DOWNLOAD_R2_BUCKET,
                    'Key' => $r2_key,
                ]
            );
            $presigned_url = (string) $s3->createPresignedRequest($command, '+5 minutes')->getUri();
            wp_safe_redirect(esc_url_raw($presigned_url));
            exit;
        } catch (\AwsException $e) {
            $wp_query->set_404();
            status_header(404);
            return;
        }
    }

    public static function get_r2_key_for_release($type, $slug, $version)
    {
        $allowed_types = ['plugin', 'theme', 'mu'];
        if (!in_array($type, $allowed_types, true)) {
            return null;
        }
        $parent_post_type = 'wp2_' . sanitize_key($type);
        $parent_post = get_page_by_path($slug, OBJECT, $parent_post_type);
        if ($parent_post === null || $parent_post->post_status !== 'publish') {
            return null;
        }
        $release_post_type = $parent_post_type . '_rel';
        $release_query = new \WP_Query(
            [
                'post_type' => $release_post_type,
                'post_status' => 'publish',
                'post_parent' => $parent_post->ID,
                'posts_per_page' => 1,
                'no_found_rows' => true,
                'fields' => 'ids',
                'update_post_meta_cache' => false,
                'update_post_term_cache' => false,
                'orderby' => 'date',
                'order' => 'DESC',
                'tax_query' => [
                    [
                        'taxonomy' => 'wp2_rel_version',
                        'field' => 'name',
                        'terms' => sanitize_text_field((string) $version),
                    ],
                ],
            ]
        );
        if (!$release_query->have_posts()) {
            return null;
        }
        $release_post_id = (int) $release_query->posts[0];
        $r2_key = get_post_meta($release_post_id, 'wp2_r2_file_key', true);
        return $r2_key ? $r2_key : null;
    }
}
