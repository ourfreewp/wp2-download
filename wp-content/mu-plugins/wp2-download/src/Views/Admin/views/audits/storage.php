<?php
// wp-content/mu-plugins/wp2-download/src/Admin/views/audits/storage.php
defined( 'ABSPATH' ) || exit();
?>

	<div class="card p-3">
		<div class="d-flex justify-content-between align-items-center mb-3">
			<h5 class="mb-0">
				<?php echo esc_html( sprintf( __( 'Storage Audit: %s', 'wp2-download' ), 'Cloudflare R2' ) ); ?>
			</h5>
			<a class="button button-primary" href="<?php echo esc_url( '#' ); ?>">
				<span class="dashicons dashicons-update" aria-hidden="true"></span>
				<?php echo esc_html__( 'Run Audit', 'wp2-download' ); ?>
			</a>
		</div>

		<h6 class="fw-semibold mt-2">
			<?php echo esc_html__( 'Releases in R2 Bucket:', 'wp2-download' ); ?>
			<span class="text-success"><?php echo esc_html( 'my-wp2-packages' ); ?></span>
		</h6>

		<div class="table-responsive">
			<table class="widefat striped table-view-list">
				<thead>
					<tr>
						<th><?php echo esc_html__( 'Version', 'wp2-download' ); ?></th>
						<th><?php echo esc_html__( 'Artifact', 'wp2-download' ); ?></th>
						<th><?php echo esc_html__( 'Date', 'wp2-download' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>v1.1.2</td>
						<td><code>wp2-download-v1.1.2.zip</code></td>
						<td><?php echo esc_html( '8/16/2025, 12:00:00 PM' ); ?></td>
					</tr>
					<tr>
						<td>v1.1.1</td>
						<td><code>wp2-download-v1.1.1.zip</code></td>
						<td><?php echo esc_html( '8/10/2025, 9:15:00 AM' ); ?></td>
					</tr>
				</tbody>
			</table>
		</div>

		<h6 class="fw-semibold mt-4 text-danger"><?php echo esc_html__( 'Missing Artifacts', 'wp2-download' ); ?></h6>
		<div class="table-responsive">
			<table class="widefat striped table-view-list">
				<thead>
					<tr>
						<th><?php echo esc_html__( 'Version', 'wp2-download' ); ?></th>
						<th><?php echo esc_html__( 'Error', 'wp2-download' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>v1.0.1</td>
						<td><span
								class="notice-inline notice-error"><?php echo esc_html__( 'Artifact not found in bucket', 'wp2-download' ); ?></span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
