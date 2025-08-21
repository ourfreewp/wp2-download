<?php

/**
 * Summary of namespace WP2\Download\Core\Analytics
 */

namespace WP2\Download\Core\Analytics;

/**
 * Interface for analytics connection adapters.
 *
 * @component_id analytics_connection
 * @namespace analytics
 * @type Interface
 * @note "Interface for analytics connection adapters."
 */
interface ConnectionInterface
{
    /**
     * Connect to the analytics service.
     *
     * @return bool True on success, false on failure.
     */
    public function connect(): bool;

    /**
     * Track an analytics event.
     *
     * @param string $event_name
     * @param array  $properties
     * @return void
     */
    public function track_event(string $event_name, array $properties = []): void;
}
