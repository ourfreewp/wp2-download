<?php
/**
 * WP2 Architecture Mermaid Diagram Generator.
 *
 * This utility generates Mermaid.js class diagram definitions from a structured array
 * of architectural components, enabling automated visualization of software design.
 *
 * @package WP2\Download\Archi\Viz\Mermaid
 */

namespace WP2\Download\Modules\Archi\Viz\Mermaid;

defined( 'ABSPATH' ) || exit;

/**
 * Generates Mermaid diagrams from architectural components.
 *
 * This final class provides a static interface to transform a PHP array describing
 * software components into a valid Mermaid.js class diagram string.
 * It supports namespaces, classes, members (attributes/methods), relationships,
 * notes, styling, and other advanced Mermaid features.
 *
 * @component_id mermaid_generator
 * @namespace archi.viz.mermaid
 * @type Utility
 * @note "Generates Mermaid class diagrams from component arrays."
 * @facet {"name": "from_components", "visibility": "public", "returnType": "string"}
 * @facet {"name": "auto_generate_components", "visibility": "public", "returnType": "array"}
 * @facet {"name": "build_header", "visibility": "private", "returnType": "array"}
 * @facet {"name": "build_classes", "visibility": "private", "returnType": "array"}
 * @facet {"name": "build_class_block", "visibility": "private", "returnType": "array"}
 * @facet {"name": "build_facet_line", "visibility": "private", "returnType": "string"}
 * @facet {"name": "build_relations", "visibility": "private", "returnType": "array"}
 * @facet {"name": "build_notes_and_interactions", "visibility": "private", "returnType": "array"}
 * @facet {"name": "get_placeholder_diagram", "visibility": "private", "returnType": "string"}
 * @facet {"name": "validate_component", "visibility": "private", "returnType": "bool"}
 * @facet {"name": "escape_id", "visibility": "private", "returnType": "string"}
 * @facet {"name": "escape_text", "visibility": "private", "returnType": "string"}
 * @facet {"name": "format_generic_type", "visibility": "private", "returnType": "string"}
 * @facet {"name": "get_visibility_symbol", "visibility": "private", "returnType": "string"}
 * @facet {"name": "get_classifier_symbol", "visibility": "private", "returnType": "string"}
 * @facet {"name": "get_relation_arrow", "visibility": "private", "returnType": "string"}
 * @relation {"to": "registry", "type": "dependency", "label": "receives component data"}
 */
final class Generator {
	/**
	 * Scans PHP classes in a directory and generates component arrays using Reflection.
	 *
	 * @note This is a conceptual stub for a future enhancement.
	 * @param string $dir Directory to scan for PHP files.
	 * @return array An empty array, as this is a placeholder.
	 */
	public static function auto_generate_components( string $dir ): array {
		// TODO: Implement recursive directory scan and ReflectionClass analysis.
		return array();
	}

	/**
	 * Generates a Mermaid class diagram from an array of architectural components.
	 *
	 * @param array<string, array<string, mixed>> $components The associative array of component definitions.
	 * @param array<string, mixed>                $opts       Configuration options for generation (e.g., 'styles', 'direction').
	 * @return string The complete Mermaid class diagram definition.
	 */
	public static function from_components( array $components, array $opts = array() ): string {
		if ( empty( $components ) ) {
			return self::get_placeholder_diagram();
		}

		// Filter out any invalid component definitions before processing.
		$valid_components = array_filter( $components, array( self::class, 'validate_component' ) );

		if ( empty( $valid_components ) ) {
			return self::get_placeholder_diagram();
		}

		$header_lines   = self::build_header( $opts );
		$class_lines    = self::build_classes( $valid_components, $opts );
		$relation_lines = self::build_relations( $valid_components );
		$note_lines     = self::build_notes_and_interactions( $valid_components );

		$all_lines = array_merge( $header_lines, $class_lines, $relation_lines, $note_lines );

		return implode( "\n", $all_lines ) . "\n";
	}

	// --- Private Diagram Builders ---

