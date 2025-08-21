<?php

/**
 * Summary of namespace WP2\Download\Views\Admin
 */

namespace WP2\Download\Views\Admin;

use WP2\Download\Views\Admin\Hub;

/**
 * Summary of Init
 */
class Init {

	public static function init() {
		( new Hub() )->register_hooks();
	}
}
