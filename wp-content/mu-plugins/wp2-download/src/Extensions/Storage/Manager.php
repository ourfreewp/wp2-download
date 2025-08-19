<?php
namespace WP2\Download\Extensions\Storage;

class Manager {
	protected $extensions = [];

	public function __construct() {
		$this->extensions = apply_filters( 'wp2_register_storage_extensions', [] );
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
