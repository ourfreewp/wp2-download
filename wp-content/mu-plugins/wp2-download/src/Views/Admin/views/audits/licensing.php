<?php
/**
 * Licensing Audit View
 *
 * @file licensing.php
 */

defined( 'ABSPATH' ) || exit();
?>


	<div class="card p-3">
		<div class="d-flex justify-content-between align-items-center mb-3">
			<h5 class="mb-0">
				<?php echo esc_html( sprintf( __( 'Licensing Audit: %s', 'wp2-download' ), 'Keygen' ) ); ?>
			</h5>
			<a class="button button-primary" href="<?php echo esc_url( '#' ); ?>">
				<span class="dashicons dashicons-update" aria-hidden="true"></span>
				<?php echo esc_html__( 'Run Audit', 'wp2-download' ); ?>
			</a>
		</div>

		<h6 class="fw-semibold mt-2"><?php echo esc_html__( 'Licensing Summary', 'wp2-download' ); ?></h6>
		<p><strong><?php echo esc_html__( 'Keygen Account ID:', 'wp2-download' ); ?></strong>
			<?php echo esc_html( 'your-keygen-account-id' ); ?></p>
		<p><strong><?php echo esc_html__( 'API Status:', 'wp2-download' ); ?></strong>
			<?php echo esc_html__( 'OK', 'wp2-download' ); ?></p>
		<hr>

		<h6 class="fw-semibold mt-4"><?php echo esc_html__( 'Managed Licenses', 'wp2-download' ); ?></h6>
		<div class="table-responsive">
			<table class="widefat striped table-view-list">
				<thead>
					<tr>
						<th><?php echo esc_html__( 'License Key', 'wp2-download' ); ?></th>
						<th><?php echo esc_html__( 'Status', 'wp2-download' ); ?></th>
						<th><?php echo esc_html__( 'Activations', 'wp2-download' ); ?></th>
						<th><?php echo esc_html__( 'Created', 'wp2-download' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><code>xxxx-xxxx-xxxx-xxxx</code></td>
						<td><span
								class="badge rounded-pill text-bg-success"><?php echo esc_html__( 'ACTIVE', 'wp2-download' ); ?></span>
						</td>
						<td>3 / 5</td>
						<td>2023-01-15</td>
					</tr>
					<tr>
						<td><code>yyyy-yyyy-yyyy-yyyy</code></td>
						<td><span
								class="badge rounded-pill text-bg-warning"><?php echo esc_html__( 'SUSPENDED', 'wp2-download' ); ?></span>
						</td>
						<td>1 / 1</td>
						<td>2022-11-01</td>
					</tr>
				</tbody>
			</table>
		</div>

		<h6 class="fw-semibold mt-4"><?php echo esc_html__( 'Managed Policies', 'wp2-download' ); ?></h6>
		<div class="table-responsive">
			<table class="widefat striped table-view-list">
				<thead>
					<tr>
						<th><?php echo esc_html__( 'Policy Name', 'wp2-download' ); ?></th>
						<th><?php echo esc_html__( 'ID', 'wp2-download' ); ?></th>
						<th><?php echo esc_html__( 'Max Activations', 'wp2-download' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><?php echo esc_html__( 'Standard Plugin License', 'wp2-download' ); ?></td>
						<td>12345-policy</td>
						<td>5</td>
					</tr>
					<tr>
						<td><?php echo esc_html__( 'Unlimited License', 'wp2-download' ); ?></td>
						<td>67890-policy</td>
						<td>999</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
