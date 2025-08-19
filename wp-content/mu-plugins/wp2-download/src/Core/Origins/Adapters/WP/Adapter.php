<?php
/**
 * Origin Adapter: WordPress.org
 *
 * @package WP2\Download
 */

namespace WP2\Download\Origin\Adapters\WP;

defined( 'ABSPATH' ) || exit;

use WP2\Download\Origin\Adapters\ConnectionInterface;

/**
 * @component_id origin_wp_adapter
 * @namespace origin.adapters.wp
 * @type Adapter
 * @note "Adapter for WordPress.org plugins/themes directory."
 */
class Adapter implements ConnectionInterface {

	/** @var mixed */
	protected $last_error = null;

	public function get_kind(): string {
		return 'wporg';
	}

	public function get_label(): string {
		return 'WordPress.org';
	}

	public function validate_source_ref( array $source_ref ): bool {
		$slug = isset( $source_ref['slug'] ) ? (string) $source_ref['slug'] : '';
		if ( $slug === '' ) {
			$this->last_error = [ 'message' => 'Missing wp.org slug.' ];
			return false;
		}
		return true;
	}

	public function fetch_metadata( array $source_ref ): array {
		return [ 
			'name' => $source_ref['slug'] ?? '',
			'description' => '',
			'links' => [],
			'requires' => [],
		];
	}

	public function fetch_versions( array $source_ref, array $constraints = [] ): array {
		return [];
	}

	public function resolve_artifact( array $source_ref, string $version ): array {
		return [ 
			'url' => '',
			'headers' => [],
			'checksum' => '',
		];
	}

	public function supports_mirror( array $source_ref ): bool {
		// Default to no mirroring unless explicitly configured to respect terms.
		return false;
	}

	public function default_update_mode( array $source_ref ): string {
		return 'native_wp';
	}

	public function get_last_error() {
		return $this->last_error;
	}
}