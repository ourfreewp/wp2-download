<?php

/**
 * Origin Adapter: WordPress.org
 *
 * @package WP2\Download
 */

namespace WP2\Download\Core\Origins\Adapters\WP;

use WP2\Download\Core\Origins\Adapters\ConnectionInterface;

/**
 * WordPress.org Adapter
 *
 * @component_id origin_wp_adapter
 * @namespace origin.adapters.wp
 * @type Adapter
 * @note "Adapter for WordPress.org plugins/themes directory."
 */
class Adapter implements ConnectionInterface
{
    /**
     * Last error encountered by the adapter.
     *
     * @var mixed
     */
    protected $last_error = null;

    public function get_kind(): string
    {
        /**
         * Get the kind of adapter.
         */
        return 'wporg';
    }

    public function get_label(): string
    {
        /**
         * Get the label for the adapter.
         */
        return 'WordPress.org';
    }

    public function validate_source_ref(array $source_ref): bool
    {
        /**
         * Validate the source reference array.
         */
        $slug = isset($source_ref['slug']) ? (string) $source_ref['slug'] : '';
        if ($slug === '') {
            $this->last_error = ['message' => 'Missing wp.org slug.'];
            return false;
        }
        return true;
    }

    public function fetch_metadata(array $source_ref): array
    {
        /**
         * Fetch metadata for the given source reference.
         */
        return [
            'name' => $source_ref['slug'] ?? '',
            'description' => '',
            'links' => [],
            'requires' => [],
        ];
    }

    public function fetch_versions(array $source_ref, array $constraints = []): array
    {
        /**
         * Fetch available versions for the given source reference and constraints.
         */
        return [];
    }

    public function resolve_artifact(array $source_ref, string $version): array
    {
        /**
         * Resolve the artifact for the given source reference and version.
         */
        return [
            'url' => '',
            'headers' => [],
            'checksum' => '',
        ];
    }

    public function supports_mirror(array $source_ref): bool
    {
        /**
         * Check if the adapter supports mirroring for the given source reference.
         */
        // Default to no mirroring unless explicitly configured to respect terms.
        return false;
    }

    public function default_update_mode(array $source_ref): string
    {
        return 'native_wp';
    }

    public function get_last_error()
    {
        return $this->last_error;
    }
}
