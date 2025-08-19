<?php
namespace WP2\Download\Licensing\Adapters\Default;

use WP2\Download\Licensing\ConnectionInterface;
/**
 * @component_id licensing_default_adapter
 * @namespace licensing
 * @type Adapter
 * @note "Default licensing adapter implementation."
 */
class Adapter implements ConnectionInterface {

	public function connect(): bool {
		// Always returns true for default adapter
		return true;
	}

	public function validate_license( string $license ): bool {
		// No-op for default adapter
		return true;
	}
}
