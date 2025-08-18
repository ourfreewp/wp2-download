<?php
// wp-content/mu-plugins/wp2-download/src/Admin/views/origins/github.php
defined( 'ABSPATH' ) || exit();
?>

<div class="wp2-settings__section">
	<h1>GitHub</h1>
	<p>Configure your GitHub releases/tags origin.</p>
	<div class="card mb-4">
		<div class="card-body">
			<h6>Snapshot Utility</h6>
			<p class="text-muted">Preview release assets and metadata.</p>
			<div class="mb-3">
				<label for="githubOwner" class="form-label">Owner</label>
				<input type="text" class="form-control" id="githubOwner" placeholder="e.g. Automattic">
			</div>
			<div class="mb-3">
				<label for="githubRepo" class="form-label">Repository</label>
				<input type="text" class="form-control" id="githubRepo" placeholder="e.g. jetpack">
			</div>
			<div class="mb-3">
				<label for="githubTagPattern" class="form-label">Optional Tag Pattern (e.g. v1.*)</label>
				<input type="text" class="form-control" id="githubTagPattern">
			</div>
			<div class="mb-3">
				<label for="githubAssetName" class="form-label">Optional Asset Name Pattern</label>
				<input type="text" class="form-control" id="githubAssetName" placeholder="e.g. my-plugin.*zip">
			</div>
			<button type="button" class="button button-secondary" onclick="fetchSnapshot('github')">Fetch
				Snapshot</button>
		</div>
	</div>
	<hr>
</div>