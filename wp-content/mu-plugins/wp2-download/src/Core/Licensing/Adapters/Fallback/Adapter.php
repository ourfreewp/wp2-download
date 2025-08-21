<?php

/**
 * Fallback adapter for licensing connections.
 *
 * @package WP2\Download\Core\Licensing\Adapters\Fallback
 */

namespace WP2\Download\Core\Licensing\Adapters\Fallback;

use WP2\Download\Core\Licensing\ConnectionInterface;

/**
 * Fallback adapter for licensing connections.
 *
 * @component_id licensing_default_adapter
 * @namespace licensing.adapters
 * @type Adapter
 * @note "Default licensing adapter (no-op)."
 */
class Adapter implements ConnectionInterface {

	public function connect(): bool {
		// Always returns true for default adapter.
		return true;
	}

	public function validate_license( string $license ): bool {
		// No-op for default adapter.
		return true;
	}
}
