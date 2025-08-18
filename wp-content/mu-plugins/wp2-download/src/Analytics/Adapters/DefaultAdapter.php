<?php
namespace WP2\Download\Analytics\Adapters;

use WP2\Download\Analytics\ConnectionInterface;

class DefaultAdapter implements ConnectionInterface {
	public function connect(): bool {
		// Always returns true for default adapter
		return true;
	}

	public function track_event( string $event_name, array $properties = [] ): void {
		// No-op for default adapter
		return;
	}
}
