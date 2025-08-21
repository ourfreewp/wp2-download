<?php
/**
 * Development settings view.
 *
 * @package WP2 Download
 */

defined( 'ABSPATH' ) || exit();
use WP2\Download\Services\Locator;

$dev_adapters = Locator::list_development_adapters();
$selected_development = (string) get_option( 'wp2_download_development_adapter', 'DefaultAdapter' );
?>

<form method="post" action="options.php">
	<?php settings_fields( 'wp2_download_settings' ); ?>
	<div class="card mb-4">
		<div class="card-header d-flex justify-content-between align-items-center">
			<span><i class="bi bi-gear-fill me-2"></i> <?php echo esc_html__( 'Development', 'wp2-download' ); ?></span>
			<select class="form-select form-select-sm" id="dev-service-select" name="wp2_download_development_adapter" style="width: auto;">
				<?php if ( empty( $dev_adapters ) ) : ?>
					<option disabled><?php esc_html_e( 'No adapters available', 'wp2-download' ); ?></option>
				<?php else : ?>
					<?php foreach ( $dev_adapters as $adapter ) : ?>
						<option value="<?php echo esc_attr( $adapter ); ?>" <?php selected( $selected_development, $adapter ); ?>><?php echo esc_html( $adapter ); ?></option>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
		</div>
		<div class="card-body">
			<p class="small text-muted">Configure your development adapter. Required: <code>WP2_GITHUB_PAT</code> (Personal Access Token).<br>
				<?php echo defined( 'WP2_GITHUB_PAT' ) ? esc_html( 'WP2_GITHUB_PAT set.' ) : esc_html( 'WP2_GITHUB_PAT not set.' ); ?>
			</p>
			<div class="mb-3">
				<label for="githubPat" class="form-label">GitHub Personal Access Token (PAT)</label>
				<input type="text" class="form-control" id="githubPat" name="wp2_github_pat" value="<?php echo esc_attr( get_option( 'wp2_github_pat', '' ) ); ?>">
			</div>
			<div class="mb-3">
				<label for="githubOrg" class="form-label">GitHub Organization</label>
				<input type="text" class="form-control" id="githubOrg" name="wp2_github_org" value="<?php echo esc_attr( get_option( 'wp2_github_org', '' ) ); ?>">
			</div>
			<button type="button" class="button button-secondary" id="test-dev-connection">Test Connection</button>
			<div id="dev-connection-status"></div>
			<button type="submit" class="button button-primary">Save Changes</button>
		</div>
	</div>
</form>
