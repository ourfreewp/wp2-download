<?php
// wp-content/mu-plugins/wp2-download/src/Admin/views/settings/analytics.php
defined( 'ABSPATH' ) || exit();
use WP2\Download\Services\Locator;
$analytics_adapters = Locator::list_analytics_adapters();
$selected_analytics = (string) get_option( 'wp2_download_analytics_adapter', 'DefaultAdapter' );
?>

<form method="post" action="options.php">
	<?php settings_fields( 'wp2_download_settings' ); ?>
	<div class="card mb-4">
		<div class="card-header d-flex justify-content-between align-items-center">
			<span><i class="bi bi-graph-up-arrow me-2"></i>
				<?php echo esc_html__( 'Analytics', 'wp2-download' ); ?></span>
			<select class="form-select form-select-sm" id="analytics-service-select"
				name="wp2_download_analytics_adapter" style="width: auto;">
				<?php if ( empty( $analytics_adapters ) ) : ?>
					<option disabled><?php esc_html_e( 'No adapters available', 'wp2-download' ); ?></option>
				<?php else : ?>
					<?php foreach ( $analytics_adapters as $adapter ) : ?>
						<option value="<?php echo esc_attr( $adapter ); ?>" <?php selected( $selected_analytics, $adapter ); ?>>
							<?php echo esc_html( $adapter ); ?>
						</option>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
		</div>
		<div class="card-body">
			<p class="small text-muted">Configure your analytics adapter. Required: <code>WP2_POSTHOG_KEY</code>.<br>
				<?php echo defined( 'WP2_POSTHOG_KEY' ) ? esc_html( 'WP2_POSTHOG_KEY set.' ) : esc_html( 'WP2_POSTHOG_KEY not set.' ); ?>
			</p>
			<div class="mb-3">
				<label for="posthogApiKey" class="form-label">PostHog API Key</label>
				<input type="text" class="form-control" id="posthogApiKey" name="wp2_posthog_api_key"
					value="<?php echo esc_attr( get_option( 'wp2_posthog_api_key', '' ) ); ?>"
					placeholder="e.g., phc_*********************">
			</div>
			<div class="mb-3">
				<label for="posthogApiUrl" class="form-label">PostHog API URL</label>
				<input type="text" class="form-control" id="posthogApiUrl" name="wp2_posthog_api_url"
					value="<?php echo esc_attr( get_option( 'wp2_posthog_api_url', '' ) ); ?>"
					placeholder="e.g., https://app.posthog.com">
			</div>
			<button type="button" class="button button-secondary" id="test-analytics-connection">Test
				Connection</button>
			<div id="analytics-connection-status"></div>
			<button type="submit" class="button button-primary">Save Changes</button>
		</div>
	</div>
</form>