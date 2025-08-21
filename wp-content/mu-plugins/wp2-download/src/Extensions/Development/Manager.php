<?php

/**
 * Summary of namespace WP2\Download\Extensions\Development
 */

namespace WP2\Download\Extensions\Development;

/**
 * Manages development extensions and execution.
 *
 * @component_id extensions_development_manager
 * @namespace extensions.development
 * @type Manager
 * @note "Manages development extensions and execution."
 */
class Manager
{
    protected $extensions = [];

    public function __construct()
    {
        $this->extensions = apply_filters('wp2_register_development_extensions', []);
    }

    public function run($context)
    {
        foreach ($this->extensions as $name => $class) {
            if (class_exists($class)) {
                $instance = new $class();
                if (method_exists($instance, 'run')) {
                    $instance->run($context);
                }
            }
        }
    }
}
