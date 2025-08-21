<?php

/**
 * Fallback adapter for storage connections.
 *
 * @package WP2\Download\Core\Storage\Adapters\Fallback
 */

namespace WP2\Download\Core\Storage\Adapters\Fallback;

use WP2\Download\Core\Storage\ConnectionInterface;

/**
 * Fallback adapter for storage connections.
 *
 * @component_id storage_default_adapter
 * @namespace storage
 * @type Adapter
 * @note "Default storage adapter implementation."
 */
class Adapter implements ConnectionInterface {

	public function connect(): bool {
		// Always returns true for default adapter.
		return true;
	}

	public function store( string $key, $value ): bool {
		// No-op for default adapter.
		return true;
	}

	public function retrieve( string $key ) {
		// No-op for default adapter.
		return null;
	}
}
