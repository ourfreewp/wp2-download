<?php
/**
 * Jobs settings view.
 *
 * @package WP2 Download
 */

use WP2\Download\Views\Admin\Jobs;

if (!class_exists('\WP2\Download\Views\Admin\Jobs')) {
    echo '<div class="notice notice-error"><p>Jobs class not found.</p></div>';
    return;
}

$jobs_admin = new Jobs();
$jobs = $jobs_admin->get_jobs();
$statuses = $jobs_admin->get_statuses();

// Handle drilldown (simple example, you may want to use GET params or AJAX for real implementation).
$drilldown_job = null;
if (isset($_GET['job_id'])) {
    // Nonce verification for GET requests (recommended for sensitive actions).
    if (isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'drilldown_job_action')) {
        foreach ($jobs as $job) {
            if ($job['ID'] === $_GET['job_id']) {
                $drilldown_job = $job;
                break;
            }
        }
    } else {
        echo '<div class="notice notice-error"><p>' . esc_html__('Invalid or missing nonce for job drilldown.', 'wp2-download') . '</p></div>';
    }
}

// Handle unschedule job POST action with nonce verification.
if (isset($_POST['unschedule_job']) && isset($_POST['unschedule_job_nonce'])) {
    if (wp_verify_nonce($_POST['unschedule_job_nonce'], 'unschedule_job_action')) {
        if (isset($_POST['action_id'])) {
            $action_id = sanitize_text_field(wp_unslash($_POST['action_id']));
            $result = $jobs_admin->unschedule_job($action_id);
            if ($result) {
                echo '<div class="notice notice-success"><p>' . esc_html__('Job unscheduled successfully.', 'wp2-download') . '</p></div>';
            } else {
                echo '<div class="notice notice-error"><p>' . esc_html__('Failed to unschedule job.', 'wp2-download') . '</p></div>';
            }
        } else {
            echo '<div class="notice notice-error"><p>' . esc_html__('Missing action ID for unschedule job.', 'wp2-download') . '</p></div>';
        }
    } else {
        echo '<div class="notice notice-error"><p>' . esc_html__('Invalid nonce for unschedule job.', 'wp2-download') . '</p></div>';
    }
}

?>
<div class="wrap">
	<h1><?php esc_html_e('Scheduled Actions (Jobs)', 'wp2-download'); ?></h1>

	<?php if ($drilldown_job) : ?>
		<h2><?php esc_html_e('Job Details', 'wp2-download'); ?></h2>
		<table class="widefat fixed">
			<tbody>
				<tr><th><?php esc_html_e('ID', 'wp2-download'); ?></th><td><?php echo esc_html($drilldown_job['ID']); ?></td></tr>
				<tr><th><?php esc_html_e('Hook', 'wp2-download'); ?></th><td><?php echo esc_html($drilldown_job['hook']); ?></td></tr>
				<tr><th><?php esc_html_e('Group', 'wp2-download'); ?></th><td><?php echo esc_html($drilldown_job['group']); ?></td></tr>
				<tr><th><?php esc_html_e('Status', 'wp2-download'); ?></th><td><?php echo esc_html($drilldown_job['status']); ?></td></tr>
				<tr><th><?php esc_html_e('Scheduled Date', 'wp2-download'); ?></th><td><?php echo esc_html($drilldown_job['scheduled_date_gmt']); ?></td></tr>
				<tr><th><?php esc_html_e('Args', 'wp2-download'); ?></th><td><pre><?php echo esc_html(wp_json_encode($drilldown_job['args'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); ?></pre></td></tr>
			</tbody>
		</table>
		<?php
        // Example: drilldown to packages/adapters if present in args.
        if (!empty($drilldown_job['args']['package'])) {
            echo '<h3>' . esc_html__('Package', 'wp2-download') . '</h3>';
            echo '<pre>' . esc_html(wp_json_encode($drilldown_job['args']['package'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) . '</pre>';
        }
	    if (!empty($drilldown_job['args']['adapter'])) {
	        echo '<h3>' . esc_html__('Adapter', 'wp2-download') . '</h3>';
	        echo '<pre>' . esc_html(wp_json_encode($drilldown_job['args']['adapter'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) . '</pre>';
	    }
	    ?>
		<p><a href="<?php echo esc_url(remove_query_arg('job_id')); ?>" class="button">&larr; <?php esc_html_e('Back to Jobs', 'wp2-download'); ?></a></p>
	<?php else : ?>
		<table class="widefat fixed striped">
			<thead>
				<tr>
					<th><?php esc_html_e('ID', 'wp2-download'); ?></th>
					<th><?php esc_html_e('Hook', 'wp2-download'); ?></th>
					<th><?php esc_html_e('Group', 'wp2-download'); ?></th>
					<th><?php esc_html_e('Status', 'wp2-download'); ?></th>
					<th><?php esc_html_e('Scheduled Date', 'wp2-download'); ?></th>
					<th><?php esc_html_e('Actions', 'wp2-download'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php if (empty($jobs)) : ?>
				<tr><td colspan="6"><?php esc_html_e('No scheduled actions found.', 'wp2-download'); ?></td></tr>
			<?php else : ?>
				<?php foreach ($jobs as $job) : ?>
					<tr>
						<td><?php echo esc_html($job['ID'] ?? ''); ?></td>
						<td><?php echo esc_html($job['hook'] ?? ''); ?></td>
						<td><?php echo esc_html($job['group'] ?? ''); ?></td>
						<td><?php echo esc_html($job['status'] ?? ''); ?></td>
						<td><?php echo !empty($job['scheduled_date_gmt']) ? esc_html($job['scheduled_date_gmt']) : '-'; ?></td>
						<td>
							<a href="<?php echo esc_url(add_query_arg('job_id', $job['ID'])); ?>" class="button"><?php esc_html_e('Details', 'wp2-download'); ?></a>
							<?php if (in_array($job['status'], ['pending', 'in-progress'], true)) : ?>
								<form method="post" style="display:inline;">
									<?php wp_nonce_field('unschedule_job_action', 'unschedule_job_nonce'); ?>
									<input type="hidden" name="action_id" value="<?php echo esc_attr($job['ID']); ?>" />
									<input type="submit" name="unschedule_job" class="button" value="<?php esc_attr_e('Unschedule', 'wp2-download'); ?>" />
								</form>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	<?php endif; ?>
</div>
