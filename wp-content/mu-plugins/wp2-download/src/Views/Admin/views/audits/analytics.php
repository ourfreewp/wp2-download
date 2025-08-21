<?php
/**
 * Settings for the Analytics Audit.
 */

defined( 'ABSPATH' ) || exit();
?>
<div class="card p-3">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h5 class="mb-0">
			<?php echo esc_html( sprintf( __( 'Analytics Audit: %s', 'wp2-download' ), 'PostHog' ) ); ?>
		</h5>
		<a class="button button-primary" href="<?php echo esc_url( '#' ); ?>">
			<span class="dashicons dashicons-update" aria-hidden="true"></span>
			<?php echo esc_html__( 'Run Audit', 'wp2-download' ); ?>
		</a>
	</div>

	<h6 class="fw-semibold mt-2"><?php echo esc_html__( 'PostHog Summary', 'wp2-download' ); ?></h6>
	<p>
		<strong><?php echo esc_html__( 'Project API:', 'wp2-download' ); ?></strong>
		<code>https://app.posthog.com</code>
	</p>
	<p>
		<strong><?php echo esc_html__( 'Project API Key:', 'wp2-download' ); ?></strong>
		<code>phc_xxxx</code>
	</p>
	<hr>

	<h6 class="fw-semibold mt-4"><?php echo esc_html__( 'Connected Sites', 'wp2-download' ); ?></h6>
	<div class="table-responsive">
		<table class="widefat striped table-view-list">
			<thead>
				<tr>
					<th><?php echo esc_html__( 'Site URL', 'wp2-download' ); ?></th>
					<th><?php echo esc_html__( 'Last Event', 'wp2-download' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<a href="<?php echo esc_url( 'https://site-a.example.com' ); ?>" target="_blank" rel="noopener noreferrer">
							https://site-a.example.com
						</a>
					</td>
					<td><?php echo esc_html( '8/16/2025, 10:30:00 AM' ); ?></td>
				</tr>
				<tr>
					<td>
						<a href="<?php echo esc_url( 'https://site-b.example.com' ); ?>" target="_blank" rel="noopener noreferrer">
							https://site-b.example.com
						</a>
					</td>
					<td><?php echo esc_html( '8/15/2025, 6:00:00 PM' ); ?></td>
				</tr>
			</tbody>
		</table>
	</div>

	<h6 class="fw-semibold mt-4"><?php echo esc_html__( 'Last Ingested Event', 'wp2-download' ); ?></h6>
	<p>
		<strong><?php echo esc_html__( 'Event:', 'wp2-download' ); ?></strong>
		<span class="badge rounded-pill text-bg-info">$pageview</span>
	</p>
	<p>
		<strong><?php echo esc_html__( 'Timestamp:', 'wp2-download' ); ?></strong>
		<?php echo esc_html( '8/16/2025, 10:30:00 AM' ); ?>
	</p>
	<p>
		<strong><?php echo esc_html__( 'Distinct ID:', 'wp2-download' ); ?></strong>
		<?php echo esc_html( 'user_1234' ); ?>
	</p>
</div>
