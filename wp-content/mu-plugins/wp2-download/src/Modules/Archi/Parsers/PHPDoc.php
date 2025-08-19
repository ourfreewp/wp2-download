<?php
namespace WP2\Download\Modules\Archi\Parsers;

use WP2\Download\Archi\Helpers;

/**
 * @component_id archi_phpdoc
 * @namespace archi.parsers
 * @type Utility
 * @note "Parses PHPDoc blocks for SDK annotations."
 * @facet {"name": "registerComponentsFromPHPDoc", "visibility": "public", "returnType": "void"}
 * @facet {"name": "getPhpFiles", "visibility": "private", "returnType": "array"}
 * @facet {"name": "parseFileForComponents", "visibility": "private", "returnType": "array"}
 * @facet {"name": "parseComponentBlock", "visibility": "private", "returnType": "?array"}
 * @relation {"to": "helpers", "type": "dependency", "label": "registers parsed components"}
 */
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
	 * Parse a PHPDoc block and extract component metadata, including @facet, @relation, and @note lines.
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

		// Facets: support both array and individual @facet lines
		$component['facets'] = [];
		// Array-style facets
		if ( preg_match( '/@facets\s+(\[.*?\])/ms', $clean_block, $m ) ) {
			$facets = json_decode( $m[1], true );
			if ( is_array( $facets ) ) {
				$component['facets'] = array_merge( $component['facets'], $facets );
			}
		}
		// Individual facet lines
		if ( preg_match_all( '/@facet\s+({.*?})/ms', $clean_block, $matches ) ) {
			foreach ( $matches[1] as $json ) {
				$facet = json_decode( $json, true );
				if ( is_array( $facet ) ) {
					$component['facets'][] = $facet;
				}
			}
		}

		// Relations: support both array and individual @relation lines
		$component['relations'] = [];
		// Array-style relations
		if ( preg_match( '/@relations\s+(\[.*?\])/ms', $clean_block, $m ) ) {
			$relations = json_decode( $m[1], true );
			if ( is_array( $relations ) ) {
				$component['relations'] = array_merge( $component['relations'], $relations );
			}
		}
		// Individual relation lines
		if ( preg_match_all( '/@relation\s+({.*?})/ms', $clean_block, $matches ) ) {
			foreach ( $matches[1] as $json ) {
				$relation = json_decode( $json, true );
				if ( is_array( $relation ) ) {
					$component['relations'][] = $relation;
				}
			}
		}

		// Individual notes (component-specific)
		if ( preg_match_all( '/@note\s+"([^"]+)"/', $clean_block, $matches ) ) {
			// If multiple notes, join them with newlines
			$component['note'] = implode( "\n", $matches[1] );
		}

		return isset( $component['component_id'] ) ? $component : null;
	}
}
