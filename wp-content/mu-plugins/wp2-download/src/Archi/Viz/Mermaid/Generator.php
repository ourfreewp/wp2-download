<?php
/**
 * WP2 Architecture Mermaid Diagram Generator.
 *
 * This file contains the final version of the Generator class
 * for creating Mermaid class diagrams from architectural components.
 * It includes all previously discussed fixes and new features.
 *
 * @package WP2\Download\Archi\Viz\Mermaid
 */

namespace WP2\Download\Archi\Viz\Mermaid;

defined( 'ABSPATH' ) || exit;

final class Generator {
	/**
	 * Automatically scan PHP classes and generate component arrays using Reflection.
	 * This is a stub for future enhancement.
	 * @param string $dir Directory to scan.
	 * @return array Generated component arrays.
	 */
	public static function auto_generate_components( string $dir ): array {
		// TODO: Implement recursive directory scan and ReflectionClass analysis.
		// Example:
		// $files = glob($dir . '/*.php');
		// foreach ($files as $file) { ... }
		// Use ReflectionClass to extract class, methods, properties, etc.
		return [];
	}

	/**
	 * Generates a Mermaid class diagram from a set of architectural components.
	 * This version uses a strict, reliable syntax to ensure rendering.
	 *
	 * @param array $components The array of component definitions from the Registry.
	 * @param array $opts       Options for generation (e.g., 'styles').
	 * @return string The complete Mermaid class diagram definition.
	 */
	public static function from_components( array $components, array $opts = [] ): string {
		// Return a placeholder diagram if the components array is empty.
		if ( empty( $components ) ) {
			return self::get_placeholder_diagram();
		}

		// Validate components before processing, removing any that are malformed.
		foreach ( $components as $id => $c ) {
			if ( ! self::validate_component( $c ) ) {
				unset( $components[ $id ] );
			}
		}

		// Initialize the diagram with the header and direction.
		$lines = [ 'classDiagram' ];
		$lines[] = 'direction LR';

		// Add general notes (if present) before any other elements.
		if ( ! empty( $opts['notes'] ) && is_array( $opts['notes'] ) ) {
			foreach ( $opts['notes'] as $note_text ) {
				$lines[] = 'note "' . self::escape_text( $note_text ) . '"';
			}
		}

		// Add general comments.
		if ( ! empty( $opts['comments'] ) && is_array( $opts['comments'] ) ) {
			foreach ( $opts['comments'] as $comment_text ) {
				$lines[] = '%% ' . $comment_text;
			}
		}

		// Add styling classes. This makes the diagram visually more informative.
		if ( ! empty( $opts['class_defs'] ) && is_array( $opts['class_defs'] ) ) {
			foreach ( $opts['class_defs'] as $class_name => $styles ) {
				$style_str = implode( ',', $styles );
				$lines[] = 'classDef ' . self::escape_id( $class_name ) . ' ' . $style_str;
			}
		}

		// Add Mermaid config YAML block if present in options
		if ( ! empty( $opts['config'] ) && is_array( $opts['config'] ) ) {
			$yaml = "---\n";
			foreach ( $opts['config'] as $key => $value ) {
				if ( is_array( $value ) ) {
					$yaml .= $key . ":\n";
					foreach ( $value as $subkey => $subval ) {
						$yaml .= "  " . $subkey . ": " . ( is_bool( $subval ) ? ( $subval ? 'true' : 'false' ) : $subval ) . "\n";
					}
				} else {
					$yaml .= $key . ": " . ( is_bool( $value ) ? ( $value ? 'true' : 'false' ) : $value ) . "\n";
				}
			}
			$yaml .= "---\n";
			array_unshift( $lines, $yaml );
		}

		// Group components by namespace to render them in logical blocks.
		$namespaced_components = [];
		foreach ( $components as $id => $c ) {
			$namespace = $c['namespace'] ?? 'default';
			$namespaced_components[ $namespace ][ $id ] = $c;
		}

		// Generate class definitions within their namespaces using the nested block syntax.
		foreach ( $namespaced_components as $namespace => $ns_components ) {
			$is_namespaced = 'default' !== $namespace;
			$indent = $is_namespaced ? '    ' : '';
			$use_colon_syntax = ! empty( $opts['colon_syntax'] );

			if ( $is_namespaced ) {
				$lines[] = 'namespace ' . self::escape_id( $namespace ) . ' {';
			}

			foreach ( $ns_components as $id => $c ) {
				$class_id = self::escape_id( $id );
				$class_id_bt = '`' . $class_id . '`';
				$title = self::escape_text( $c['title'] ?? $id );
				$annotation = self::escape_text( $c['type'] ?? 'component' );
				$class_style = ! empty( $c['css_class'] ) ? ':::' . self::escape_id( $c['css_class'] ) : '';
				$generic = ! empty( $c['generic'] ) ? '<' . self::escape_text( $c['generic'] ) . '>' : '';

				// Backtick-escaped class label
				$lines[] = $indent . 'class ' . $class_id_bt . $generic . '["' . $title . '"]' . $class_style . ' {';
				$lines[] = $indent . '    <<' . $annotation . '>>';

				// Add members (facets) with visibility, classifiers, generic types, and colon syntax option.
				if ( ! empty( $c['facets'] ) ) {
					foreach ( $c['facets'] as $facet ) {
						$visibility = self::get_visibility_symbol( $facet['visibility'] ?? 'public' );
						$text = self::escape_text( $facet['name'] );
						$classifier = ! empty( $facet['classifier'] ) ? self::get_classifier_symbol( $facet['classifier'] ) : '';
						$generic = ! empty( $facet['generic'] ) ? '<' . self::escape_text( $facet['generic'] ) . '>' : '';
						$type = isset( $facet['type'] ) ? self::escape_text( $facet['type'] ) : '';

						// Differentiate between attributes and methods.
						if ( isset( $facet['parameters'] ) ) {
							$parameters = array_map( fn( $p ) => self::escape_text( $p['name'] ), $facet['parameters'] );
							$params_str = implode( ', ', $parameters );
							$return_type = ! empty( $facet['returnType'] ) ? self::escape_text( $facet['returnType'] ) : '';
							if ( $use_colon_syntax && $return_type ) {
								$lines[] = $indent . '    ' . $visibility . $text . '(' . $params_str . ')' . $generic . ': ' . $return_type . $classifier;
							} else {
								$lines[] = $indent . '    ' . $visibility . $text . '(' . $params_str . ')' . $generic . ' ' . $return_type . $classifier;
							}
						} else {
							if ( $use_colon_syntax && $type ) {
								$lines[] = $indent . '    ' . $visibility . $classifier . ' ' . $text . $generic . ': ' . $type;
							} else {
								$lines[] = $indent . '    ' . $visibility . $classifier . ' ' . $type . ' ' . $text . $generic;
							}
						}
					}
				}
				$lines[] = $indent . '}';
			}

			if ( $is_namespaced ) {
				$lines[] = '}';
			}
		}

		// Generate all relationships after all classes have been fully defined.
		foreach ( $components as $id => $c ) {
			foreach ( $c['relations'] as $r ) {
				if ( ! isset( $components[ $r['to'] ] ) ) {
					continue;
				}

				$from_id = self::escape_id( $id );
				$to_id = self::escape_id( $r['to'] );
				$fromCard = ! empty( $r['fromCard'] ) ? ' "' . self::escape_text( $r['fromCard'] ) . '"' : '';
				$toCard = ! empty( $r['toCard'] ) ? ' "' . self::escape_text( $r['toCard'] ) . '"' : '';
				$label = ! empty( $r['label'] ) ? ' : ' . self::escape_text( $r['label'] ) : '';

				$type = match ( $r['type'] ?? 'depends' ) {
					'extends' => '<|--',
					'emits' => '..>',
					'composition' => '*--',
					'aggregation' => 'o--',
					'realization' => '..|>',
					'lollipop' => '--()',
					default => '-->',
				};

				$lines[] = $from_id . $fromCard . ' ' . $type . $toCard . ' ' . $to_id . $label;
			}
		}

		// Add notes and links after all classes and relations have been defined.
		foreach ( $components as $id => $c ) {
			if ( ! empty( $c['note'] ) ) {
				$note_id = self::escape_id( $id );
				$note_text = self::escape_text( $c['note'] );
				$lines[] = 'note for ' . $note_id . ' "' . $note_text . '"';
			}
			// Add flexible interaction: link, click, callback
			if ( ! empty( $c['url'] ) ) {
				$link_label = ! empty( $c['link_label'] ) ? ' "' . self::escape_text( $c['link_label'] ) . '"' : '';
				$lines[] = 'link ' . self::escape_id( $id ) . ' "' . self::escape_text( $c['url'] ) . '"' . $link_label;
			}
			if ( ! empty( $c['callback'] ) ) {
				$callback_label = ! empty( $c['callback_label'] ) ? ' "' . self::escape_text( $c['callback_label'] ) . '"' : '';
				$lines[] = 'click ' . self::escape_id( $id ) . ' call ' . self::escape_text( $c['callback'] ) . $callback_label;
			}
		}

		return implode( "\n", $lines ) . "\n";
	}

