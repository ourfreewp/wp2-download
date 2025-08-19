<?php
/**
 * @var array $packages An array of package data passed from the Hub class.
 */
?>
<div class="wrap">
	<h1>WP2 Downloads</h1>
	<p class="subtitle">A central catalog of all managed packages and their full lifecycle status.</p>
	<?php
	require_once WP2_DOWNLOAD_PATH . 'src/Admin/views/hub/overview.php';
	require_once WP2_DOWNLOAD_PATH . 'src/Admin/views/hub/catalog.php';
	require_once WP2_DOWNLOAD_PATH . 'src/Admin/views/hub/controls.php';
	require_once WP2_DOWNLOAD_PATH . 'src/Admin/views/hub/modals/setup.php';
	require_once WP2_DOWNLOAD_PATH . 'src/Admin/views/hub/modals/details-package.php';
	require_once WP2_DOWNLOAD_PATH . 'src/Admin/views/hub/modals/details-analytics.php';
	require_once WP2_DOWNLOAD_PATH . 'src/Admin/views/hub/modals/details-licensing.php';
	require_once WP2_DOWNLOAD_PATH . 'src/Admin/views/hub/modals/details-repo.php';
	require_once WP2_DOWNLOAD_PATH . 'src/Admin/views/hub/modals/details-storage.php';
	?>
</div>