<?php
namespace WP2\Download\Development\Adapters;

use WP2\Download\Development\ConnectionInterface;

class DefaultAdapter implements ConnectionInterface {
	public function connect(): bool {
		// Always returns true for default adapter
		return true;
	}

	public function get_environment(): string {
		// No-op for default adapter
		return 'production';
	}
}
