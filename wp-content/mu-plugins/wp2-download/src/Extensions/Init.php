<?php

/**
 * Summary of namespace WP2\Download\Extensions
 */

namespace WP2\Download\Extensions;

use WP2\Download\Extensions\Analytics\Manager as AnalyticsManager;
use WP2\Download\Extensions\Commerce\Manager as CommerceManager;
use WP2\Download\Extensions\Development\Manager as DevelopmentManager;
use WP2\Download\Extensions\Identity\Manager as IdentityManager;
use WP2\Download\Extensions\Licensing\Manager as LicensingManager;
use WP2\Download\Extensions\Storage\Manager as StorageManager;

/**
 * Initializes all extension managers.
 *
 * @component_id extensions_init
 * @namespace extensions
 * @type Bootstrap
 * @note "Initializes all extension managers."
 */
class Init {

	public $analytics;
	public $commerce;
	public $development;
	public $identity;
	public $licensing;
	public $storage;

	public function init() {
		$this->analytics = new AnalyticsManager();
		$this->commerce = new CommerceManager();
		$this->development = new DevelopmentManager();
		$this->identity = new IdentityManager();
		$this->licensing = new LicensingManager();
		$this->storage = new StorageManager();
	}
}
