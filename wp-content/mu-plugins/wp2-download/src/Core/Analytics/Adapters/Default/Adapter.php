<?php
namespace WP2\Download\Core\Analytics\Adapters\Default;

use WP2\Download\Analytics\ConnectionInterface;

/**
 * @component_id analytics_default_adapter
 * @namespace analytics.adapters
 * @type Adapter
 * @note "Default analytics adapter (no-op)."
 */
class Adapter implements ConnectionInterface {
	public function connect(): bool {
		// Always returns true for default adapter
		return true;
	}

	public function track_event( string $event_name, array $properties = array() ): void {
		// No-op for default adapter
		return;
	}
}
