<?php
// wp-content/mu-plugins/wp2-download/src/Storage/ConnectionInterface.php
namespace WP2\Download\Storage;

defined( 'ABSPATH' ) || exit();

/**
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