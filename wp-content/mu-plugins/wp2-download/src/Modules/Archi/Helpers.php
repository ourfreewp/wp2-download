<?php

/**
 * Summary of namespace WP2\Download\Modules\Archi
 */

namespace WP2\Download\Modules\Archi;

/**
 * Helper utilities for WP2 Archi.
 *
 * @component_id helpers
 * @namespace archi
 * @type Utility
 * @note "Helper utilities for WP2 Archi."
 * @facet {"name": "register_annotation", "visibility": "public", "returnType": "void"}
 * @facet {"name": "register_annotations", "visibility": "public", "returnType": "void"}
 * @relation {"to": "registry", "type": "dependency", "label": "registers components"}
 */
class Helpers {

	public static function register_annotation( array $component ): void {
		add_filter(
			'wp2_archi_annotations',
			static function ( array $components ) use ( $component ): array {
				$components[] = $component;
				return $components;
			}
		);
	}

	public static function register_annotations( array $components ): void {
		foreach ( $components as $component ) {
			self::register_annotation( $component );
		}
	}
}
