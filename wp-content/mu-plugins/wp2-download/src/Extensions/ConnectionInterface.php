<?php
namespace WP2\Download\Extensions;

/**
 * Standard interface for all core and extension adapters.
 */
interface ConnectionInterface {
	public function get_id(): string;
	public function get_label(): string;
	public function is_configured(): bool;
	public function test_connection(): bool;
}
