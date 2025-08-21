<?php
/**
 * Settings for the Composer origin.
 */

defined( 'ABSPATH' ) || exit();
?>

<div class="wp2-settings__section">
	<h1>Composer</h1>
	<p>Configure your Composer/Packagist origin.</p>
	<div class="card mb-4">
		<div class="card-body">
			<h6>Registry Access</h6>
			<p class="text-muted">By default, we use the public Packagist registry. You can add a private one.</p>
			<div class="mb-3">
				<label for="composerRegistryUrl" class="form-label">Custom Registry URL (Optional)</label>
				<input type="url" class="form-control" id="composerRegistryUrl" placeholder="e.g. https://my-private-registry.com">
			</div>
			<h6>Policy</h6>
			<div class="form-check form-switch mb-3">
				<input class="form-check-input" type="checkbox" id="composerMirroringToggle">
				<label class="form-check-label" for="composerMirroringToggle">Mirror this origin</label>
				<p class="small text-muted mb-0">Overrides global mirroring policy.</p>
			</div>
			<div class="mb-3">
				<label for="composerTtlOverride" class="form-label">Cache TTL Override (minutes)</label>
				<input type="number" class="form-control" id="composerTtlOverride" placeholder="e.g. 30">
			</div>
			<button type="button" class="button button-secondary">Save Changes</button>
		</div>
	</div>
	<div class="card mb-4">
		<div class="card-body">
			<h6>Snapshot Utility</h6>
			<p class="text-muted">Preview package metadata and available versions.</p>
			<div class="mb-3">
				<label for="composerPackage" class="form-label">Package (vendor/name)</label>
				<input type="text" class="form-control" id="composerPackage" placeholder="e.g. johnpbloch/wordpress-core">
			</div>
			<div class="mb-3">
				<label for="composerConstraint" class="form-label">Optional Constraint (e.g. ^6.3)</label>
				<input type="text" class="form-control" id="composerConstraint">
			</div>
			<div class="mb-3">
				<label for="composerChannel" class="form-label">Optional Channel</label>
				<input type="text" class="form-control" id="composerChannel">
			</div>
			<button type="button" class="button button-secondary" onclick="fetchSnapshot('composer')">Fetch Snapshot</button>
		</div>
	</div>
	<hr>
</div>
