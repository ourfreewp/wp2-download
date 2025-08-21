<?php

/**
 * REST API initialization.
 *
 * @package WP2_Download
 */

namespace WP2\Download\REST;

use WP2\Download\REST\Manifests\Controller as ManifestsController;
use WP2\Download\REST\Packages\Controller as PackagesController;
use WP2\Download\REST\Releases\Controller as ReleasesController;
use WP2\Download\REST\Systems\Client\Controller as ClientController;
use WP2\Download\REST\Systems\Controller as SystemController;
use WP2\Download\REST\Systems\Health\Controller as HealthController;

/**
 * REST API initialization.
 *
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
