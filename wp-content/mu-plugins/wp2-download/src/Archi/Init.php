<?php
namespace WP2\Download\Archi;

use WP2\Download\Archi\Admin\Page as AdminPage;
use WP2\Download\Archi\CLI\Commands as CLICommands;
use WP2\Download\Archi\REST\Controller as RESTController;

defined( 'ABSPATH' ) || exit;

/**
 * Main bootstrap for the Archi SDK.
 */
final class Init {

	public function boot(): void {
		// Hook the initialization function into plugins_loaded.
		add_action( 'plugins_loaded', [ $this, 'initialize' ] );
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
			\WP_CLI::add_command( 'wp2 archi', CliCommands::class);
		}
	}
}
