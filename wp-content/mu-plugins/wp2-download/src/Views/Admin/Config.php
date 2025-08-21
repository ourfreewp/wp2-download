<?php

/**
 * WP2 Download – Admin configuration for the plugin.
 *
 * @package WP2\Download
 */

namespace WP2\Download\Views\Admin;

/**
 * Summary of Config
 */
class Config
{
    public static function get_pages(): array
    {
        return [
            'audits' => [
                'title' => __('Audits', 'wp2-download'),
                'view_path' => 'audits',
                'tab_order' => ['system', 'development', 'storage', 'analytics', 'licensing'],
            ],
            'settings' => [
                'title' => __('Settings', 'wp2-download'),
                'view_path' => 'settings',
                'tab_order' => ['system', 'development', 'storage', 'analytics', 'licensing'],
            ],
            'origins' => [
                'title' => __('Origins', 'wp2-download'),
                'view_path' => 'origins',
                'tab_order' => ['settings', 'storage', 'composer', 'wporg', 'github', 'gdrive'],
            ],
            'packages' => [
                'title' => __('Packages', 'wp2-download'),
                'view_path' => 'packages',
                'tab_order' => ['overview', 'plugins', 'themes', 'mu-plugins'],
            ],
            'releases' => [
                'title' => __('Releases', 'wp2-download'),
                'view_path' => 'releases',
                'tab_order' => ['overview', 'plugins', 'themes', 'mu-plugins'],
            ],
            'accounts' => [
                'title' => __('Accounts', 'wp2-download'),
                'view_path' => 'accounts',
                'tab_order' => ['system', 'development', 'storage', 'analytics', 'licensing'],
            ],
            'machines' => [
                'title' => __('Machines', 'wp2-download'),
                'view_path' => 'machines',
                'tab_order' => ['system', 'development', 'storage', 'analytics', 'licensing'],
            ],
        ];
    }
}
