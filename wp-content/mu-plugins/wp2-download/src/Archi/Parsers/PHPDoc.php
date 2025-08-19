<?php
namespace WP2\Download\Archi\Parsers;

use WP2\Download\Archi\Helpers;

class PHPDoc {
	/**
	 * Scan PHP files in the given directory for PHPDoc component annotations and register them.
	 * @param string $directory
	 */
	public static function registerComponentsFromPHPDoc( string $directory ): void {
		$files = self::getPhpFiles( $directory );
		foreach ( $files as $file ) {
			$components = self::parseFileForComponents( $file );
			foreach ( $components as $component ) {
				Helpers::register_annotation( $component );
			}
		}
	}

	/**
	 * Get all PHP files recursively in a directory.
	 */
	private static function getPhpFiles( string $directory ): array {
		$rii = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $directory ) );
		$files = [];
		foreach ( $rii as $file ) {
			if ( $file->isDir() )
				continue;
			if ( strtolower( $file->getExtension() ) === 'php' ) {
				$files[] = $file->getPathname();
			}
		}
		return $files;
	}

	/**
	 * Parse a PHP file for PHPDoc blocks containing component annotations.
	 * Returns an array of component arrays.
	 */
	private static function parseFileForComponents( string $file ): array {
		$contents = file_get_contents( $file );
		$matches = [];
		// Match PHPDoc blocks with @component_id
		preg_match_all( '/\/\*\*([\s\S]*?)\*\//', $contents, $matches );
		$components = [];
		foreach ( $matches[1] as $block ) {
			if ( strpos( $block, '@component_id' ) !== false ) {
				$component = self::parseComponentBlock( $block );
				if ( $component ) {
					error_log( 'DEBUG: PHPDoc parsed component: ' . print_r( $component, true ) );
					$components[] = $component;
				}
			}
		}
		error_log( 'DEBUG: PHPDoc parsed components array: ' . print_r( $components, true ) );
		return $components;
	}

	/**
	 * Parse a PHPDoc block and extract component metadata.
	 * Returns a component array or null.
	 */
	private static function parseComponentBlock( string $block ): ?array {
		// Preprocess block: remove leading '*' and whitespace from each line
		$lines = explode( "\n", $block );
		$clean_block = '';
		foreach ( $lines as $line ) {
			$clean_block .= preg_replace( '/^\s*\*?\s?/', '', $line ) . "\n";
		}

		$component = [];
		// Simple regex for each field
		foreach ( [ 
			'component_id', 'namespace', 'type', 'title', 'note', 'url'
		] as $field ) {
			if ( preg_match( '/@' . $field . '\s+([^\n]*)/', $clean_block, $m ) ) {
				$component[ $field ] = trim( $m[1] );
			}
		}
		// Facets and relations: try to extract as JSON, fallback to empty array
		foreach ( [ 'facets', 'relations' ] as $field ) {
			if ( preg_match( '/@' . $field . '\s+(\[.*?\])/ms', $clean_block, $m ) ) {
				$component[ $field ] = json_decode( $m[1], true ) ?: [];
			} else {
				$component[ $field ] = [];
			}
		}
		return isset( $component['component_id'] ) ? $component : null;
	}
}
