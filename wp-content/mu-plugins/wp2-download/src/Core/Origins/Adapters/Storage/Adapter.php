<?php

/**
 * Origin Adapter: Storage (Hub-owned)
 *
 * @package WP2\Download
 */

namespace WP2\Download\Core\Origins\Adapters\Storage;

use WP2\Download\Core\Origins\Adapters\ConnectionInterface;

/**
 * Storage Adapter
 *
 * @component_id origin_storage_adapter
 * @namespace origin.adapters.storage
 * @type Adapter
 * @note "Adapter for self-hosted object storage (R2/S3)."
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
        return 'hub';
    }

    public function get_label(): string
    {
        /**
         * Get the label for the adapter.
         */
        return 'Hub Storage';
    }

    public function validate_source_ref(array $source_ref): bool
    {
        /**
         * Validate the source reference array.
         */
        $key = isset($source_ref['r2_key']) ? (string) $source_ref['r2_key'] : '';
        if ($key === '') {
            $this->last_error = ['message' => 'Missing storage key (r2_key).'];
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
            'name' => $source_ref['r2_key'] ?? '',
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
        // For a single-key artifact, use explicit versions in path or metadata.
        return [];
    }

    public function resolve_artifact(array $source_ref, string $version): array
    {
        /**
         * Resolve the artifact for the given source reference and version.
         */
        // Presigning is done elsewhere; return a logical key and let the gateway handle URL creation.
        return [
            'url' => '',
            'headers' => [],
            'checksum' => '',
        ];
    }

    public function supports_mirror(array $source_ref): bool
    {
        // Already authoritative; mirroring conceptually N/A.
        return true;
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
