<?php
namespace WP2\Download\Archi;

defined( 'ABSPATH' ) || exit;

/**
 * Helper to register one annotation via a simple function call.
 *
 * @param array $component The component annotation payload.
 */
function register_annotation( array $component ): void {
	add_filter(
		'wp2_archi_annotations',
		static function (array $components) use ($component): array {
			$components[] = $component;
			return $components;
		}
	);
}
