<?php
namespace WP2\Download\Extensions\Storage;

/**
 * @component_id extensions_storage_manager
 * @namespace extensions.storage
 * @type Manager
 * @note "Manages storage extensions and operations."
 */
class Manager {
	protected $extensions = array();

	public function __construct() {
		$this->extensions = apply_filters( 'wp2_register_storage_extensions', array() );
	}

	public function store( $context ) {
		foreach ( $this->extensions as $name => $class ) {
			if ( class_exists( $class ) ) {
				$instance = new $class();
				if ( method_exists( $instance, 'store' ) ) {
					$instance->store( $context );
				}
			}
		}
	}
}
