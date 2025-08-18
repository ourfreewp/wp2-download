<?php
namespace WP2\Download\Health;

/**
 * Class BaseCheck
 * Abstract base class for health checks on package posts.
 *
 * @package WP2\Download\Health
 */
abstract class BaseCheck implements CheckInterface {
	protected $package_post;

	public function run( \WP_Post $package_post, bool $force = false ) {
		$this->package_post = $package_post;
		return $this->perform_check( $force );
	}

	abstract protected function perform_check( bool $force = false );

	protected function get_meta( string $key, bool $single = true ) {
		if ( ! isset( $this->package_post->ID ) ) {
			return null;
		}
		return get_post_meta( $this->package_post->ID, $key, $single );
	}

	protected function update_meta( string $key, $value ) {
		if ( ! isset( $this->package_post->ID ) ) {
			return false;
		}
		return update_post_meta( $this->package_post->ID, $key, $value );
	}
}
