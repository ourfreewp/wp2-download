<?php

/**
 * Summary of namespace WP2\Download\Core\Development
 */

namespace WP2\Download\Core\Development;

/**
 * Development connection interface.
 *
 * @component_id development_connection_interface
 * @namespace development
 * @type Interface
 * @note "Interface for development service adapters."
 */
interface ConnectionInterface {

	/**
	 * Connect to the development service.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function connect(): bool;
}
