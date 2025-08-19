<?php
// wp-content/mu-plugins/wp2-download/src/Admin/views/origins/gdrive.php
defined( 'ABSPATH' ) || exit();
?>

<div class="wp2-settings__section">
	<h1>Gdrive</h1>
	<p>Configure your Google Drive origin.</p>
	<div class="card mb-4">
		<div class="card-body">
			<h6>Credentials & Policy</h6>
			<div class="mb-3">
				<label for="gdriveCredentials" class="form-label">Service Account Summary</label>
				<textarea class="form-control" id="gdriveCredentials" rows="3"
					placeholder="Service account details here..."></textarea>
				<div class="form-text">Scopes hint: drive.readonly</div>
			</div>
			<div class="form-check form-switch mb-3">
				<input class="form-check-input" type="checkbox" id="gdriveMirroringToggle">
				<label class="form-check-label" for="gdriveMirroringToggle">Mirror this origin</label>
				<p class="small text-muted mb-0">Updates are via direct-vendor by default (no mirroring unless
					explicitly permitted).</p>
			</div>
			<div class="mb-3">
				<label for="gdriveTtl" class="form-label">Metadata TTL (minutes)</label>
				<input type="number" class="form-control" id="gdriveTtl" value="60" min="30" max="120">
			</div>
			<button type="button" class="button button-secondary">Save Changes</button>
		</div>
	</div>
	<div class="card mb-4">
		<div class="card-body">
			<h6>Snapshot Utility</h6>
			<p class="text-muted">Preview file metadata and download viability.</p>
			<div class="mb-3">
				<label for="gdriveFileId" class="form-label">File ID</label>
				<input type="text" class="form-control" id="gdriveFileId" placeholder="e.g. 1a2b3c4d5e6f7g8h9i0j">
			</div>
			<button type="button" class="button button-secondary" onclick="fetchSnapshot('gdrive')">Fetch
				Snapshot</button>
		</div>
	</div>
	<hr>
</div>