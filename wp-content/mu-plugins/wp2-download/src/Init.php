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
use WP2\Download\Extensions\Init as ExtensionInit;

use WP2\Download\Archi\Init as ArchiInit;

use WP2\Download\Archi\Helpers as ArchiHelper;


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

	// 7) Extensions – universal extension framework
	$extension_init = new ExtensionInit();
	$extension_init->init();


	// Last) Archi — architecture visualization
	$archi_init = new ArchiInit();
	$archi_init->boot();

	// Register components from PHPDoc annotations in the codebase
	\WP2\Download\Archi\Parsers\PHPDoc::registerComponentsFromPHPDoc( __DIR__ );
}

wp2_download_bootstrap();