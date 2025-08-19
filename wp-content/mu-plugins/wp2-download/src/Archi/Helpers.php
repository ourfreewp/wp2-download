<?php
namespace WP2\Download\Archi;

class Helpers {
	public static function register_annotation( array $component ): void {
		add_filter(
			'wp2_archi_annotations',
			static function (array $components) use ($component): array {
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