<?php

namespace WP2\Download\Admin;

use WP2\Download\Admin\Hub;
use WP2\Download\Admin\Settings;
use WP2\Download\Admin\Audits;
use WP2\Download\Admin\Manifests;
use WP2\Download\Admin\Packages;
use WP2\Download\Admin\Releases;
use WP2\Download\Admin\Origins;

class Init {
	public static function init() {
		( new Hub() )->register_hooks();
		( new Settings() )->register_hooks();
		( new Audits() )->register_hooks();
		( new Manifests() )->register_hooks();
		( new Packages() )->register_hooks();
		( new Releases() )->register_hooks();
		( new Origins() )->register_hooks();
	}
}