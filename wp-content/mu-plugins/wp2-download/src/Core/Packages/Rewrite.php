<?php
namespace WP2\Download\Core\Packages;

class Rewrite {
	public static function add_rewrite_rules() {
		add_rewrite_rule(
			'^wp2-download/([^/]+)/([^/]+)/([^/]+)/?$',
			'index.php?wp2_package_type=$matches[1]&wp2_package_slug=$matches[2]&wp2_package_version=$matches[3]',
			'top'
		);
	}

	public static function add_query_vars( $vars ) {
		$vars[] = 'wp2_package_type';
		$vars[] = 'wp2_package_slug';
		$vars[] = 'wp2_package_version';
		return $vars;
	}
}
