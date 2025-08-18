<?php
// wp-content/mu-plugins/wp2-download/src/Storage/ConnectionInterface.php
namespace WP2\Download\Storage;

defined( 'ABSPATH' ) || exit();

/**
 * Interface for storage connection adapters.
 */
interface ConnectionInterface {
	/**
	 * Connect to the storage service.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function connect(): bool;
}