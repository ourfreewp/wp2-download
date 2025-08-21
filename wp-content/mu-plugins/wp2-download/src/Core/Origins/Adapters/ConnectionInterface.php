<?php

/**
 * Origin Adapters: ConnectionInterface
 *
 * @package WP2\Download
 */

namespace WP2\Download\Core\Origins\Adapters;

/**
 * Connection Interface
 *
 * @component_id origin_connection_interface
 * @namespace origin.adapters
 * @type Interface
 * @note "Contract for all origin connections (wp.org, composer, github, storage, etc)."
 */
interface ConnectionInterface
{
    /**
     * Unique, stable identifier for this origin (e.g., 'wporg', 'composer', 'github', 'storage', 'gdrive').
     *
     * @return string
     */
    public function get_kind(): string;

    /**
     * Human-friendly label for admin UI.
     *
     * @return string
     */
    public function get_label(): string;

    /**
     * Validate the origin-specific source reference payload.
     *
     * @param array $source_ref Origin identifier (shape depends on kind).
     * @return bool True if valid and minimally complete.
     */
    public function validate_source_ref(array $source_ref): bool;

    /**
     * Fetch normalized metadata for this package from the origin.
     *
     * @param array $source_ref Origin identifier.
     * @return array Associative array (name, description, links, requires, etc.).
     */
    public function fetch_metadata(array $source_ref): array;

    /**
     * Discover available versions (optionally filtered by constraints).
     *
     * @param array $source_ref Origin identifier.
     * @param array $constraints Optional constraints (e.g., channel, semver range).
     * @return array List of version strings (newest-first recommended).
     */
    public function fetch_versions(array $source_ref, array $constraints = []): array;

    /**
     * Resolve the artifact to deliver for a specific version.
     *
     * @param array  $source_ref Origin identifier.
     * @param string $version    Chosen version.
     * @return array { url: string, headers?: array, checksum?: string, signature?: array }
     */
    public function resolve_artifact(array $source_ref, string $version): array;

    /**
     * Whether the origin permits mirroring artifacts in the hub (policy/ToS awareness).
     *
     * @param array $source_ref Origin identifier.
     * @return bool
     */
    public function supports_mirror(array $source_ref): bool;

    /**
     * Recommended default update mode for this origin (e.g., native_wp, hub_mirrored, direct_vendor, hub_managed).
     *
     * @param array $source_ref Origin identifier.
     * @return string
     */
    public function default_update_mode(array $source_ref): string;

    /**
     * Retrieve the last error raised during an operation (implementation-defined).
     *
     * @return mixed Null when no error; otherwise an error object/array.
     */
    public function get_last_error();
}
