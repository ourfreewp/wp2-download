<?php

namespace WP2\Download\Client\MustUse;

/**
 * MU plugin wrapper (cron-based self-updates).
 */
class Updater {
	/** @var Updater */
	private $updater;
	/**
	 * @param string $plugin_file __FILE__ of the MU plugin.
	 */
	public function __construct( $plugin_file ) {
		$this->updater = new Updater( $plugin_file );
	}
	public function init() {
		$this->updater->init(); // for shared hooks like auth headers, reporting
		add_action( 'wp2_mu_plugin_update_check', [ $this->updater, 'check_for_updates' ] );
		if ( ! wp_next_scheduled( 'wp2_mu_plugin_update_check' ) ) {
			wp_schedule_event( time() + rand( 300, 1800 ), 'twicedaily', 'wp2_mu_plugin_update_check' );
		}
	}
}
