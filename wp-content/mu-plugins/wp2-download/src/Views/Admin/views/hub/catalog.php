<?php
/**
 * Catalog view for WP2 Download admin hub.
 *
 * @package WP2Download
 */

?>
<div class="wp2-hub-catalog">
	<div class="d-flex justify-content-between align-items-center">
		<h5>Package Catalog</h5>
		<button class="button button-primary wp2-run-all-checks mb-2">
			<i class="dashicons dashicons-update me-1"></i> Refresh All
		</button>
	</div>
	<div class="p-0">
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th>Name</th>
					<th>Type</th>
					<th>Development</th>
					<th>Storage</th>
					<th>Licensing</th>
					<th>Analytics</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php if ( empty( $packages ) ) : ?>
					<tr>
						<td colspan="14">No packages have been ingested from manifests yet.</td>
					</tr>
				<?php else : ?>
					<?php foreach ( $packages as $package ) : ?>
						<?php
						$repo = \WP2\Download\API\Packages\Controller::get_repo_data( $package );
						$storage = \WP2\Download\API\Packages\Controller::get_storage_data( $package );
						$storage = \WP2\Download\API\Packages\Controller::get_licensing_data( $package );
						$storage = \WP2\Download\API\Packages\Controller::get_analytics_data( $package );
						$health = \WP2\Download\API\Packages\Controller::get_health_data( $package );
						?>
						<tr>
							<td class="wp2-hub-td-name">
								<?php echo esc_html( $package['name'] ?? '' ); ?>
							</td>
							<td class="wp2-hub-td-type">
								<?php echo esc_html( $package['type'] ?? '' ); ?>
							</td>
							<td class="wp2-hub-td-development">

							</td>
							<td class="wp2-hub-td-storage">

							</td>
							<td class="wp2-hub-td-licensing">

							</td>
							<td class="wp2-hub-td-analytics">

							</td>
							<td class="wp2-hub-td-actions">
								<button class="button button-secondary details-btn"
									data-slug="<?php echo esc_attr( $package['slug'] ?? '' ); ?>"
									data-package="<?php echo esc_attr( wp_json_encode( $package ) ); ?>" data-bs-toggle="modal"
									data-bs-target="#packageDetailsModal">Details</button>


							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
