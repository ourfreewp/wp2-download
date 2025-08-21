<?php

/**
 * Summary of namespace WP2\Download\Core\Commerce
 */

namespace WP2\Download\Core\Commerce;

interface ConnectionInterface {

	public function connect(): bool;

	public function disconnect(): bool;

	public function is_connected(): bool;
}
