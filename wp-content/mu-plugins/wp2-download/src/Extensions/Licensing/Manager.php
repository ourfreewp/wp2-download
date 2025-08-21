<?php

/**
 * Summary of namespace WP2\Download\Extensions\Licensing
 */

namespace WP2\Download\Extensions\Licensing;

/**
 * Manages licensing extensions and validation.
 *
 * @component_id extensions_licensing_manager
 * @namespace extensions.licensing
 * @type Manager
 * @note "Manages licensing extensions and validation."
 */
class Manager
{
    protected $extensions = [];

    public function __construct()
    {
        $this->extensions = apply_filters('wp2_register_licensing_extensions', []);
    }

    public function validate_license($context)
    {
        foreach ($this->extensions as $name => $class) {
            if (class_exists($class)) {
                $instance = new $class();
                if (method_exists($instance, 'validate_license')) {
                    if (!$instance->validate_license($context)) {
                        return false;
                    }
                }
            }
        }
        return true;
    }
}
