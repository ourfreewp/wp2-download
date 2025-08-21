<?php

/**
 * Summary of namespace WP2\Download\Core\Storage
 */

namespace WP2\Download\Core\Storage;

/**
 * Interface for storage connection adapters.
 *
 * @component_id storage_connection_interface
 * @namespace storage
 * @type Interface
 * @note "Interface for storage connection adapters."
 */
interface ConnectionInterface {

	/**
	 * Connect to the storage service.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function connect(): bool;
}
