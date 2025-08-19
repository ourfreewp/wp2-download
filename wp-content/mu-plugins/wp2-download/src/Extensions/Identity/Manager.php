<?php
namespace WP2\Download\Extensions\Identity;

/**
 * @component_id extensions_identity_manager
 * @namespace extensions.identity
 * @type Manager
 * @note "Manages identity extensions and authentication."
 */
class Manager {
	protected $extensions = [];

	public function __construct() {
		$this->extensions = apply_filters( 'wp2_register_identity_extensions', [] );
	}

	public function authenticate( $context ) {
		foreach ( $this->extensions as $name => $class ) {
			if ( class_exists( $class ) ) {
				$instance = new $class();
				if ( method_exists( $instance, 'authenticate' ) ) {
					if ( ! $instance->authenticate( $context ) ) {
						return false;
					}
				}
			}
		}
		return true;
	}
}
