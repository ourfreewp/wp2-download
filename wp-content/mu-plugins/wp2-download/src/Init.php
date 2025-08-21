<?php

/**
 * WP2 Download – Core bootstrap for feature initializers.
 *
 * @package WP2\Download
 */

namespace WP2\Download;

/**
 * Register all feature initializers in load order.
 *
 * @return void
 */
function wp2_download_bootstrap(): void {
	\WP2\Download\Core\Init::init();
	\WP2\Download\Extensions\Init::init();	
	\WP2\Download\Helpers\Init::init();	
	\WP2\Download\Modules\Init::init();
	\WP2\Download\REST\Init::init();
	\WP2\Download\Services\Init::init();
	\WP2\Download\Utils\Init::init();
	\WP2\Download\Views\Init::init();
}