<?php
// wp-content/mu-plugins/wp2-download/src/Licensing/ConnectionInterface.php
namespace WP2\Download\Licensing;

defined( 'ABSPATH' ) || exit();

/**
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