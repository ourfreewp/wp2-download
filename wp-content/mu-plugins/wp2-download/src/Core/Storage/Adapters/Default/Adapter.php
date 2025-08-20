<?php
namespace WP2\Download\Core\Storage\Adapters\Default;

use WP2\Download\Storage\ConnectionInterface;

/**
 * @component_id storage_default_adapter
 * @namespace storage
 * @type Adapter
 * @note "Default storage adapter implementation."
 */
class Adapter implements ConnectionInterface {
	public function connect(): bool {
		// Always returns true for default adapter
		return true;
	}

	public function store( string $key, $value ): bool {
		// No-op for default adapter
		return true;
	}

	public function retrieve( string $key ) {
		// No-op for default adapter
		return null;
	}
}
