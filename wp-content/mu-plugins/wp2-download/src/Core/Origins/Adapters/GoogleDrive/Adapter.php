<?php

/**
 * Origin Adapter: Google Drive
 *
 * @package WP2\Download
 */

namespace WP2\Download\Core\Origins\Adapters\GoogleDrive;

use WP2\Download\Core\Origins\Adapters\ConnectionInterface;

/**
 * Google Drive Adapter
 *
 * @component_id origin_google_drive_adapter
 * @namespace origin.adapters.googledrive
 * @type Adapter
 * @note "Adapter for Google Drive file operations."
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
        return 'gdrive';
    }

    public function get_label(): string
    {
        /**
         * Get the label for the adapter.
         */
        return 'Google Drive';
    }

    public function validate_source_ref(array $source_ref): bool
    {
        /**
         * Validate the source reference array.
         */
        $file_id = isset($source_ref['file_id']) ? (string) $source_ref['file_id'] : '';
        if ($file_id === '') {
            $this->last_error = ['message' => 'Missing Google Drive file_id.'];
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
            'name' => $source_ref['file_id'] ?? '',
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
        // Drive files may not expose semantic versions; surface a single logical "current".
        return ['current'];
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
        // Often not permitted; default to false unless policy explicitly allows.
        return false;
    }

    public function default_update_mode(array $source_ref): string
    {
        return 'direct_vendor';
    }

    public function get_last_error()
    {
        return $this->last_error;
    }
}
