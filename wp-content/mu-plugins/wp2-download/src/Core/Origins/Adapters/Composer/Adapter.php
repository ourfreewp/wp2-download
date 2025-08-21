<?php

/**
 * Origin Adapter: Composer
 *
 * @package WP2\Download
 */

namespace WP2\Download\Core\Origins\Adapters\Composer;

use WP2\Download\Core\Origins\Adapters\ConnectionInterface;

/**
 * Composer Adapter
 *
 * @component_id origin_composer_adapter
 * @namespace origin.adapters.composer
 * @type Adapter
 * @note "Adapter for Composer/Packagist repositories."
 */
class Adapter implements ConnectionInterface
{
    protected $last_error = null;

    public function get_kind(): string
    {
        return 'composer';
    }

    public function get_label(): string
    {
        return 'Composer / Packagist';
    }

    public function validate_source_ref(array $source_ref): bool
    {
        $pkg = isset($source_ref['package']) ? (string) $source_ref['package'] : '';
        if ($pkg === '' || !preg_match('/^[a-z0-9._-]+\/[a-z0-9._-]+$/', $pkg)) {
            $this->last_error = ['message' => 'Invalid composer package name.'];
            return false;
        }
        return true;
    }

    public function fetch_metadata(array $source_ref): array
    {
        // Placeholder: return normalized metadata shape.
        return [
            'name' => $source_ref['package'] ?? '',
            'description' => '',
            'links' => [],
            'requires' => [],
        ];
    }

    public function fetch_versions(array $source_ref, array $constraints = []): array
    {
        // Placeholder: newest-first list.
        return [];
    }

    public function resolve_artifact(array $source_ref, string $version): array
    {
        // Placeholder: dist URL + checksum if available.
        return [
            'url' => '',
            'headers' => [],
            'checksum' => '',
        ];
    }

    public function supports_mirror(array $source_ref): bool
    {
        // Default stance: allowed when license/terms permit (caller enforces policy).
        return true;
    }

    public function default_update_mode(array $source_ref): string
    {
        return 'hub_mirrored';
    }

    public function get_last_error()
    {
        return $this->last_error;
    }
}
