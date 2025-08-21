<?php

/**
 * Summary of namespace WP2\Download\Extensions
 */

namespace WP2\Download\Extensions;

/**
 * Initializes all extension managers.
 *
 * @component_id extensions_init
 * @namespace extensions
 * @type Bootstrap
 * @note "Initializes all extension managers."
 */
class Init {
	public static function init() {
		error_log('[WP2][DEBUG] Extensions Init');
	}
}
