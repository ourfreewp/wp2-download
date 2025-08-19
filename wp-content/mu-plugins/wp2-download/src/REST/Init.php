<?php
namespace WP2\Download\REST;

use WP2\Download\REST\Releases\Controller as ReleasesController;
use WP2\Download\REST\Manifests\Controller as ManifestsController;
use WP2\Download\REST\Packages\Controller as PackagesController;
use WP2\Download\REST\Client\Controller as ClientController;
use WP2\Download\REST\System\Controller as SystemController;
use WP2\Download\REST\System\Health\Controller as HealthController;

/**
 * @component_id rest_init
 * @namespace rest
 * @type Bootstrap
 * @note "Initializes all REST API controllers."
 */
class Init {
	public static function init() {
		( new ReleasesController() )->register_routes();
		( new ManifestsController() )->register_routes();
		( new PackagesController() )->register_routes();
		( new ClientController() )->register_routes();
		( new SystemController() )->register_routes();
		( new HealthController() )->register_routes();
	}
}