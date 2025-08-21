<?php

/**
 * Origin Adapter: GitHub
 *
 * @package WP2\Download
 */

namespace WP2\Download\Core\Origins\Adapters\Github;

use WP2\Download\Core\Origins\Adapters\ConnectionInterface;

/**
 * GitHub Adapter
 *
 * @component_id origin_github_adapter
 * @namespace origin.adapters.github
 * @type Adapter
 * @note "Adapter for GitHub releases and tags."
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
        return 'github';
    }

    public function get_label(): string
    {
        /**
         * Get the label for the adapter.
         */
        return 'GitHub';
    }

    public function validate_source_ref(array $source_ref): bool
    {
        /**
         * Validate the source reference array.
         */
        $owner = isset($source_ref['owner']) ? (string) $source_ref['owner'] : '';
        $repo = isset($source_ref['repo']) ? (string) $source_ref['repo'] : '';
        if ($owner === '' || $repo === '') {
            $this->last_error = ['message' => 'Missing owner/repo.'];
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
            'name' => "{$source_ref['owner']}/{$source_ref['repo']}",
            'description' => '',
            'links' => ['source' => 'https://github.com/' . ($source_ref['owner'] ?? '') . '/' . ($source_ref['repo'] ?? '')],
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
        // Mirroring typically allowed for public repos; private repos depend on license/terms.
        return true;
    }

    public function default_update_mode(array $source_ref): string
    {
        // MU workflows will often be hub-managed; default remains mirrored for general use.
        return 'hub_mirrored';
    }

    public function get_last_error()
    {
        return $this->last_error;
    }
}
