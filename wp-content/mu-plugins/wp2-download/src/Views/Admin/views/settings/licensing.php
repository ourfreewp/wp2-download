<?php
// wp-content/mu-plugins/wp2-download/src/Admin/views/settings/licensing.php
defined( 'ABSPATH' ) || exit();
use WP2\Download\Services\Locator;

$licensing_adapters = Locator::list_licensing_adapters();
$selected_licensing = (string) get_option( 'wp2_download_licensing_adapter', 'DefaultAdapter' );
?>

<form method="post" action="options.php">
	<?php settings_fields( 'wp2_download_settings' ); ?>
	<div class="card mb-4">
		<div class="card-header d-flex justify-content-between align-items-center">
			<span><i class="bi bi-key-fill me-2"></i> <?php echo esc_html__( 'Licensing', 'wp2-download' ); ?></span>
			<select class="form-select form-select-sm" id="licensing-service-select"
				name="wp2_download_licensing_adapter" style="width: auto;">
				<?php if ( empty( $licensing_adapters ) ) : ?>
					<option disabled><?php esc_html_e( 'No adapters available', 'wp2-download' ); ?></option>
				<?php else : ?>
					<?php foreach ( $licensing_adapters as $adapter ) : ?>
						<option value="<?php echo esc_attr( $adapter ); ?>" <?php selected( $selected_licensing, $adapter ); ?>>
							<?php echo esc_html( $adapter ); ?></option>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
		</div>
		<div class="card-body">
			<p class="small text-muted">Configure your licensing adapter. Required: <code>WP2_KEYGEN_ACCOUNT</code>,
				<code>WP2_KEYGEN_TOKEN</code>.<br>
				<?php echo defined( 'WP2_KEYGEN_ACCOUNT' ) ? esc_html( 'WP2_KEYGEN_ACCOUNT set.' ) : esc_html( 'WP2_KEYGEN_ACCOUNT not set.' ); ?>
				<?php echo defined( 'WP2_KEYGEN_TOKEN' ) ? esc_html( 'WP2_KEYGEN_TOKEN set.' ) : esc_html( 'WP2_KEYGEN_TOKEN not set.' ); ?>
			</p>
			<div class="mb-3">
				<label for="keygenAccountId" class="form-label">Keygen Account ID</label>
				<input type="text" class="form-control" id="keygenAccountId" name="wp2_keygen_account_id"
					value="<?php echo esc_attr( get_option( 'wp2_keygen_account_id', '' ) ); ?>"
					placeholder="e.g., a1b2c3d4-e5f6-7890-abcd-ef0123456789">
			</div>
			<div class="mb-3">
				<label for="keygenProductToken" class="form-label">Keygen Product Token</label>
				<input type="text" class="form-control" id="keygenProductToken" name="wp2_keygen_product_token"
					value="<?php echo esc_attr( get_option( 'wp2_keygen_product_token', '' ) ); ?>"
					placeholder="e.g., prod-********************">
			</div>
			<button type="button" class="button button-secondary" id="test-licensing-connection">Test
				Connection</button>
			<div id="licensing-connection-status"></div>
			<button type="submit" class="button button-primary">Save Changes</button>
		</div>
	</div>
</form>
