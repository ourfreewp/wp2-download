<?php

/**
 * Archi Registry module.
 *
 * @package WP2_Download
 */

namespace WP2\Download\Modules\Archi;

use WP2\Download\Modules\Archi\Caching;

/**
 * Archi Registry
 *
 * @component_id registry
 * @namespace archi
 * @type Service
 * @note "Registry service for WP2 Arch."
 * @facet {"name": "instance", "visibility": "public", "returnType": "Registry"}
 * @facet {"name": "boot", "visibility": "public", "returnType": "void"}
 * @facet {"name": "load_components", "visibility": "public", "returnType": "void"}
 * @relation {"to": "caching", "type": "dependency", "label": "uses cache"}
 */
final class Registry
{
    private static ?Registry $instance = null;
    private array $components = [];
    private array $relations_out = [];
    private array $relations_in = [];
    private bool $is_loaded = false;

    public static function instance(): Registry
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function boot(): void
    {
        add_action('init', [$this, 'load_components']);
    }

    public function load_components(bool $force_recache = false): void
    {
        if ($this->is_loaded === true && $force_recache !== true) {
            return;
        }

        $cached = get_transient(Caching::TRANSIENT_KEY);
        if ($force_recache !== true && is_array($cached)) {
            $this->components = $cached['components'] ?? [];
            $this->relations_in = $cached['relations_in'] ?? [];
            $this->relations_out = $cached['relations_out'] ?? [];
            $this->is_loaded = true;
            return;
        }

        $this->collect_annotations();
        $this->is_loaded = true;

        $cache_payload = [
            'components' => $this->components,
            'relations_in' => $this->relations_in,
            'relations_out' => $this->relations_out,
        ];
        set_transient(Caching::TRANSIENT_KEY, $cache_payload);

        do_action('wp2_archi_collected', $this->components);
    }

    private function collect_annotations(): void
    {
        $this->components = []; // Reset before collection.
        $payloads = (array) apply_filters('wp2_archi_annotations', []);

        foreach ($payloads as $payload) {
            $component = $this->normalize_component($payload);
            if ($component === null || $component === '') {
                continue;
            }
            $id = $component['component_id'];
            $this->components[$id] = $component;
        }
        $this->index_relations();
    }

    private function index_relations(): void
    {
        $this->relations_in = [];
        $this->relations_out = [];

        foreach ($this->components as $component) {
            $id = $component['component_id'];
            foreach ($component['relations'] as $edge) {
                $to = $edge['to'];
                $type = $edge['type'];
                if (isset($this->components[$to])) {
                    $this->relations_out[$id][$type][] = $to;
                    $this->relations_in[$to][$type][] = $id;
                }
            }
        }
    }

    private function normalize_component(array $payload): ?array
    {
        $component_id = $this->extract_component_id($payload);
        if ($component_id === '') {
            return null;
        }

        $component = [
            'component_id' => $component_id,
            'namespace' => $this->normalize_namespace($payload),
            'type' => $this->normalize_type($payload),
            'title' => $this->normalize_title($payload, $component_id),
            'description' => $this->normalize_description($payload),
            'facets' => $this->normalize_facets($payload['facets'] ?? []),
            'hooks' => $this->normalize_hooks($payload['hooks'] ?? []),
            'files' => $this->normalize_files($payload['files'] ?? []),
            'source' => $this->normalize_source($payload['source'] ?? []),
            'version' => $this->normalize_version($payload),
            'relations' => $this->normalize_relations($payload['relations'] ?? []),
            'meta' => $this->normalize_meta($payload['meta'] ?? []),
        ];

        error_log('DEBUG: Registry normalized component: ' . json_encode($component));
        return $component;
    }

    private function normalize_namespace(array $payload): string
    {
        return $payload['namespace'] ?? '';
    }

    private function normalize_type(array $payload): string
    {
        return $payload['type'] ?? 'service';
    }

    private function normalize_title(array $payload, string $component_id): string
    {
        return $payload['title'] ?? $component_id;
    }

    private function normalize_description(array $payload): string
    {
        return $payload['description'] ?? '';
    }

    private function normalize_version(array $payload): string
    {
        return $payload['version'] ?? '';
    }

    private function extract_component_id(array $payload): string
    {
        return isset($payload['component_id'])
            ? strtolower(trim((string) $payload['component_id']))
            : '';
    }

    private function normalize_meta($meta): array
    {
        return is_array($meta) ? $meta : [];
    }

    private function normalize_facets(array $facets): array
    {
        $result = [];
        foreach ($facets as $facet) {
            if (is_string($facet)) {
                $result[] = [
                    'name' => $facet,
                    'visibility' => 'public',
                ];
            } elseif (is_array($facet)) {
                $facet['name'] = $facet['name'] ?? '';
                $facet['visibility'] = $facet['visibility'] ?? 'public';
                $result[] = $facet;
            } else {
                $result[] = [];
            }
        }
        return $result;
    }

    private function normalize_hooks(array $hooks): array
    {
        $provides = [];
        $consumes = [];
        if (isset($hooks['provides'])) {
            foreach ((array) $hooks['provides'] as $hook) {
                $provides[] = (string) $hook;
            }
            $provides = array_values(array_unique($provides));
        }
        if (isset($hooks['consumes'])) {
            foreach ((array) $hooks['consumes'] as $hook) {
                $consumes[] = (string) $hook;
            }
            $consumes = array_values(array_unique($consumes));
        }
        return [
            'provides' => $provides,
            'consumes' => $consumes,
        ];
    }

    private function normalize_files(array $files): array
    {
        $result = [];
        foreach ($files as $file) {
            $result[] = (string) $file;
        }
        return $result;
    }

    private function normalize_source(array $source): array
    {
        return [
            'plugin' => $source['plugin'] ?? '',
            'path' => $source['path'] ?? '',
        ];
    }

    private function normalize_relations(array $relations): array
    {
        $result = [];
        foreach ($relations as $edge) {
            $to = strtolower(trim((string) ($edge['to'] ?? '')));
            if ($to === '') {
                continue;
            }
            $result[] = [
                'to' => $to,
                'type' => strtolower(trim((string) ($edge['type'] ?? 'depends'))),
                'weight' => (float) ($edge['weight'] ?? 1.0),
                'label' => (string) ($edge['label'] ?? ''),
            ];
        }
        return $result;
    }

    public function all(): array
    {
        $this->load_components();
        return $this->components;
    }

    public function get(string $component_id): ?array
    {
        $this->load_components();
        $component_id = strtolower(trim($component_id));
        return $this->components[$component_id] ?? null;
    }
}
