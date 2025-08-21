<?php
/**
 * Settings for system-level configurations in WP2 Download.
 */

defined( 'ABSPATH' ) || exit();

use WP2\Download\Services\Locator;

$storage_adapters = Locator::list_storage_adapters();
$dev_adapters = Locator::list_development_adapters();
$licensing_adapters = Locator::list_licensing_adapters();
$analytics_adapters = Locator::list_analytics_adapters();

$selected_storage = (string) get_option( 'wp2_download_storage_adapter', 'DefaultAdapter' );
$selected_development = (string) get_option( 'wp2_download_development_adapter', 'DefaultAdapter' );
$selected_licensing = (string) get_option( 'wp2_download_licensing_adapter', 'DefaultAdapter' );
$selected_analytics = (string) get_option( 'wp2_download_analytics_adapter', 'DefaultAdapter' );
?>

<form method="post" action="options.php">
	<?php settings_fields( 'wp2_download_settings' ); ?>

	<div class="wp2-download-settings-system">
		<h1><?php echo esc_html__( 'System Settings', 'wp2-download' ); ?></h1>
		<p><?php echo esc_html__( 'Configure system-level settings for WP2 Download.', 'wp2-download' ); ?></p>

		<div class="card mb-4">
			<div class="card-body">
				<h2><?php echo esc_html__( '1. Server Setup (One-Time)', 'wp2-download' ); ?></h2>
				<p><?php echo esc_html__( 'Add the following to your', 'wp2-download' ); ?> <code>wp-config.php</code>
					<?php echo esc_html__( 'file with your own values:', 'wp2-download' ); ?></p>
				<div class="bg-light p-3 rounded mb-3 overflow-auto">
					<pre><code class="text-dark">define('WP2_HUB_INGEST_TOKEN',
							'your-strong-unique-ingest-token');</code></pre>
				</div>
				<p class="small text-muted">
					<?php echo esc_html__( 'You must generate a strong, unique ingest token yourself. This token is required for the', 'wp2-download' ); ?>
					<code>/ingest-release</code>
					<?php echo esc_html__( 'API endpoint and should be kept secret.', 'wp2-download' ); ?>
				</p>
				<p><?php echo esc_html__( 'The very first time, you must manually upload the', 'wp2-download' ); ?>
					<code>wp2-download.php</code> <?php echo esc_html__( 'loader and the', 'wp2-download' ); ?>
					<code>wp2-download</code> <?php echo esc_html__( 'directory to your', 'wp2-download' ); ?>
					<code>wp-content/mu-plugins/</code> <?php echo esc_html__( 'directory.', 'wp2-download' ); ?>
				</p>
			</div>
		</div>

		<div class="card mb-4">
			<div class="card-header d-flex justify-content-between align-items-center">
				<span><i class="bi bi-gear-fill me-2"></i>
					<?php echo esc_html__( 'Development', 'wp2-download' ); ?></span>
				<select class="form-select form-select-sm" id="dev-service-select"
					name="wp2_download_development_adapter" style="width: auto;">
					<?php foreach ( $dev_adapters as $adapter ) : ?>
						<option value="<?php echo esc_attr( $adapter ); ?>" <?php selected( $selected_development, $adapter ); ?>>
							<?php echo esc_html( $adapter ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="card-body">
				<div id="dev-service-github-content">
					<p class="small text-muted">Configure your development adapter. Required:
						<code>WP2_GITHUB_PAT</code> (Personal Access Token).<br>
						<?php echo defined( 'WP2_GITHUB_PAT' ) ? esc_html( 'WP2_GITHUB_PAT set.' ) : ''; ?>
					</p>
					<button type="button" class="button button-secondary" id="test-dev-connection">Test
						Connection</button>
					<div id="dev-connection-status"></div>
				</div>
			</div>
		</div>

		<div class="card mb-4">
			<div class="card-header d-flex justify-content-between align-items-center">
				<span><i class="bi bi-key-fill me-2"></i>
					<?php echo esc_html__( 'Licensing', 'wp2-download' ); ?></span>
				<select class="form-select form-select-sm" id="licensing-service-select"
					name="wp2_download_licensing_adapter" style="width: auto;">
					<?php foreach ( $licensing_adapters as $adapter ) : ?>
						<option value="<?php echo esc_attr( $adapter ); ?>" <?php selected( $selected_licensing, $adapter ); ?>>
							<?php echo esc_html( $adapter ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="card-body">
				<div id="licensing-service-none-content">
					<p class="small text-muted">Configure your licensing adapter. Required:
						<code>WP2_KEYGEN_ACCOUNT</code>,
						<code>WP2_KEYGEN_TOKEN</code>.<br>
						<?php echo defined( 'WP2_KEYGEN_ACCOUNT' ) ? esc_html( 'WP2_KEYGEN_ACCOUNT set.' ) : ''; ?>
						<?php echo defined( 'WP2_KEYGEN_TOKEN' ) ? esc_html( 'WP2_KEYGEN_TOKEN set.' ) : ''; ?>
					</p>
					<button type="button" class="button button-secondary" id="test-licensing-connection">Test
						Connection</button>
					<div id="licensing-connection-status"></div>
				</div>
			</div>
		</div>

		<div class="card mb-4">
			<div class="card-header d-flex justify-content-between align-items-center">
				<span><i class="bi bi-graph-up-arrow me-2"></i>
					<?php echo esc_html__( 'Analytics', 'wp2-download' ); ?></span>
				<select class="form-select form-select-sm" id="analytics-service-select"
					name="wp2_download_analytics_adapter" style="width: auto;">
					<?php foreach ( $analytics_adapters as $adapter ) : ?>
						<option value="<?php echo esc_attr( $adapter ); ?>" <?php selected( $selected_analytics, $adapter ); ?>>
							<?php echo esc_html( $adapter ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="card-body">
				<div id="analytics-service-none-content">
					<p class="small text-muted">Configure your analytics adapter. Required:
						<code>WP2_POSTHOG_KEY</code>.<br>
						<?php echo defined( 'WP2_POSTHOG_KEY' ) ? esc_html( 'WP2_POSTHOG_KEY set.' ) : ''; ?>
					</p>
					<button type="button" class="button button-secondary" id="test-analytics-connection">Test
						Connection</button>
					<div id="analytics-connection-status"></div>
				</div>
			</div>
		</div>

		<p>
			<button type="submit"
				class="button button-primary"><?php echo esc_html__( 'Save Changes', 'wp2-download' ); ?></button>
		</p>

	</div>
</form>
