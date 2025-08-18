<?php
namespace WP2\Download\Health;

interface CheckInterface {
	/**
	 * Interface CheckInterface
	 * Defines the contract for health checks on package posts.
	 *
	 * @package WP2\Download\Health
	 */
	public function get_id(): string;
	public function run( \WP_Post $package_post, bool $force = false );
}
