<?php
namespace WP2\Download\Modules\Archi;

use WP2\Download\Archi\Admin\Page as AdminPage;

defined( 'ABSPATH' ) || exit;

/**
 * @component_id archi_init
 * @namespace archi
 * @type Bootstrap
 * @note "Main bootstrap for the Archi SDK."
 * @facet {"name": "boot", "visibility": "public", "returnType": "void"}
 * @facet {"name": "initialize", "visibility": "public", "returnType": "void"}
 * @relation {"to": "registry", "type": "dependency", "label": "boots registry"}
 * @relation {"to": "caching", "type": "dependency", "label": "boots caching"}
 */
final class Init {

	public function boot(): void {
		// Hook the initialization function into plugins_loaded.
		add_action( 'plugins_loaded', array( $this, 'initialize' ) );
	}

	/**
	 * Initialize the SDK components.
	 */
	public function initialize(): void {
		// Core components
		Registry::instance()->boot();
		( new Caching() )->boot();

		// Admin, REST, and CLI components
		( new AdminPage() )->boot();
		( new RestController() )->boot();

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			\WP_CLI::add_command( 'wp2 archi', CliCommands::class );
		}
	}
}
