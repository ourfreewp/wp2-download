<?php

/**
 * Summary of namespace WP2\Download\Core\Licensing
 */

namespace WP2\Download\Core\Licensing;

/**
 * Licensing connection interface.
 *
 * @component_id licensing_connection_interface
 * @namespace licensing
 * @type Interface
 * @note "Interface for licensing connection adapters."
 */
interface ConnectionInterface {

	/**
	 * Connect to the licensing service.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function connect(): bool;
}
