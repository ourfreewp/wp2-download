<?php

namespace WP2\Download\Health;

use WP2\Download\Services\Locator;
use WP2\Download\Health\Checks\Github\RepoCheck;
use WP2\Download\Health\Checks\Github\TagCheck;
use WP2\Download\Health\Checks\CloudflareR2\ArtifactCheck;

class Init {
	public static function init() {
		$runner = Locator::get_health_runner();
		$runner->register_check( new RepoCheck() );
		$runner->register_check( new TagCheck() );
		$runner->register_check( new ArtifactCheck() );
		$runner->register_hooks();
	}
}