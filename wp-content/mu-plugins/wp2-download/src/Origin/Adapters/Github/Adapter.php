<?php
/**
 * Origin Adapter: GitHub
 *
 * @package WP2\Download
 */

namespace WP2\Download\Origin\Adapters\Github;

defined( 'ABSPATH' ) || exit;

use WP2\Download\Origin\Adapters\ConnectionInterface;

/**
 * GitHub releases/tags adapter.
 */
class Adapter implements ConnectionInterface {

	/** @var mixed */
	protected $last_error = null;

	public function get_kind(): string {
		return 'github';
	}

	public function get_label(): string {
		return 'GitHub';
	}

	public function validate_source_ref( array $source_ref ): bool {
		$owner = isset( $source_ref['owner'] ) ? (string) $source_ref['owner'] : '';
	$repo  = isset( $source_ref['repo'] ) ? (string) $source_ref['repo'] : '';
		if ( $owner === '' || $repo === '' ) {
			$this->last_error = [ 'message' => 'Missing owner/repo.' ];
			return false;
		}
		return true;
	}

	public function fetch_metadata( array $source_ref ): array {
		return [
			'name'        => "{$source_ref['owner']}/{$source_ref['repo']}",
			'description' => '',
			'links'       => [ 'source' => 'https://github.com/' . ($source_ref['owner'] ?? '') . '/' . ($source_ref['repo'] ?? '') ],
			'requires'    => [],
		];
	}

	public function fetch_versions( array $source_ref, array $constraints = [] ): array {
		return [];
	}

	public function resolve_artifact( array $source_ref, string $version ): array {
		return [
			'url'      => '',
			'headers'  => [],
			'checksum' => '',
		];
	}

	public function supports_mirror( array $source_ref ): bool {
		// Mirroring typically allowed for public repos; private repos depend on license/terms.
		return true;
	}

	public function default_update_mode( array $source_ref ): string {
		// MU workflows will often be hub-managed; default remains mirrored for general use.
		return 'hub_mirrored';
	}

	public function get_last_error() {
		return $this->last_error;
	}
}