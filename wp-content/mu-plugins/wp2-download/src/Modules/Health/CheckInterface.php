<?php
namespace WP2\Download\Modules\Health;

/**
 * @component_id health_check_interface
 * @namespace health
 * @type Interface
 * @note "Interface for health check implementations."
 */
interface CheckInterface {
	/**
	 * Returns the unique ID for this health check.
	 * @return string
	 */
	public function get_id(): string;

	/**
	 * Runs the health check for a given package post.
	 * @param \WP_Post $package_post
	 * @param bool $force
	 * @return array|null
	 */
	public function run( \WP_Post $package_post, bool $force = false );
}
