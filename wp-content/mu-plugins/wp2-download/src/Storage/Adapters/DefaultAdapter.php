<?php
namespace WP2\Download\Storage\Adapters;

use WP2\Download\Storage\ConnectionInterface;

class DefaultAdapter implements ConnectionInterface {
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
