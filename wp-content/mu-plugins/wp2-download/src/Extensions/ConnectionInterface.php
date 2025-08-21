<?php

/**
 * Summary of namespace WP2\Download\Extensions
 */

namespace WP2\Download\Extensions;

/**
 * Manages connections to external services.
 *
 * @component_id extensions_connection_interface
 * @namespace extensions
 * @type Interface
 * @note "Standard interface for all core and extension adapters."
 */
interface ConnectionInterface {

	public function get_id(): string;

	public function get_label(): string;

	public function is_configured(): bool;

	public function test_connection(): bool;
}
