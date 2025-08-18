<?php
namespace WP2\Download\Release;

/**
 * Class Channel
 * Manages release channels for packages.
 *
 * @package WP2\Download\Release
 */
class Channel {
	/**
	 * Stable channel constant.
	 * @var string
	 */
	public const STABLE = 'stable';

	/**
	 * Beta channel constant.
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
		return in_array( $channel, [ self::STABLE, self::BETA ], true );
	}
}
