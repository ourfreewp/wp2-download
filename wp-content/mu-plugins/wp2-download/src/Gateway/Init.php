<?php
namespace WP2\Download\Gateway;

use WP2\Download\Gateway\Rewrite;
use WP2\Download\Gateway\CloudflareR2\Gateway as R2Gateway;

class Init {
	public static function init() {
		add_action( 'init', [ Rewrite::class, 'add_rewrite_rules' ] );
		add_filter( 'query_vars', [ Rewrite::class, 'add_query_vars' ] );
		add_action( 'template_redirect', [ R2Gateway::class, 'serve' ] );
	}
}