	/**
	 * Builds the header part of the Mermaid diagram, including styles and notes.
	 *
	 * @param array<string, mixed> $opts Generation options.
	 * @return string[] Lines for the diagram header.
	 */
	private static function build_header( array $opts ): array {
		$lines = array( 'classDiagram', 'direction LR' );

		// Add top-level notes and comments.
		if ( ! empty( $opts['notes'] ) && is_array( $opts['notes'] ) ) {
			foreach ( $opts['notes'] as $note ) {
				$lines[] = 'note "' . self::escape_text( $note ) . '"';
			}
		}
		if ( ! empty( $opts['comments'] ) && is_array( $opts['comments'] ) ) {
			foreach ( $opts['comments'] as $comment ) {
				$lines[] = '%% ' . $comment;
			}
		}

		// Define default styles, allowing user options to override.
		$default_class_defs = array(
			'service'    => 'fill:#add8e6,stroke:#333',
			'manager'    => 'fill:#90ee90,stroke:#333',
			'gateway'    => 'fill:#b39ddb,stroke:#311b92',
			'page'       => 'fill:#f8d7da,stroke:#721c24',
			'utility'    => 'fill:#e2e3e5,stroke:#383d41',
			'adapter'    => 'fill:#d4edda,stroke:#155724',
			'interface'  => 'fill:#fff3cd,stroke:#856404',
			'controller' => 'fill:#d1ecf1,stroke:#0c5460',
			'bootstrap'  => 'fill:#cce5ff,stroke:#004085',
			'check'      => 'fill:#f5c6cb,stroke:#721c24',
		);
		$class_defs         = array_merge( $default_class_defs, $opts['class_defs'] ?? array() );

		foreach ( $class_defs as $class_name => $styles ) {
			$style_str = is_array( $styles ) ? implode( ',', $styles ) : $styles;
			$lines[]   = 'classDef ' . self::escape_id( $class_name ) . ' ' . $style_str;
		}

		return $lines;
	}

	/**
	 * Builds the class and namespace definition blocks.
	 *
	 * @param array<string, array<string, mixed>> $components Validated component definitions.
	 * @param array<string, mixed>                $opts       Generation options.
	 * @return string[] Lines for the class definitions.
	 */
	private static function build_classes( array $components, array $opts ): array {
		$lines            = array();
		$use_colon_syntax = ! empty( $opts['colon_syntax'] );

		// Group components by namespace to create namespace blocks.
		$namespaced_components = array();
		foreach ( $components as $id => $c ) {
			$namespace                                  = $c['namespace'] ?? 'default';
			$namespaced_components[ $namespace ][ $id ] = $c;
		}

		foreach ( $namespaced_components as $namespace => $ns_components ) {
			$is_default_ns = 'default' === $namespace;

			if ( ! $is_default_ns ) {
				$lines[] = 'namespace ' . self::escape_id( $namespace ) . ' {';
			}

			$indent = ! $is_default_ns ? '    ' : '';

			foreach ( $ns_components as $id => $c ) {
				$lines = array_merge( $lines, self::build_class_block( $id, $c, $use_colon_syntax, $indent ) );
			}

			if ( ! $is_default_ns ) {
				$lines[] = '}';
			}
		}
		return $lines;
	}

	/**
	 * Builds the lines for a single class definition block.
	 *
	 * @param string               $id               The component's unique ID.
	 * @param array<string, mixed> $component        The component's data.
	 * @param bool                 $use_colon_syntax Whether to use colon syntax for members.
	 * @param string               $indent           Indentation string (for namespaces).
	 * @return string[] The lines defining the class in Mermaid syntax.
	 */
	private static function build_class_block( string $id, array $component, bool $use_colon_syntax, string $indent = '' ): array {
		$class_id    = self::escape_id( $id );
		$title       = self::escape_text( $component['title'] ?? $id );
		$annotation  = self::escape_text( $component['type'] ?? 'Component' );
		$style_class = ! empty( $component['css_class'] ) ? ':::' . self::escape_id( $component['css_class'] ) : '';
		$generic     = ! empty( $component['generic'] ) ? self::format_generic_type( $component['generic'] ) : '';

		$lines   = array();
		$lines[] = $indent . 'class ' . $class_id . $generic . '["' . $title . '"]' . $style_class . ' {';

		// Add stereotype/annotation
		$lines[] = $indent . '    <<' . $annotation . '>>';

		if ( ! empty( $component['facets'] ) && is_array( $component['facets'] ) ) {
			foreach ( $component['facets'] as $facet ) {
				// Only render if name is present
				if ( ! empty( $facet['name'] ) ) {
					$lines[] = $indent . '    ' . self::build_facet_line( $facet, $use_colon_syntax );
				}
			}
		}

		$lines[] = $indent . '}';
		return $lines;
	}

