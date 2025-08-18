<?php
// wp-content/mu-plugins/wp2-download/src/Licensing/ConnectionInterface.php
namespace WP2\Download\Licensing;

defined( 'ABSPATH' ) || exit();

/**
 * Interface for licensing connection adapters.
 *
 * Consider expanding with capability interfaces for richer licensing operations:
 * - ValidatesKeys: validate_license_key( string $license_key ): array
 * - ManagesInstalls: activate_install( string $license_id, string $fingerprint ): array
 * - DeactivatesInstalls: deactivate_install( string $license_id, string $fingerprint ): array
 * - RetrievesLicenses: get_license_by_key( string $license_key ): array
 *
 * Or, expand this base interface if you want all adapters to implement all methods.
 *
 * TODO: Decide and implement capability interfaces or expand base interface.
 */
interface ConnectionInterface {
	/**
	 * Connect to the licensing service.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function connect(): bool;
}