	/**
	 * Returns a dynamically generated placeholder class diagram.
	 *
	 * @return string A complete and valid Mermaid class diagram definition.
	 */
	private static function get_placeholder_diagram(): string {
		$sample_components = [ 
			'api_gateway' => [ 
				'component_id' => 'api_gateway',
				'namespace' => 'CoreServices',
				'type' => 'Service',
				'title' => 'API Gateway',
				'facets' => [ 
					[ 'name' => 'core', 'visibility' => 'public' ],
					[ 'name' => 'public', 'visibility' => 'public' ],
					[ 'name' => 'handleRequest', 'visibility' => 'public', 'parameters' => [], 'returnType' => 'bool', 'classifier' => 'abstract' ],
				],
				'relations' => [ 
					[ 
						'to' => 'license_manager',
						'type' => 'depends',
						'label' => 'validates via',
						'fromCard' => '1',
						'toCard' => '1',
					],
				],
				'note' => 'Handles all incoming requests.',
				'url' => 'https://example.com/docs/api_gateway'
			],
			'license_manager' => [ 
				'component_id' => 'license_manager',
				'namespace' => 'BusinessLogic',
				'type' => 'Manager',
				'title' => 'License Manager',
				'facets' => [ 
					[ 'name' => 'core', 'visibility' => 'public' ],
					[ 'name' => 'singleton', 'visibility' => 'public', 'classifier' => 'static' ],
				],
				'relations' => [],
			],
			'user_db' => [ 
				'component_id' => 'user_db',
				'namespace' => 'DataStore',
				'type' => 'Database',
				'title' => 'User Database',
				'facets' => [],
				'relations' => [ 
					[ 
						'to' => 'license_manager',
						'type' => 'composition',
						'label' => 'contains',
					],
				],
			],
		];

		$opts = [ 
			'class_defs' => [ 
				'Service' => [ 'fill:#add8e6', 'stroke:#000' ],
				'Manager' => [ 'fill:#90ee90', 'stroke:#000' ],
				'Database' => [ 'fill:#ffe4b5', 'stroke:#000' ],
			],
			'comments' => [ 
				'This is a top-level architectural overview.',
				'The following components are for demonstration purposes only.'
			]
		];

		return self::from_components( $sample_components, $opts );
	}

