<?php
/**
 * Origin Adapter: Google Drive
 *
 * @package WP2\Download
 */

namespace WP2\Download\Origin\Adapters\GoogleDrive;

defined( 'ABSPATH' ) || exit;

use WP2\Download\Origin\Adapters\ConnectionInterface;

/**
 * Google Drive file adapter.
 */
class Adapter implements ConnectionInterface {

	/** @var mixed */
	protected $last_error = null;

	public function get_kind(): string {
		return 'gdrive';
	}

	public function get_label(): string {
		return 'Google Drive';
	}

	public function validate_source_ref( array $source_ref ): bool {
		$file_id = isset( $source_ref['file_id'] ) ? (string) $source_ref['file_id'] : '';
		if ( $file_id === '' ) {
			$this->last_error = [ 'message' => 'Missing Google Drive file_id.' ];
			return false;
		}
		return true;
	}

	public function fetch_metadata( array $source_ref ): array {
		return [
			'name'        => $source_ref['file_id'] ?? '',
			'description' => '',
			'links'       => [],
			'requires'    => [],
		];
	}

	public function fetch_versions( array $source_ref, array $constraints = [] ): array {
		// Drive files may not expose semantic versions; surface a single logical "current".
		return [ 'current' ];
	}

	public function resolve_artifact( array $source_ref, string $version ): array {
		return [
			'url'      => '',
			'headers'  => [],
			'checksum' => '',
		];
	}

	public function supports_mirror( array $source_ref ): bool {
		// Often not permitted; default to false unless policy explicitly allows.
		return false;
	}

	public function default_update_mode( array $source_ref ): string {
		return 'direct_vendor';
	}

	public function get_last_error() {
		return $this->last_error;
	}
}