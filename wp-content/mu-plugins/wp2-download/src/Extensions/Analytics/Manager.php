<?php
namespace WP2\Download\Extensions\Analytics;

/**
 * @component_id extensions_analytics_manager
 * @namespace extensions.analytics
 * @type Manager
 * @note "Manages analytics extensions and execution."
 */
class Manager {
	protected $extensions = array();

	public function __construct() {
		if ( function_exists( 'apply_filters' ) ) {
			$this->extensions = apply_filters( 'wp2_register_analytics_extensions', array() );
		}
	}

	public function run( $context ) {
		foreach ( $this->extensions as $name => $class ) {
			if ( class_exists( $class ) ) {
				$instance = new $class();
				if ( method_exists( $instance, 'run' ) ) {
					$instance->run( $context );
				}
			}
		}
	}
}
