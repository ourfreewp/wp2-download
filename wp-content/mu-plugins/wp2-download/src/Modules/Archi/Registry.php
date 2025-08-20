<?php
namespace WP2\Download\Modules\Archi;

use WP2\Download\Archi\Caching;

defined( 'ABSPATH' ) || exit;

/**
 * @component_id registry
 * @namespace archi
 * @type Service
 * @note "Registry service for WP2 Arch."
 * @facet {"name": "instance", "visibility": "public", "returnType": "Registry"}
 * @facet {"name": "boot", "visibility": "public", "returnType": "void"}
 * @facet {"name": "load_components", "visibility": "public", "returnType": "void"}
 * @relation {"to": "caching", "type": "dependency", "label": "uses cache"}
 */
final class Registry {
	private static ?Registry $instance = null;
	private array $components          = array();
	private array $relations_out       = array();
	private array $relations_in        = array();
	private bool $is_loaded            = false;

	public static function instance(): Registry {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function boot(): void {
		add_action( 'init', array( $this, 'load_components' ) );
	}

	public function load_components( bool $force_recache = false ): void {
		if ( $this->is_loaded && ! $force_recache ) {
			return;
		}

		$cached = get_transient( Caching::TRANSIENT_KEY );
		if ( ! $force_recache && is_array( $cached ) ) {
			$this->components    = $cached['components'] ?? array();
			$this->relations_in  = $cached['relations_in'] ?? array();
			$this->relations_out = $cached['relations_out'] ?? array();
			$this->is_loaded     = true;
			return;
		}

		$this->collect_annotations();
		$this->is_loaded = true;

		$cache_payload = array(
			'components'    => $this->components,
			'relations_in'  => $this->relations_in,
			'relations_out' => $this->relations_out,
		);
		set_transient( Caching::TRANSIENT_KEY, $cache_payload );

		do_action( 'wp2_archi_collected', $this->components );
	}

	private function collect_annotations(): void {
		$this->components = array(); // Reset before collection.
		$payloads         = (array) apply_filters( 'wp2_archi_annotations', array() );

		foreach ( $payloads as $payload ) {
			$component = $this->normalize_component( $payload );
			if ( ! $component ) {
				continue;
			}
			$id                      = $component['component_id'];
			$this->components[ $id ] = $component;
		}
		$this->index_relations();
	}

	private function index_relations(): void {
		$this->relations_in  = array();
		$this->relations_out = array();

		foreach ( $this->components as $component ) {
			$id = $component['component_id'];
			foreach ( $component['relations'] as $edge ) {
				$to   = $edge['to'];
				$type = $edge['type'];
				if ( isset( $this->components[ $to ] ) ) {
					$this->relations_out[ $id ][ $type ][] = $to;
					$this->relations_in[ $to ][ $type ][]  = $id;
				}
			}
		}
	}

	private function normalize_component( array $payload ): ?array {
		$component_id = isset( $payload['component_id'] ) ? strtolower( trim( (string) $payload['component_id'] ) ) : '';
		if ( '' === $component_id ) {
			return null;
		}

		$component = array(
			'component_id' => $component_id,
			'namespace'    => $payload['namespace'] ?? '',
			'type'         => $payload['type'] ?? 'service',
			'title'        => $payload['title'] ?? $component_id,
			'description'  => $payload['description'] ?? '',
			// Facet normalization: preserve all facet details from annotation
			'facets'       => array_values(
				array_map(
					function ( $facet ) {
						if ( is_string( $facet ) ) {
								return array(
									'name'       => $facet,
									'visibility' => 'public',
								);
						}
						if ( is_array( $facet ) ) {
							$facet['name']       = $facet['name'] ?? '';
							$facet['visibility'] = $facet['visibility'] ?? 'public';
							return $facet;
						}
						return array();
					},
					(array) ( $payload['facets'] ?? array() )
				)
			),
			'hooks'        => array(
				'provides' => array_values( array_unique( array_map( 'strval', (array) ( $payload['hooks']['provides'] ?? array() ) ) ) ),
				'consumes' => array_values( array_unique( array_map( 'strval', (array) ( $payload['hooks']['consumes'] ?? array() ) ) ) ),
			),
			'files'        => array_values( array_map( 'strval', (array) ( $payload['files'] ?? array() ) ) ),
			'source'       => array(
				'plugin' => $payload['source']['plugin'] ?? '',
				'path'   => $payload['source']['path'] ?? '',
			),
			'version'      => $payload['version'] ?? '',
			'relations'    => array(),
			'meta'         => is_array( $payload['meta'] ?? null ) ? $payload['meta'] : array(),
		);
		error_log( 'DEBUG: Registry normalized component: ' . print_r( $component, true ) );

		foreach ( (array) ( $payload['relations'] ?? array() ) as $edge ) {
			$to = strtolower( trim( (string) ( $edge['to'] ?? '' ) ) );
			if ( '' === $to ) {
				continue;
			}
			$component['relations'][] = array(
				'to'     => $to,
				'type'   => strtolower( trim( (string) ( $edge['type'] ?? 'depends' ) ) ),
				'weight' => (float) ( $edge['weight'] ?? 1.0 ),
				'label'  => (string) ( $edge['label'] ?? '' ),
			);
		}
		return $component;
	}

	public function all(): array {
		$this->load_components();
		return $this->components;
	}

	public function get( string $component_id ): ?array {
		$this->load_components();
		$component_id = strtolower( trim( $component_id ) );
		return $this->components[ $component_id ] ?? null;
	}
}
