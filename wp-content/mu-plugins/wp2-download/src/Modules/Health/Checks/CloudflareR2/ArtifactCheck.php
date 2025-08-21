<?php

/**
 * Artifact health check for Cloudflare R2.
 *
 * @package WP2_Download
 */

namespace WP2\Download\Modules\Health\Checks\CloudflareR2;

use Aws\S3\S3Client;
use WP2\Download\Modules\Health\BaseCheck;

/**
 * Cloudflare R2 Artifact Check
 *
 * @component_id health_r2_artifact_check
 * @namespace health.checks.cloudflarer2
 * @type Check
 * @note "Verifies R2 artifacts for all releases."
 */
class ArtifactCheck extends BaseCheck
{
    /**
     * Returns the check ID.
     *
     * @return string
     */
    public function get_id(): string
    {
        return 'r2_artifact_check';
    }

    /**
     * Runs the health check for R2 artifacts.
     *
     * @param bool $force Whether to bypass any caches.
     * @return array Health check result.
     */
    protected function perform_check(bool $force = false)
    {
        $type = str_replace('wp2_', '', $this->package_post->post_type);
        $release_query = $this->get_release_query($type);
        if (!$release_query->have_posts()) {
            return $this->success_result();
        }
        $config_error = $this->validate_r2_config();
        if ($config_error) {
            return $config_error;
        }
        $s3 = $this->init_s3_client();
        if (is_wp_error($s3)) {
            return [
                'status' => 'error',
                'message' => 'Failed to initialize S3 client.',
                'details' => $s3->get_error_message(),
            ];
        }
        $missing = $this->find_missing_artifacts($release_query->posts, $s3);
        $status = empty($missing) ? 'success' : 'error';
        return [
            'status' => $status,
            'data' => ['missing_artifacts' => $missing],
        ];
    }

    private function get_release_query($type)
    {
        return new \WP_Query(
            [
                'post_type' => "wp2_{$type}_rel",
                'post_parent' => $this->package_post->ID,
                'posts_per_page' => -1,
            ]
        );
    }

    private function success_result()
    {
        return [
            'status' => 'success',
            'data' => ['missing_artifacts' => []],
        ];
    }

    private function validate_r2_config()
    {
        if (!defined('WP2_DOWNLOAD_R2_BUCKET') || !defined('WP2_DOWNLOAD_R2_ACCOUNT_ID') || !defined('WP2_DOWNLOAD_R2_ACCESS_KEY') || !defined('WP2_DOWNLOAD_R2_SECRET_KEY')) {
            return [
                'status' => 'error',
                'message' => 'R2 bucket is not configured.',
                'details' => [
                    'WP2_DOWNLOAD_R2_BUCKET' => defined('WP2_DOWNLOAD_R2_BUCKET') ? WP2_DOWNLOAD_R2_BUCKET : null,
                    'WP2_DOWNLOAD_R2_ACCOUNT_ID' => defined('WP2_DOWNLOAD_R2_ACCOUNT_ID') ? WP2_DOWNLOAD_R2_ACCOUNT_ID : null,
                    'WP2_DOWNLOAD_R2_ACCESS_KEY' => defined('WP2_DOWNLOAD_R2_ACCESS_KEY') ? WP2_DOWNLOAD_R2_ACCESS_KEY : null,
                    'WP2_DOWNLOAD_R2_SECRET_KEY' => defined('WP2_DOWNLOAD_R2_SECRET_KEY') ? WP2_DOWNLOAD_R2_SECRET_KEY : null,
                ],
            ];
        }
        return null;
    }

    private function init_s3_client()
    {
        try {
            return new S3Client(
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
        } catch (\Exception $e) {
            return new \WP_Error('s3_init_failed', $e->getMessage());
        }
    }

    private function find_missing_artifacts($release_posts, $s3)
    {
        $missing = [];
        foreach ($release_posts as $release_post) {
            $r2_key = get_post_meta($release_post->ID, 'wp2_r2_file_key', true);
            try {
                if ($r2_key && !$s3->doesObjectExist(WP2_DOWNLOAD_R2_BUCKET, $r2_key)) {
                    $missing[] = get_post_meta($release_post->ID, 'wp2_version', true);
                }
            } catch (\Exception $e) {
                $missing[] = [
                    'version' => get_post_meta($release_post->ID, 'wp2_version', true),
                    'error' => $e->getMessage(),
                    'r2_key' => $r2_key,
                ];
            }
        }
        return $missing;
    }
}