	/**
	 * Sanitizes a string to be used as a Mermaid ID.
	 * IDs must be valid Mermaid identifiers (alphanumeric, underscores).
	 * @param string $id The input string.
	 * @return string The sanitized ID.
	 */
	private static function escape_id( string $id ): string {
		return preg_replace( '/[^a-zA-Z0-9_]/', '_', strtolower( $id ) );
	}

	/**
	 * Escapes text to be used safely inside Mermaid labels and definitions.
	 * @param string $text The input text.
	 * @return string The escaped text.
	 */
	private static function escape_text( string $text ): string {
		$text = str_replace( '\\', '\\\\', $text );
		$text = str_replace( '"', '\"', $text );
		$text = str_replace( "\n", "\\n", $text );
		// Allow angle brackets for generics, but escape if not part of generic type
		if ( ! preg_match( '/<.*>/', $text ) ) {
			$text = str_replace( '<', '&lt;', $text );
			$text = str_replace( '>', '&gt;', $text );
		}
		return $text;
	}

	/**
	 * Gets the corresponding Mermaid symbol for a given visibility type.
	 * @param string $visibility The visibility type (public, private, etc.).
	 * @return string The Mermaid symbol.
	 */
	private static function get_visibility_symbol( string $visibility ): string {
		return match ( strtolower( $visibility ) ) {
			'private' => '-',
			'protected' => '#',
			'package' => '~',
			default => '+', // public
		};
	}

	/**
	 * Gets the corresponding Mermaid symbol for a given classifier.
	 * @param string $classifier The classifier type (static, abstract).
	 * @return string The Mermaid symbol.
	 */
	private static function get_classifier_symbol( string $classifier ): string {
		return match ( strtolower( $classifier ) ) {
			'static' => '$',
			'abstract' => '*',
			default => '',
		};
	}

	/**
	 * Validates a single component definition.
	 * @param array $component The component data to validate.
	 * @return bool True if valid, false otherwise.
	 */
	private static function validate_component( array $component ): bool {
		if ( empty( $component['component_id'] ) ) {
			return false;
		}
		if ( ! empty( $component['relations'] ) ) {
			foreach ( $component['relations'] as $relation ) {
				if ( empty( $relation['to'] ) ) {
					return false;
				}
			}
		}
		return true;
	}
}
