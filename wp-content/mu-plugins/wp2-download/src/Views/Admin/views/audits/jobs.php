<?php
use WP2\Download\Admin\Jobs;

if ( ! class_exists( '\WP2\Download\Admin\Jobs' ) ) {
	echo '<div class="notice notice-error"><p>Jobs class not found.</p></div>';
	return;
}

$jobs_admin = new Jobs();
$jobs       = $jobs_admin->get_jobs();
$statuses   = $jobs_admin->get_statuses();

?>
<div class="wrap">
	<h1><?php esc_html_e( 'Scheduled Actions (Jobs)', 'wp2-download' ); ?></h1>
	<table class="widefat fixed striped">
		<thead>
			<tr>
				<th><?php esc_html_e( 'ID', 'wp2-download' ); ?></th>
				<th><?php esc_html_e( 'Hook', 'wp2-download' ); ?></th>
				<th><?php esc_html_e( 'Group', 'wp2-download' ); ?></th>
				<th><?php esc_html_e( 'Status', 'wp2-download' ); ?></th>
				<th><?php esc_html_e( 'Scheduled Date', 'wp2-download' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'wp2-download' ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php if ( empty( $jobs ) ) : ?>
			<tr><td colspan="6"><?php esc_html_e( 'No scheduled actions found.', 'wp2-download' ); ?></td></tr>
		<?php else : ?>
			<?php foreach ( $jobs as $job ) : ?>
				<tr>
					<td><?php echo esc_html( $job['ID'] ?? '' ); ?></td>
					<td><?php echo esc_html( $job['hook'] ?? '' ); ?></td>
					<td><?php echo esc_html( $job['group'] ?? '' ); ?></td>
					<td><?php echo esc_html( $job['status'] ?? '' ); ?></td>
					<td><?php echo ! empty( $job['scheduled_date_gmt'] ) ? esc_html( $job['scheduled_date_gmt'] ) : '-'; ?></td>
					<td>
						<?php if ( in_array( $job['status'], array( 'pending', 'in-progress' ), true ) ) : ?>
							<form method="post" style="display:inline;">
								<input type="hidden" name="action_id" value="<?php echo esc_attr( $job['ID'] ); ?>" />
								<input type="submit" name="unschedule_job" class="button" value="<?php esc_attr_e( 'Unschedule', 'wp2-download' ); ?>" />
							</form>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		</tbody>
	</table>
</div>
