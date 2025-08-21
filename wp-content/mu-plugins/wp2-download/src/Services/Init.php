<?php

/**
 * Summary of namespace WP2\Download\Services
 */

namespace WP2\Download\Services;

/**
 * Initializes services and their dependencies.
 *
 * @component_id services_init
 * @namespace services
 * @type Bootstrap
 * @note "Registers settings and helpers for bootstrap."
 */
class Init
{
    /**
     * Register wp2_download_settings and related fields.
     *
     * @return void
     */
    public static function init(): void
    {
        add_action(
            'admin_init',
            static function (): void {
                register_setting(
                    'wp2_download_settings',
                    'wp2_download_storage_adapter',
                    [
                        'type' => 'string',
                        'sanitize_callback' => 'sanitize_text_field',
                    ]
                );
                register_setting(
                    'wp2_download_settings',
                    'wp2_download_development_adapter',
                    [
                        'type' => 'string',
                        'sanitize_callback' => 'sanitize_text_field',
                    ]
                );
                register_setting(
                    'wp2_download_settings',
                    'wp2_download_licensing_adapter',
                    [
                        'type' => 'string',
                        'sanitize_callback' => 'sanitize_text_field',
                    ]
                );
                register_setting(
                    'wp2_download_settings',
                    'wp2_download_analytics_adapter',
                    [
                        'type' => 'string',
                        'sanitize_callback' => 'sanitize_text_field',
                    ]
                );

                // Storage tab options.
                register_setting(
                    'wp2_download_settings',
                    'wp2_r2_account_id',
                    [
                        'type' => 'string',
                        'sanitize_callback' => 'sanitize_text_field',
                    ]
                );
                register_setting(
                    'wp2_download_settings',
                    'wp2_r2_access_key',
                    [
                        'type' => 'string',
                        'sanitize_callback' => 'sanitize_text_field',
                    ]
                );
                register_setting(
                    'wp2_download_settings',
                    'wp2_r2_secret_key',
                    [
                        'type' => 'string',
                        'sanitize_callback' => 'sanitize_text_field',
                    ]
                );
                register_setting(
                    'wp2_download_settings',
                    'wp2_r2_bucket_name',
                    [
                        'type' => 'string',
                        'sanitize_callback' => 'sanitize_text_field',
                    ]
                );

                // Development tab options.
                register_setting(
                    'wp2_download_settings',
                    'wp2_github_pat',
                    [
                        'type' => 'string',
                        'sanitize_callback' => 'sanitize_text_field',
                    ]
                );
                register_setting(
                    'wp2_download_settings',
                    'wp2_github_org',
                    [
                        'type' => 'string',
                        'sanitize_callback' => 'sanitize_text_field',
                    ]
                );

                // Licensing tab options.
                register_setting(
                    'wp2_download_settings',
                    'wp2_keygen_account_id',
                    [
                        'type' => 'string',
                        'sanitize_callback' => 'sanitize_text_field',
                    ]
                );
                register_setting(
                    'wp2_download_settings',
                    'wp2_keygen_product_token',
                    [
                        'type' => 'string',
                        'sanitize_callback' => 'sanitize_text_field',
                    ]
                );

                // Analytics tab options.
                register_setting(
                    'wp2_download_settings',
                    'wp2_posthog_api_key',
                    [
                        'type' => 'string',
                        'sanitize_callback' => 'sanitize_text_field',
                    ]
                );
                register_setting(
                    'wp2_download_settings',
                    'wp2_posthog_api_url',
                    [
                        'type' => 'string',
                        'sanitize_callback' => 'sanitize_text_field',
                    ]
                );

                // (Reserved) Origin-related initialization could be added here in the future.
            }
        );
    }

    /**
     * Get origin snapshot info from a package manifest.
     *
     * Accepts a normalized manifest array that includes:
     *   - origin.kind        (string)
     *   - origin.source_ref  (array)
     *
     * Returns a light-weight snapshot suitable for UI or logs:
     *   [
     *     'ok'                  => bool,
     *     'kind'                => string,
     *     'label'               => string,
     *     'supports_mirror'     => bool,
     *     'default_update_mode' => string,
     *     'metadata'            => array,
     *     'versions'            => array,
     *     'error'               => string (when ok = false)
     *   ]
     *
     * @param array $manifest Package manifest data.
     * @return array
     */
    public static function get_origin_snapshot(array $manifest): array
    {
        $kind = self::extract_kind($manifest);
        $source_ref = self::extract_source_ref($manifest);
        if ($kind === '') {
            return [
                'ok' => false,
                'error' => 'Missing origin.kind',
            ];
        }
        $adapter = self::resolve_adapter($kind);
        if (!$adapter) {
            return [
                'ok' => false,
                'kind' => $kind,
                'error' => 'Unsupported origin kind',
            ];
        }
        if (empty($source_ref)) {
            return [
                'ok' => false,
                'kind' => $kind,
                'error' => 'Missing origin.source_ref',
            ];
        }
        if (!self::validate_source_ref($adapter, $source_ref)) {
            return [
                'ok' => false,
                'kind' => $kind,
                'error' => 'Invalid origin.source_ref',
            ];
        }
        $label = self::get_adapter_label($adapter, $kind);
        $supports_mirror = self::get_supports_mirror($adapter, $source_ref);
        $default_update_mode = self::get_default_update_mode($adapter, $source_ref);
        $metadata = self::get_metadata($adapter, $source_ref);
        $versions = self::get_versions($adapter, $source_ref);
        return [
            'ok' => true,
            'kind' => $kind,
            'label' => $label,
            'supports_mirror' => $supports_mirror,
            'default_update_mode' => $default_update_mode,
            'metadata' => $metadata,
            'versions' => $versions,
        ];
    }

    private static function extract_kind(array $manifest): string
    {
        return isset($manifest['origin']['kind']) ? strtolower((string) $manifest['origin']['kind']) : '';
    }

    private static function extract_source_ref(array $manifest): array
    {
        return isset($manifest['origin']['source_ref']) && is_array($manifest['origin']['source_ref'])
            ? $manifest['origin']['source_ref']
            : [];
    }

    private static function resolve_adapter(string $kind)
    {
        try {
            return \WP2\Download\Services\Locator::origin($kind);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private static function validate_source_ref($adapter, $source_ref): bool
    {
        return !(method_exists($adapter, 'validate_source_ref') && !$adapter->validate_source_ref($source_ref));
    }

    private static function get_adapter_label($adapter, $kind): string
    {
        return method_exists($adapter, 'get_label')
            ? (string) $adapter->get_label()
            : ucfirst($kind);
    }

    private static function get_supports_mirror($adapter, $source_ref): bool
    {
        return method_exists($adapter, 'supports_mirror')
            ? (bool) $adapter->supports_mirror($source_ref)
            : false;
    }

    private static function get_default_update_mode($adapter, $source_ref): string
    {
        return method_exists($adapter, 'default_update_mode')
            ? (string) $adapter->default_update_mode($source_ref)
            : '';
    }

    private static function get_metadata($adapter, $source_ref): array
    {
        return method_exists($adapter, 'fetch_metadata')
            ? (array) $adapter->fetch_metadata($source_ref)
            : [];
    }

    private static function get_versions($adapter, $source_ref): array
    {
        return method_exists($adapter, 'fetch_versions')
            ? (array) $adapter->fetch_versions($source_ref, [])
            : [];
    }
}
