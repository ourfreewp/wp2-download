<?php
/**
 * Interface for health check implementations in WP2 Download.
 *
 * Provides a contract for health check classes to implement unique identification and execution logic.
 *
 * @component_id health_check_interface
 * @namespace health
 * @type Interface
 * @note "Interface for health check implementations."
 *
 * @package WP2\Download\Modules\Health
 */

namespace WP2\Download\Modules\Health;

interface CheckInterface {
	/**
	 * Returns the unique ID for this health check.
	 *
	 * @return string Unique health check ID.
	 */
	public function get_id(): string;

	/**
	 * Runs the health check for a given package post.
	 *
	 * @param \WP_Post $package_post The package post object to check.
	 * @param bool     $force        Whether to force the check.
	 * @return array|null Health check result data or null.
	 */
	public function run( \WP_Post $package_post, bool $force = false );
}