	/**
	 * Builds a single line for a class member (facet).
	 *
	 * @param array<string, mixed> $facet            The facet data.
	 * @param bool                 $use_colon_syntax Whether to use colon syntax.
	 * @return string The formatted member line.
	 */
	private static function build_facet_line( array $facet, bool $use_colon_syntax ): string {
		$visibility = self::get_visibility_symbol( $facet['visibility'] ?? 'public' );
		$name       = self::escape_text( $facet['name'] ?? '' );
		$classifier = self::get_classifier_symbol( $facet['classifier'] ?? '' );

		$base_type   = isset( $facet['type'] ) ? self::escape_text( $facet['type'] ) : '';
		$return_type = ! empty( $facet['returnType'] ) ? self::escape_text( $facet['returnType'] ) : '';
		$generic     = ! empty( $facet['generic'] ) ? self::format_generic_type( $facet['generic'] ) : '';

		// Handle methods/functions.
		if ( isset( $facet['parameters'] ) && is_array( $facet['parameters'] ) ) {
			$params           = array_map( fn( $p ) => self::escape_text( $p['name'] ?? '' ), $facet['parameters'] );
			$params_str       = implode( ', ', $params );
			$method_signature = $name . '(' . $params_str . ')' . $generic;

			if ( $use_colon_syntax && $return_type ) {
				return trim( $visibility . ' ' . $method_signature . ' : ' . $return_type ) . $classifier;
			}
			return trim( $visibility . ' ' . $return_type . ' ' . $method_signature ) . $classifier;
		}

		// Handle attributes/properties.
		$type_str = $base_type . $generic;
		if ( $use_colon_syntax && $type_str ) {
			return trim( $visibility . $classifier . ' ' . $name . ' : ' . $type_str );
		}
		return trim( $visibility . $classifier . ' ' . $type_str . ' ' . $name );
	}

	/**
	 * Builds the relationship lines between components.
	 *
	 * @param array<string, array<string, mixed>> $components Validated component definitions.
	 * @return string[] Lines for the relationships.
	 */
	private static function build_relations( array $components ): array {
		$lines = array();
		foreach ( $components as $id => $c ) {
			if ( empty( $c['relations'] ) || ! is_array( $c['relations'] ) ) {
				continue;
			}

			foreach ( $c['relations'] as $r ) {
				if ( empty( $r['to'] ) || ! isset( $components[ $r['to'] ] ) ) {
					continue;
				}

				$from_id = self::escape_id( $id );
				$to_id   = self::escape_id( $r['to'] );
				$arrow   = self::get_relation_arrow( $r['type'] ?? 'association' );
				$label   = ! empty( $r['label'] ) ? ' : ' . self::escape_text( $r['label'] ) : '';

				$from_card = ! empty( $r['fromCard'] ) ? '"' . self::escape_text( $r['fromCard'] ) . '" ' : '';
				$to_card   = ! empty( $r['toCard'] ) ? ' "' . self::escape_text( $r['toCard'] ) . '"' : '';

				$lines[] = $from_id . ' ' . $from_card . $arrow . $to_card . ' ' . $to_id . $label;
			}
		}
		return $lines;
	}

	/**
	 * Builds notes, links, and callbacks for components.
	 *
	 * @param array<string, array<string, mixed>> $components Validated component definitions.
	 * @return string[] Lines for notes and interactions.
	 */
	private static function build_notes_and_interactions( array $components ): array {
		$lines = array();
		foreach ( $components as $id => $c ) {
			$escaped_id = self::escape_id( $id );

			if ( ! empty( $c['note'] ) ) {
				$lines[] = 'note for ' . $escaped_id . ' "' . self::escape_text( $c['note'] ) . '"';
			}
			if ( ! empty( $c['url'] ) ) {
				$tooltip = ! empty( $c['link_label'] ) ? ' "' . self::escape_text( $c['link_label'] ) . '"' : '';
				$lines[] = 'click ' . $escaped_id . ' href "' . self::escape_text( $c['url'] ) . '"' . $tooltip;
			}
			if ( ! empty( $c['callback'] ) ) {
				$tooltip = ! empty( $c['callback_label'] ) ? ' "' . self::escape_text( $c['callback_label'] ) . '"' : '';
				$lines[] = 'click ' . $escaped_id . ' call ' . self::escape_text( $c['callback'] ) . '()' . $tooltip;
			}
		}
		return $lines;
	}

	// --- Private Helpers ---

