<?php
namespace WP2\Download\Development;

defined( 'ABSPATH' ) || exit();

/**
 * Interface for development service adapters.
 */
interface ConnectionInterface {
	/**
	 * Connect to the development service.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function connect(): bool;
}
