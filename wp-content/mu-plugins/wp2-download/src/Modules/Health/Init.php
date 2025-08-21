<?php

/**
 * Summary of namespace WP2\Download\Modules\Health
 */

namespace WP2\Download\Modules\Health;

use WP2\Download\Modules\Health\Checks\CloudflareR2\ArtifactCheck;
use WP2\Download\Modules\Health\Checks\Github\RepoCheck;
use WP2\Download\Modules\Health\Checks\Github\TagCheck;
use WP2\Download\Services\Locator;

/**
 * Health Initialization
 *
 * @component_id health_init
 * @namespace health
 * @type Bootstrap
 * @note "Initializes health system and registers checks."
 */
class Init {

	/**
	 * Initialize health system and register checks.
	 */
	public static function init() {
		$runner = Locator::get_health_runner();
		$runner->register_check( new RepoCheck() );
		$runner->register_check( new TagCheck() );
		$runner->register_check( new ArtifactCheck() );

		// Register Scheduler hooks for Action Scheduler integration.
		$scheduler = new \WP2\Download\Modules\Health\Scheduler();
		$scheduler->register_hooks();

		\WP2\Download\Utils\Logger::log( 'Health Init: Registered all health checks, scheduler, and hooks.', 'DEBUG' );
	}
}
