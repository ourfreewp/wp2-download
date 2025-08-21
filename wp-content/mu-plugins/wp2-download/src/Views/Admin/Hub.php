<?php

/**
 * Summary of namespace WP2\Download\Views\Admin
 */

namespace WP2\Download\Views\Admin;

use WP2\Download\API\Packages\Controller as PackagesController;

/**
 * Class Hub
 * Manages the WP2 Hub admin dashboard UI and assets.
 *
 * @package WP2\Download\Views\Admin
 */
class Hub
{
    /**
     * Register hooks with WordPress.
     */
    public function register_hooks(): void
    {
        add_action('admin_menu', [$this, 'add_admin_menu_page']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    /**
     * Enqueue the CSS and JavaScript assets for the admin page.
     *
     * @param string $hook The current admin page hook.
     */
    public function enqueue_assets(string $hook): void
    {
        if ($hook !== 'toplevel_page_wp2-hub') {
            return;
        }

        // Enqueue Bootstrap CSS from CDN.
        wp_enqueue_style(
            'bootstrap',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
            [],
            '5.3.0'
        );

        // Enqueue Bootstrap Icons CSS from CDN.
        wp_enqueue_style(
            'bootstrap-icons',
            'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css',
            [],
            '1.11.1'
        );

        // Enqueue Bootstrap JS from CDN.
        wp_enqueue_script(
            'bootstrap',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
            [],
            '5.3.0',
            true
        );

        // Enqueue custom styles.
        wp_enqueue_style(
            'wp2-hub-styles',
            WP2_DOWNLOAD_URL . 'src/Admin/assets/styles/main.css',
            [],
            filemtime(WP2_DOWNLOAD_PATH . 'src/Admin/assets/styles/main.css')
        );

        // Enqueue custom scripts.
        wp_enqueue_script(
            'wp2-hub-scripts',
            WP2_DOWNLOAD_URL . 'src/Admin/assets/scripts/main.js',
            ['jquery'],
            filemtime(WP2_DOWNLOAD_PATH . 'src/Admin/assets/scripts/main.js'),
            true
        );
        // Localize API URL, download URL, and nonce for JS.
        wp_localize_script(
            'wp2-hub-scripts',
            'wp2Hub',
            [
                'apiUrl' => rest_url('wp2/v1/'),
                'downloadUrl' => home_url('/wp2-download/'),
                'ajaxNonce' => wp_create_nonce('wp_rest'),
            ]
        );
    }

    /**
     * Add the main admin menu page for the WP2 Hub.
     */
    public function add_admin_menu_page(): void
    {
        add_menu_page(
            'WP2 Package Hub',
            'WP2 Hub',
            'manage_options',
            'wp2-hub',
            [$this, 'render_dashboard_page'],
            'dashicons-cloud',
            20
        );
    }

    /**
     * Render the HTML for the main dashboard page by including the view file.
     */
    public function render_dashboard_page(): void
    {
        // Prepare data to pass to the view.
        $packages = (new PackagesController())->get_all_packages_data();

        // Include the view file.
        require_once WP2_DOWNLOAD_PATH . 'src/Admin/views/hub.php';
    }
}
