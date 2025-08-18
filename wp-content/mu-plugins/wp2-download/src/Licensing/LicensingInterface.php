<?php
namespace WP2\Download\Licensing;

interface LicensingInterface {
	/**
	 * Interface LicensingInterface
	 * Validates a license key for a specific package.
	 *
	 * @package WP2\Download\Licensing
	 */
	public function validate( string $license_key, string $package_slug ): bool;
}
