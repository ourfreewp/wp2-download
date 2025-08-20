<?php
namespace WP2\Download\Core\Releases\Channels;

/**
 * @component_id release_channel
 * @namespace release
 * @type Entity
 * @note "Manages release channels for packages."
 */
class Manager {
	/**
	 * Stable channel constant.
	 *
	 * @var string
	 */
	public const STABLE = 'stable';

	/**
	 * Beta channel constant.
	 *
	 * @var string
	 */
	public const BETA = 'beta';

	/**
	 * Checks if a channel name is valid.
	 *
	 * @param string $channel Channel name to validate.
	 * @return bool True if valid, false otherwise.
	 */
	public static function is_valid( string $channel ): bool {
		return in_array( $channel, array( self::STABLE, self::BETA ), true );
	}
}
