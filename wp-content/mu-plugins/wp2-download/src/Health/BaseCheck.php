<?php
namespace WP2\Download\Health;

/**
 * Class BaseCheck
 * Abstract base class for health checks on package posts.
 *
 * @package WP2\Download\Health
 */
/**
 * Abstract base class for health checks on package posts.
 * Provides utility methods for meta access and contract for checks.
 */
abstract class BaseCheck implements CheckInterface {
	/**
	 * @var \WP_Post|null
	 */
	protected $package_post = null;

	/**
	 * Run the health check for a package post.
	 * @param \WP_Post $package_post
	 * @param bool $force
	 * @return array|null
	 */
	public function run( \WP_Post $package_post, bool $force = false ) {
		$this->package_post = $package_post;
		try {
			return $this->perform_check( $force );
		} catch (\Throwable $e) {
			return [ 
				'status' => 'error',
				'message' => 'Health check exception: ' . $e->getMessage(),
				'details' => [ 'exception' => get_class( $e ) ]
			];
		}
	}

	/**
	 * Perform the health check. Must be implemented by subclasses.
	 * @param bool $force
	 * @return array|null
	 */
	abstract protected function perform_check( bool $force = false );

	/**
	 * Get post meta for the package post.
	 * @param string $key
	 * @param bool $single
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
	 * @param string $key
	 * @param mixed $value
	 * @return bool
	 */
	protected function update_meta( string $key, $value ) {
		if ( ! isset( $this->package_post->ID ) ) {
			return false;
		}
		return update_post_meta( $this->package_post->ID, $key, $value );
	}
}

