<?php
/**
 * Hub Overview admin view.
 *
 * @package WP2_Download
 */

defined( 'ABSPATH' ) || exit();
?>

<div class="wp2-hub-overview d-flex flex-wrap gap-4 mb-4">
	<div class="card flex-grow-1">
		<div class="card-body d-flex flex-column justify-content-between">
			<h5>Total Packages</h5>
			<h2 class="display-4 fw-bold"><?php echo count( $packages ); ?></h2>
		</div>
	</div>
</div>
