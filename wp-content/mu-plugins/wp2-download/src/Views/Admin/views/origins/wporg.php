<?php
// wp-content/mu-plugins/wp2-download/src/Admin/views/origins/wporg.php
defined( 'ABSPATH' ) || exit();
?>

<div class="wp2-settings__section">
	<h1>Wporg</h1>
	<p>Configure your WordPress.org directory origin.</p>
	<div class="card mb-4">
		<div class="card-body">
			<h6>Policy</h6>
			<div class="form-check form-switch mb-3">
				<input class="form-check-input" type="checkbox" id="wporgMirroringToggle">
				<label class="form-check-label" for="wporgMirroringToggle">Mirror this origin</label>
				<p class="small text-muted mb-0">Overrides global mirroring policy.</p>
			</div>
			<div class="mb-3">
				<label for="wporgTtl" class="form-label">Metadata TTL (minutes)</label>
				<input type="number" class="form-control" id="wporgTtl" value="30" min="10" max="60">
			</div>
			<button type="button" class="button button-secondary">Save Changes</button>
		</div>
	</div>
	<div class="card mb-4">
		<div class="card-body">
			<h6>Snapshot Utility</h6>
			<p class="text-muted">Preview package metadata and available versions.</p>
			<div class="mb-3">
				<label for="wporgSlug" class="form-label">Slug (plugin/theme)</label>
				<input type="text" class="form-control" id="wporgSlug" placeholder="e.g. jetpack">
			</div>
			<div class="mb-3">
				<label for="wporgType" class="form-label">Optional Type</label>
				<select class="form-select" id="wporgType">
					<option value="">Any</option>
					<option value="plugin">Plugin</option>
					<option value="theme">Theme</option>
				</select>
			</div>
			<div class="mb-3">
				<label for="wporgChannel" class="form-label">Optional Channel</label>
				<select class="form-select" id="wporgChannel">
					<option value="stable">Stable</option>
					<option value="beta">Beta</option>
				</select>
			</div>
			<button type="button" class="button button-secondary" onclick="fetchSnapshot('wporg')">Fetch
				Snapshot</button>
		</div>
	</div>
	<hr>
</div>
