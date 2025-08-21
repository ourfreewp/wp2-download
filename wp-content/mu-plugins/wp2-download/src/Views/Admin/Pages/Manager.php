<?php

/**
 * Summary of namespace WP2\Download\Views\Admin\Pages.
 */

namespace WP2\Download\Views\Admin\Pages;

use WP2\Download\Views\Admin\Config;

/**
 * Summary of Manager
 */
class Manager
{
    private $pages = [];

    public function __construct()
    {
        $this->pages = Config::get_pages();
    }

    public function register_hooks(): void
    {
        add_action('admin_menu', [$this, 'add_admin_pages']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function add_admin_pages(): void
    {
        foreach ($this->pages as $slug => $config) {
            add_submenu_page(
                'wp2-hub',
                'WP2 ' . $config['title'],
                $config['title'],
                'manage_options',
                'wp2-hub-' . $slug,
                function () use ($slug) {
                    $this->render_page($slug);
                }
            );
        }
    }

    public function enqueue_assets(string $hook): void
    {
        if (strpos($hook, 'wp2-hub') === false) {
            return;
        }

        wp_enqueue_style(
            'wp2-hub-styles',
            esc_url(WP2_DOWNLOAD_URL . 'src/Admin/assets/styles/main.css'),
            [],
            filemtime(WP2_DOWNLOAD_PATH . 'src/Admin/assets/styles/main.css')
        );

        wp_enqueue_script(
            'wp2-hub-scripts',
            esc_url(WP2_DOWNLOAD_URL . 'src/Admin/assets/scripts/main.js'),
            ['jquery'],
            filemtime(WP2_DOWNLOAD_PATH . 'src/Admin/assets/scripts/main.js'),
            true
        );
    }

    public function render_page(string $page_slug): void
    {
        $page_config = $this->pages[$page_slug];
        $views_dir = WP2_DOWNLOAD_PATH . 'src/Admin/views/' . $page_config['view_path'] . '/';
        $tabs = $this->get_tabs($views_dir, $page_config['tab_order']);
        $current_tab = $this->get_current_tab($tabs);

        $build_tab_url = static function (string $tab) use ($page_slug): string {
            return esc_url(add_query_arg(
                [
                    'page' => 'wp2-hub-' . $page_slug,
                    'tab' => $tab,
                ],
                admin_url('admin.php')
            ));
        };

        $render_tab = function (string $tab) use ($views_dir, $tabs): void {
            if (!isset($tabs[$tab])) {
                return;
            }
            $file = $tabs[$tab]['file'];
            if (is_readable($file)) {
                require $file;
            }
        };

        require WP2_DOWNLOAD_PATH . 'src/Admin/views/' . $page_config['view_path'] . '.php';
    }

    private function get_tabs(string $views_dir, array $order): array
    {
        $tabs = [];

        if (!is_dir($views_dir)) {
            return $tabs;
        }

        $files = glob(trailingslashit($views_dir) . '*.php');
        if (empty($files)) {
            return $tabs;
        }

        foreach ($files as $file) {
            $slug = sanitize_key(basename($file, '.php'));
            $label = ucwords(str_replace(['-', '_'], ' ', $slug));

            $tabs[$slug] = [
                'label' => $label,
                'file' => $file,
            ];
        }

        $ordered_tabs = [];
        foreach ($order as $slug) {
            if (isset($tabs[$slug])) {
                $ordered_tabs[$slug] = $tabs[$slug];
            }
        }

        foreach ($tabs as $slug => $tab) {
            if (!isset($ordered_tabs[$slug])) {
                $ordered_tabs[$slug] = $tab;
            }
        }

        return $ordered_tabs;
    }

    private function get_current_tab(array $tabs): string
    {
        $first = array_key_first($tabs) ?? '';
        if (empty($first)) {
            return '';
        }

        // Verify nonce when processing tab parameter.
        if (isset($_GET['tab']) && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'wp2_hub_tab_switch')) {
            $requested = sanitize_key(wp_unslash($_GET['tab']));
        } else {
            $requested = $first;
        }

        return array_key_exists($requested, $tabs) ? $requested : $first;
    }
}
