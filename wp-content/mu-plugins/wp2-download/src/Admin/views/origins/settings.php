<?php
// wp-content/mu-plugins/wp2-download/src/Admin/views/origins/settings.php
defined( 'ABSPATH' ) || exit();
?>

<div class="wp2-settings__section">
	<h1>Settings</h1>
	<p>Configure global defaults that affect all origins.</p>
	<div class="card mb-4">
		<div class="card-body">
			<h6>Origin Availability</h6>
			<p class="text-muted">Toggle which origin types are supported.</p>
			<div class="form-check form-switch mb-2">
				<input class="form-check-input" type="checkbox" id="composer-toggle" checked>
				<label class="form-check-label" for="composer-toggle">Composer</label>
				<p class="small text-muted mb-0">Manages packages from Composer/Packagist.</p>
			</div>
			<div class="form-check form-switch mb-2">
				<input class="form-check-input" type="checkbox" id="github-toggle" checked>
				<label class="form-check-label" for="github-toggle">GitHub</label>
				<p class="small text-muted mb-0">Connects to GitHub releases and tags.</p>
			</div>
			<div class="form-check form-switch mb-2">
				<input class="form-check-input" type="checkbox" id="wporg-toggle" checked>
				<label class="form-check-label" for="wporg-toggle">Wporg</label>
				<p class="small text-muted mb-0">Mirrors packages from the WordPress.org directory.</p>
			</div>
			<div class="form-check form-switch mb-2">
				<input class="form-check-input" type="checkbox" id="gdrive-toggle" checked>
				<label class="form-check-label" for="gdrive-toggle">Gdrive</label>
				<p class="small text-muted mb-0">Mirrors packages from Google Drive.</p>
			</div>
			<div class="form-check form-switch mb-2">
				<input class="form-check-input" type="checkbox" id="hub-toggle" checked>
				<label class="form-check-label" for="hub-toggle">Hub</label>
				<p class="small text-muted mb-0">Directly serves packages from this Hub instance (Storage tab).</p>
			</div>
		</div>
	</div>
	<div class="card mb-4">
		<div class="card-body">
			<h6>Mirroring Policy</h6>
			<p class="text-muted">Global policy for mirroring artifacts from remote origins.</p>
			<div class="form-check">
				<input class="form-check-input" type="radio" name="mirroringPolicy" id="mirrorNever" value="never"
					checked>
				<label class="form-check-label" for="mirrorNever">Never</label>
			</div>
			<div class="form-check">
				<input class="form-check-input" type="radio" name="mirroringPolicy" id="mirrorWhenAllowed"
					value="when-allowed">
				<label class="form-check-label" for="mirrorWhenAllowed">When Allowed</label>
			</div>
			<div class="form-check">
				<input class="form-check-input" type="radio" name="mirroringPolicy" id="mirrorAlways" value="always">
				<label class="form-check-label" for="mirrorAlways">Always</label>
			</div>
			<p class="small mt-2 text-muted">Legal note: Premium vendors and directories may restrict mirroring. This
				setting can be overridden in individual tabs.</p>
		</div>
	</div>
	<div class="card mb-4">
		<div class="card-body">
			<h6>Metadata Refresh Intervals</h6>
			<p class="text-muted">Time-to-live (TTL) in minutes for fetching fresh metadata.</p>
			<div class="mb-3">
				<label for="composerTtl" class="form-label">Composer (minutes)</label>
				<input type="number" class="form-control" id="composerTtl" value="15" min="10" max="60">
			</div>
			<div class="mb-3">
				<label for="githubTtl" class="form-label">GitHub (minutes)</label>
				<input type="number" class="form-control" id="githubTtl" value="15" min="10" max="60">
			</div>
			<div class="mb-3">
				<label for="wporgTtl" class="form-label">Wporg (minutes)</label>
				<input type="number" class="form-control" id="wporgTtl" value="30" min="10" max="60">
			</div>
		</div>
	</div>
	<div class="card mb-4">
		<div class="card-body">
			<h6>Licensing Enforcement</h6>
			<p class="small text-muted">
				This hub can enforce licenses for mirrored packages. The current behavior is to <a href="#">manage
					licensing through Keygen</a>. To change this, visit the Licensing settings tab.
			</p>
		</div>
	</div>
	<hr>
</div>