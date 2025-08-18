<?php
namespace WP2\Download\Licensing\Adapters;

use WP2\Download\Licensing\ConnectionInterface;

class DefaultAdapter implements ConnectionInterface {
	public function connect(): bool {
		// Always returns true for default adapter
		return true;
	}

	public function validate_license( string $license ): bool {
		// No-op for default adapter
		return true;
	}
}
