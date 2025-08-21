<?php

/**
 * Archi Caching module.
 *
 * @package WP2_Download
 */

namespace WP2\Download\Modules\Archi;

/**
 * Archi Caching
 *
 * @component_id caching
 * @namespace archi
 * @type Service
 * @note "Caching service for WP2 Archi."
 * @facet {"name": "boot", "visibility": "public", "returnType": "void"}
 * @facet {"name": "flush_cache", "visibility": "public", "returnType": "void"}
 * @facet {"name": "rebuild_cache", "visibility": "public", "returnType": "void"}
 * @relation {"to": "registry", "type": "dependency", "label": "rebuilds registry cache"}
 * @relation {"to": "helpers", "type": "dependency", "label": "uses annotation helpers"}
 */
final class Caching
{
    public const TRANSIENT_KEY = 'wp2_archi_graph_cache';

    public function boot(): void
    {
        add_action('activated_plugin', [$this, 'flush_cache']);
        add_action('deactivated_plugin', [$this, 'flush_cache']);
    }

    public function flush_cache(): void
    {
        delete_transient(self::TRANSIENT_KEY);
    }

    public function rebuild_cache(): void
    {
        $this->flush_cache();
        Registry::instance()->load_components(true);
    }
}
