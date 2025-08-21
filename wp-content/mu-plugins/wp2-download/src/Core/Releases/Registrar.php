<?php

/**
 * Release Registrar
 *
 * @package wp2-download
 */

namespace WP2\Download\Core\Releases;

/**
 * Handles registration of release-related taxonomies.
 */
class Registrar
{
    private $types = ['plugin', 'theme', 'mu'];

    /**
     * Self-bootstrap: registers taxonomies for discovered types on `init`.
     *
     * @return void
     */
    public static function init(): void
    {
        add_action(
            'init',
            static function (): void {
                $types = [];

                foreach ((new self())->types as $type) {
                    $types[] = $type;
                }

                if (empty($types)) {
                    return;
                }

                $registrar = new self();
                foreach ($types as $type) {
                    $registrar->register_taxonomies($type);
                }
            },
            9
        );
    }

    /**
     * Register private taxonomies used for fast lookups.
     *
     * @param string $type Release type slug (e.g. `pkg`, `theme`, etc.).
     * @return void
     */
    public function register_taxonomies(string $type): void
    {
        $object_type = "wp2_{$type}_rel";

        register_taxonomy(
            'wp2_rel_channel',
            $object_type,
            [
                'public' => false,
                'show_ui' => false,
                'show_in_rest' => false,
                'hierarchical' => false,
                'rewrite' => false,
                'labels' => [
                    'name' => __('Release Channels', 'wp2-download'),
                    'singular_name' => __('Release Channel', 'wp2-download'),
                ],
            ]
        );

        register_taxonomy(
            'wp2_rel_version',
            $object_type,
            [
                'public' => false,
                'show_ui' => false,
                'show_in_rest' => false,
                'hierarchical' => false,
                'rewrite' => false,
                'labels' => [
                    'name' => __('Release Versions', 'wp2-download'),
                    'singular_name' => __('Release Version', 'wp2-download'),
                ],
            ]
        );
    }
}
