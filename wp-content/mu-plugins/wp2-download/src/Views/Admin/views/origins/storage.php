<?php
// wp-content/mu-plugins/wp2-download/src/Admin/views/origins/storage.php
defined( 'ABSPATH' ) || exit();
?>

<div class="wp2-settings__section">
	<h1>Storage (Hub)</h1>
	<p>Configure your hub-owned storage origin for packages.</p>
	<div class="card mb-4">
		<div class="card-body">
			<h6>Pathing Conventions</h6>
			<p class="text-muted">Read-only examples of how packages are stored.</p>
			<p><code>plugins/{slug}-{version}.zip</code></p>
			<p><code>themes/{slug}-{version}.zip</code></p>
		</div>
	</div>
	<div class="card mb-4">
		<div class="card-body">
			<h6>Presign Settings</h6>
			<div class="mb-3">
				<label for="linkExpiry" class="form-label">Link Expiry (minutes)</label>
				<input type="number" class="form-control" id="linkExpiry" value="15" min="5" max="30">
			</div>
			<div class="form-check form-switch mb-3">
				<input class="form-check-input" type="checkbox" id="forceDownloadHeaders">
				<label class="form-check-label" for="forceDownloadHeaders">Force-download headers</label>
			</div>
			<h6>Integrity Expectations</h6>
			<div class="form-check form-switch">
				<input class="form-check-input" type="checkbox" id="requireChecksum">
				<label class="form-check-label" for="requireChecksum">Require checksum before publish</label>
			</div>
		</div>
	</div>
	<div class="card mb-4">
		<div class="card-body">
			<h6>Fetch Snapshot</h6>
			<p class="text-muted">Fetch metadata for a test key to ensure it's accessible.</p>
			<div class="mb-3">
				<label for="r2TestKey" class="form-label">R2 Test Key</label>
				<input type="text" class="form-control" id="r2TestKey" placeholder="e.g. plugins/my-plugin-1.0.0.zip">
			</div>
			<button type="button" class="button button-secondary" onclick="fetchSnapshot('storage')">Fetch
				Snapshot</button>
		</div>
	</div>
	<hr>
</div>
