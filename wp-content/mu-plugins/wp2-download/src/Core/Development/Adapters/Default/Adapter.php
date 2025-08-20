<?php
namespace WP2\Download\Core\Development\Adapters\Default;

use WP2\Download\Core\Development\ConnectionInterface;

/**
 * @component_id development_default_adapter
 * @namespace development.adapters
 * @type Adapter
 * @note "Default development adapter (no-op)."
 */
class Adapter implements ConnectionInterface {
	public function connect(): bool {
		// Always returns true for default adapter
		return true;
	}

	public function get_environment(): string {
		// No-op for default adapter
		return 'production';
	}
}
