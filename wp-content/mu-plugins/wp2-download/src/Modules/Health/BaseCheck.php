<?php
/**
 * Base class for health checks on package posts.
 *
 * @component_id health_base_check
 * @namespace health
 * @type Abstract
 * @note "Abstract base class for health checks on package posts."
 *
 * @package wp2-download
 */

namespace WP2\Download\Modules\Health;

/**
 * Abstract base class for health checks on package posts.
 *
 * Provides common methods for running health checks and managing post meta.
 *
 * @package wp2-download
 */
abstract class BaseCheck implements CheckInterface {
	/**
	 * @var \WP_Post|null
	 */
	protected $package_post = null;

	/**
	 * Run the health check for a package post.
	 *
	 * @param \WP_Post $package_post
	 * @param bool     $force
	 * @return array|null
	 */
	public function run( \WP_Post $package_post, bool $force = false ) {
		$this->package_post = $package_post;
		try {
			return $this->perform_check( $force );
		} catch ( \Throwable $e ) {
			return array(
				'status'  => 'error',
				'message' => 'Health check exception: ' . $e->getMessage(),
				'details' => array( 'exception' => get_class( $e ) ),
			);
		}
	}

	/**
	 * Perform the health check. Must be implemented by subclasses.
	 *
	 * @param bool $force
	 * @return array|null
	 */
	abstract protected function perform_check( bool $force = false );

	/**
	 * Get post meta for the package post.
	 *
	 * @param string $key
	 * @param bool   $single
	 * @return mixed|null
	 */
	protected function get_meta( string $key, bool $single = true ) {
		if ( ! isset( $this->package_post->ID ) ) {
			return null;
		}
		return get_post_meta( $this->package_post->ID, $key, $single );
	}

	/**
	 * Update post meta for the package post.
	 *
	 * @param string $key
	 * @param mixed  $value
	 * @return bool
	 */
	protected function update_meta( string $key, $value ) {
		if ( ! isset( $this->package_post->ID ) ) {
			return false;
		}
		return update_post_meta( $this->package_post->ID, $key, $value );
	}
}
