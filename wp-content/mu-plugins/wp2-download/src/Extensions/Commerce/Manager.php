<?php
namespace WP2\Download\Extensions\Commerce;

/**
 * @component_id extensions_commerce_manager
 * @namespace extensions.commerce
 * @type Manager
 * @note "Manages commerce extensions and purchase URLs."
 */
class Manager {
	protected $extensions = [];

	public function __construct() {
		$this->extensions = apply_filters( 'wp2_register_commerce_extensions', [] );
	}

	public function get_purchase_url( $context ) {
		$urls = [];
		foreach ( $this->extensions as $name => $class ) {
			if ( class_exists( $class ) ) {
				$instance = new $class();
				if ( method_exists( $instance, 'get_purchase_url' ) ) {
					$urls[ $name ] = $instance->get_purchase_url( $context );
				}
			}
		}
		return $urls;
	}
}
