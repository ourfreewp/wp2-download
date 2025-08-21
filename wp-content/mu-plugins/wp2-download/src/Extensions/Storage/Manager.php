<?php

/**
 * Summary of namespace WP2\Download\Extensions\Storage
 */

namespace WP2\Download\Extensions\Storage;

/**
 * Manages storage extensions and their operations.
 *
 * @component_id extensions_storage_manager
 * @namespace extensions.storage
 * @type Manager
 * @note "Manages storage extensions and operations."
 */
class Manager
{
    protected $extensions = [];

    public function __construct()
    {
        $this->extensions = apply_filters('wp2_register_storage_extensions', []);
    }

    public function store($context)
    {
        foreach ($this->extensions as $name => $class) {
            if (class_exists($class)) {
                $instance = new $class();
                if (method_exists($instance, 'store')) {
                    $instance->store($context);
                }
            }
        }
    }
}
