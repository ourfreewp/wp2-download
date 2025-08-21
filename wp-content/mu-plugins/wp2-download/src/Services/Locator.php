<?php

/**
 * Summary of namespace WP2\Download\Services
 */

namespace WP2\Download\Services;

use WP2\Download\Core\Origins\Adapters\ConnectionInterface;

/**
 * Service locator for adapters and origins.
 *
 * @component_id services_locator
 * @namespace services
 * @type Service
 * @note "Service locator for adapters and origins."
 */
class Locator
{
    private static $health_runner = null;
    protected static array $origins = [];

    private static function scan_adapter_dir(string $dir): array
    {
        $adapters = [];
        if (is_dir($dir) === true) {
            foreach (glob($dir . '/*Adapter.php') as $file) {
                $adapters[] = basename($file, '.php');
            }
        }
        sort($adapters, SORT_NATURAL | SORT_FLAG_CASE);
        return $adapters;
    }

    public static function list_storage_adapters(): array
    {
        if (defined('WP2_DOWNLOAD_PATH')) {
            $dir = WP2_DOWNLOAD_PATH . '/src/Storage/Adapters';
            return self::scan_adapter_dir($dir);
        }
        return [];
    }

    public static function list_development_adapters(): array
    {
        if (defined('WP2_DOWNLOAD_PATH')) {
            $dir = WP2_DOWNLOAD_PATH . '/src/Development/Adapters';
            return self::scan_adapter_dir($dir);
        }
        return [];
    }

    public static function list_licensing_adapters(): array
    {
        if (defined('WP2_DOWNLOAD_PATH')) {
            $dir = WP2_DOWNLOAD_PATH . '/src/Licensing/Adapters';
            return self::scan_adapter_dir($dir);
        }
        return [];
    }

    public static function list_analytics_adapters(): array
    {
        if (defined('WP2_DOWNLOAD_PATH')) {
            $dir = WP2_DOWNLOAD_PATH . '/src/Analytics/Adapters';
            return self::scan_adapter_dir($dir);
        }
        return [];
    }

    public static function get_selected_adapter_slug(string $service): string
    {
        $option_key = 'wp2_download_' . $service . '_adapter';
        $val = get_option($option_key);
        return (is_string($val) && $val !== '') ? $val : 'unset';
    }

    private static function build_adapter_fqcn(string $service, string $adapter_slug): string
    {
        $namespace_map = [
            'analytics' => '\\WP2\\Download\\Core\\Analytics\\Adapters',
            'licensing' => '\\WP2\\Download\\Core\\Licensing\\Adapters',
            'storage' => '\\WP2\\Download\\Core\\Storage\\Adapters',
            'development' => '\\WP2\\Download\\Core\\Development\\Adapters',
        ];
        $ns = $namespace_map[$service] ?? '';
        return $ns . '\\' . $adapter_slug;
    }

    public static function resolve_adapter_instance(string $service)
    {
        $slug = self::get_selected_adapter_slug($service);
        if ($slug === 'unset') {
            return null;
        }
        $fqcn = self::build_adapter_fqcn($service, $slug);
        if (class_exists($fqcn)) {
            return new $fqcn();
        }
        return null;
    }

    public static function storage()
    {
        static $instance = null;
        if (!isset($instance)) {
            $instance = self::resolve_adapter_instance('storage');
        }
        return $instance;
    }

    public static function development()
    {
        static $instance = null;
        if (!isset($instance)) {
            $instance = self::resolve_adapter_instance('development');
        }
        return $instance;
    }

    public static function licensing()
    {
        static $instance = null;
        if (!isset($instance)) {
            $instance = self::resolve_adapter_instance('licensing');
        }
        return $instance;
    }

    public static function analytics()
    {
        static $instance = null;
        if (!isset($instance)) {
            $instance = self::resolve_adapter_instance('analytics');
        }
        return $instance;
    }

    public static function list_origin_kinds(): array
    {
        $kinds = ['composer', 'github', 'gdrive', 'storage', 'wporg'];
        sort($kinds, SORT_STRING);
        return $kinds;
    }

    private static function build_origin_fqcn(string $kind): string
    {
        $map = [
            'composer' => '\\WP2\\Download\\Core\\Origins\\Adapters\\Composer\\Adapter',
            'github' => '\\WP2\\Download\\Core\\Origins\\Adapters\\Github\\Adapter',
            'gdrive' => '\\WP2\\Download\\Core\\Origins\\Adapters\\GoogleDrive\\Adapter',
            'storage' => '\\WP2\\Download\\Core\\Origins\\Adapters\\Storage\\Adapter',
            'wporg' => '\\WP2\\Download\\Core\\Origins\\Adapters\\WP\\Adapter',
        ];
        return $map[$kind] ?? '';
    }

    public static function origin(string $kind): ?ConnectionInterface
    {
        $kind = strtolower(trim($kind));
        if (isset(self::$origins[$kind])) {
            return self::$origins[$kind];
        }
        $fqcn = self::build_origin_fqcn($kind);
        if ($fqcn && class_exists($fqcn)) {
            self::$origins[$kind] = new $fqcn();
            return self::$origins[$kind];
        }
        return null;
    }

    public static function get_origin_adapter_label(string $kind): string
    {
        $adapter = self::origin($kind);
        if ($adapter && method_exists($adapter, 'get_label')) {
            return $adapter->get_label();
        }
        return ucfirst($kind);
    }

    public static function register_default_origins(): void
    {
        self::$origins = [
            'composer' => new \WP2\Download\Core\Origins\Adapters\Composer\Adapter(),
            'github' => new \WP2\Download\Core\Origins\Adapters\Github\Adapter(),
            'gdrive' => new \WP2\Download\Core\Origins\Adapters\GoogleDrive\Adapter(),
            'storage' => new \WP2\Download\Core\Origins\Adapters\Storage\Adapter(),
            'wporg' => new \WP2\Download\Core\Origins\Adapters\WP\Adapter(),
        ];
    }

    public static function get_health_runner()
    {
        if (self::$health_runner === null) {
            self::$health_runner = new \WP2\Download\Modules\Health\Runner();
        }
        return self::$health_runner;
    }
}