	/**
	 * Returns a dynamically generated placeholder class diagram for demonstration.
	 *
	 * @return string A complete and valid Mermaid class diagram definition.
	 */
	private static function get_placeholder_diagram(): string {
		$sample_components = array(
			'api_gateway'     => array(
				'component_id' => 'api_gateway',
				'namespace'    => 'Core.Services',
				'type'         => 'Service',
				'title'        => 'API Gateway',
				'css_class'    => 'service',
				'facets'       => array(
					array(
						'name'       => 'handleRequest',
						'visibility' => 'public',
						'parameters' => array( array( 'name' => 'req' ) ),
						'returnType' => 'Response',
						'classifier' => 'abstract',
					),
				),
				'relations'    => array(
					array(
						'to'       => 'license_manager',
						'type'     => 'dependency',
						'label'    => 'validates via',
						'fromCard' => '1',
						'toCard'   => '1',
					),
				),
				'note'         => 'Handles all incoming API requests.',
			),
			'license_manager' => array(
				'component_id' => 'license_manager',
				'namespace'    => 'Business.Logic',
				'type'         => 'Manager',
				'title'        => 'License Manager',
				'css_class'    => 'manager',
				'facets'       => array(
					array(
						'name'       => 'validate',
						'visibility' => 'public',
						'parameters' => array( array( 'name' => 'key' ) ),
						'returnType' => 'bool',
					),
				),
			),
		);
		return self::from_components( $sample_components, array( 'comments' => array( 'This is a placeholder diagram.' ) ) );
	}

	/**
	 * Validates a single component definition.
	 *
	 * @param mixed $component The component data to validate.
	 * @return bool True if valid, false otherwise.
	 */
	private static function validate_component( $component ): bool {
		if ( ! is_array( $component ) || empty( $component['component_id'] ) ) {
			return false;
		}
		if ( ! empty( $component['relations'] ) && is_array( $component['relations'] ) ) {
			foreach ( $component['relations'] as $relation ) {
				if ( ! is_array( $relation ) || empty( $relation['to'] ) ) {
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * Sanitizes a string to be used as a Mermaid ID.
	 *
	 * @param string $id The input string.
	 * @return string The sanitized ID.
	 */
	private static function escape_id( string $id ): string {
		return preg_replace( '/[^a-zA-Z0-9_.-]/', '_', $id );
	}

	/**
	 * Escapes text for safe inclusion in Mermaid labels, notes, and definitions.
	 *
	 * @param string $text The input text.
	 * @return string The escaped text.
	 */
	private static function escape_text( string $text ): string {
		// Escape backslashes first.
		$text = str_replace( '\\', '\\\\', $text );
		// Replace newlines with the \n literal for Mermaid.
		$text = str_replace( array( "\r\n", "\r", "\n" ), '\\n', $text );
		// Escape double quotes with a backslash, which is the correct method for Mermaid strings.
		$text = str_replace( '"', '\\"', $text );
		return $text;
	}

	/**
	 * Formats a generic type string into Mermaid's tilde syntax.
	 *
	 * @param string $type The generic type (e.g., "T", "string").
	 * @return string The formatted type, like "~T~".
	 */
	private static function format_generic_type( string $type ): string {
		return '~' . self::escape_text( $type ) . '~';
	}

	/**
	 * Maps a visibility keyword to its Mermaid symbol.
	 *
	 * @param string $visibility The visibility keyword (e.g., 'public', 'private').
	 * @return string The corresponding Mermaid symbol.
	 */
	private static function get_visibility_symbol( string $visibility ): string {
		return match ( strtolower( $visibility ) ) {
			'private'             => '-', 'protected' => '#', 'package', 'internal' => '~',
			default               => '+',
		};
	}

	/**
	 * Maps a member classifier to its Mermaid symbol.
	 *
	 * @param string $classifier The classifier keyword (e.g., 'static', 'abstract').
	 * @return string The corresponding Mermaid symbol.
	 */
	private static function get_classifier_symbol( string $classifier ): string {
		return match ( strtolower( $classifier ) ) {
			'static'   => '$', 'abstract' => '*',
			default    => '',
		};
	}

	/**
	 * Maps a relationship type to its Mermaid arrow syntax.
	 *
	 * @param string $type The relationship type keyword.
	 * @return string The corresponding Mermaid arrow string.
	 */
	private static function get_relation_arrow( string $type ): string {
		return match ( strtolower( $type ) ) {
			'inheritance', 'extends'                 => '<|--',
			'composition'                            => '*--',
			'aggregation'                            => 'o--',
			'association'                            => '-->',
			'link', 'solid'                          => '--',
			'dependency', 'depends', 'uses', 'emits' => '..>',
			'realization', 'implements'              => '..|>',
			'dashed'                                 => '..',
			'lollipop'                               => '--()',
			default                                  => '-->',
		};
	}
}
