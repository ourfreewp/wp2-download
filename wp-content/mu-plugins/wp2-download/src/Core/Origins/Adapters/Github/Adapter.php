<?php
/**
 * Origin Adapter: GitHub
 *
 * @package WP2\Download
 */

namespace WP2\Download\Core\Origins\Adapters\Github;

defined( 'ABSPATH' ) || exit;

use WP2\Download\Origin\Adapters\ConnectionInterface;

/**
 * @component_id origin_github_adapter
 * @namespace origin.adapters.github
 * @type Adapter
 * @note "Adapter for GitHub releases and tags."
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
			$this->last_error = array( 'message' => 'Missing owner/repo.' );
			return false;
		}
		return true;
	}

	public function fetch_metadata( array $source_ref ): array {
		return array(
			'name'        => "{$source_ref['owner']}/{$source_ref['repo']}",
			'description' => '',
			'links'       => array( 'source' => 'https://github.com/' . ( $source_ref['owner'] ?? '' ) . '/' . ( $source_ref['repo'] ?? '' ) ),
			'requires'    => array(),
		);
	}

	public function fetch_versions( array $source_ref, array $constraints = array() ): array {
		return array();
	}

	public function resolve_artifact( array $source_ref, string $version ): array {
		return array(
			'url'      => '',
			'headers'  => array(),
			'checksum' => '',
		);
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
