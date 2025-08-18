<?php
/**
 * WP2 Download – Core bootstrap for feature initializers.
 *
 * @package WP2\Download
 */
namespace WP2\Download;

defined( 'ABSPATH' ) || exit;

// 1. Import all necessary Init classes
use WP2\Download\Services\Init as ServicesInit;
use WP2\Download\Content\Init as ContentInit;
use WP2\Download\Gateway\Init as GatewayInit;
use WP2\Download\REST\Init as RESTInit;
use WP2\Download\Health\Init as HealthInit;
use WP2\Download\Admin\Init as AdminInit;

/**
 * Register all feature initializers in load order.
 *
 * @return void
 */
function wp2_download_bootstrap(): void {
	// 1) Services – service locator, config, logger, shared singletons
	ServicesInit::init();

	// 2) Content  – CPTs/taxonomies
	ContentInit::init();

	// 3) Gateway  – rewrites + download dispatcher
	GatewayInit::init();

	// 4) REST     – controllers + routes
	RESTInit::init();

	// 5) Health   – runner + checks + listeners
	HealthInit::init();

	// 6) Admin    – menus, assets, screens
	AdminInit::init();
}

wp2_download_bootstrap();