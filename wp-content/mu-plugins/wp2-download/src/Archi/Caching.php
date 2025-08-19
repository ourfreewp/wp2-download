<?php
namespace WP2\Download\Archi;

defined( 'ABSPATH' ) || exit;

final class Caching {

	public const TRANSIENT_KEY = 'wp2_archi_graph_cache';

	public function boot(): void {
		add_action( 'activated_plugin', [ $this, 'flush_cache' ] );
		add_action( 'deactivated_plugin', [ $this, 'flush_cache' ] );
	}

	public function flush_cache(): void {
		delete_transient( self::TRANSIENT_KEY );
	}

	public function rebuild_cache(): void {
		$this->flush_cache();
		Registry::instance()->load_components( true );
	}
}
