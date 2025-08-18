<?php
namespace WP2\Download\Content;

use WP2\Download\Content\Packages\Registrar as ContentRegistrar;

class Init {
	public static function init() {
		add_action( 'init', [ ContentRegistrar::class, 'register' ] );
	}
}
