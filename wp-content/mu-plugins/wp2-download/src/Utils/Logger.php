<?php

/**
 * Summary of namespace WP2\Download\Utils
 */

namespace WP2\Download\Utils;

/**
 * Logger Utility
 *
 * @component_id util_logger
 * @namespace util
 * @type Utility
 * @note "A simple logging utility for WP2 Hub."
 */
class Logger {

	/**
	 * Logs a message to the WordPress debug log.
	 *
	 * @param string $message The message to log.
	 * @param string $level   The log level (e.g., 'INFO', 'ERROR').
	 * @return void
	 */
	public static function log( string $message, string $level = 'INFO' ): void {
		if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			error_log( sprintf( '[WP2 Hub][%s] %s', $level, $message ) );
		}
	}
}
