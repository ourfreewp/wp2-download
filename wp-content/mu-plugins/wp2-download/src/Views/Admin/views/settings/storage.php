<?php
// wp-content/mu-plugins/wp2-download/src/Admin/views/settings/storage.php
defined( 'ABSPATH' ) || exit();
use WP2\Download\Services\Locator;

$storage_adapters = Locator::list_storage_adapters();
$selected_storage = (string) get_option( 'wp2_download_storage_adapter', 'DefaultAdapter' );
?>

<form method="post" action="options.php">
	<?php settings_fields( 'wp2_download_settings' ); ?>
	<div class="card mb-4">
		<div class="card-header d-flex justify-content-between align-items-center">
			<span><i class="bi bi-cloud-fill me-2"></i> <?php echo esc_html__( 'Storage', 'wp2-download' ); ?></span>
			<select class="form-select form-select-sm" id="storage-service-select" name="wp2_download_storage_adapter"
				style="width: auto;">
				<?php if ( empty( $storage_adapters ) ) : ?>
					<option disabled><?php esc_html_e( 'No adapters available', 'wp2-download' ); ?></option>
				<?php else : ?>
					<?php foreach ( $storage_adapters as $adapter ) : ?>
						<option value="<?php echo esc_attr( $adapter ); ?>" <?php selected( $selected_storage, $adapter ); ?>>
							<?php echo esc_html( $adapter ); ?>
						</option>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
		</div>
		<div class="card-body">
			<p class="small text-muted">Configure your storage adapter. Required: <code>WP2_R2_ACCOUNT_ID</code>,
				<code>WP2_R2_ACCESS_KEY</code>, <code>WP2_R2_SECRET_KEY</code>, <code>WP2_R2_BUCKET_NAME</code>.<br>
				<?php echo defined( 'WP2_R2_ACCOUNT_ID' ) ? esc_html( 'WP2_R2_ACCOUNT_ID set.' ) : esc_html( 'WP2_R2_ACCOUNT_ID not set.' ); ?>
				<?php echo defined( 'WP2_R2_ACCESS_KEY' ) ? esc_html( 'WP2_R2_ACCESS_KEY set.' ) : esc_html( 'WP2_R2_ACCESS_KEY not set.' ); ?>
				<?php echo defined( 'WP2_R2_SECRET_KEY' ) ? esc_html( 'WP2_R2_SECRET_KEY set.' ) : esc_html( 'WP2_R2_SECRET_KEY not set.' ); ?>
				<?php echo defined( 'WP2_R2_BUCKET_NAME' ) ? esc_html( 'WP2_R2_BUCKET_NAME set.' ) : esc_html( 'WP2_R2_BUCKET_NAME not set.' ); ?>
			</p>
			<div class="mb-3">
				<label for="r2AccountId" class="form-label">R2 Account ID</label>
				<input type="text" class="form-control" id="r2AccountId" name="wp2_r2_account_id"
					value="<?php echo esc_attr( get_option( 'wp2_r2_account_id', '' ) ); ?>">
			</div>
			<div class="mb-3">
				<label for="r2AccessKey" class="form-label">R2 Access Key</label>
				<input type="text" class="form-control" id="r2AccessKey" name="wp2_r2_access_key"
					value="<?php echo esc_attr( get_option( 'wp2_r2_access_key', '' ) ); ?>">
			</div>
			<div class="mb-3">
				<label for="r2SecretKey" class="form-label">R2 Secret Key</label>
				<input type="text" class="form-control" id="r2SecretKey" name="wp2_r2_secret_key"
					value="<?php echo esc_attr( get_option( 'wp2_r2_secret_key', '' ) ); ?>">
			</div>
			<div class="mb-3">
				<label for="r2BucketName" class="form-label">R2 Bucket Name</label>
				<input type="text" class="form-control" id="r2BucketName" name="wp2_r2_bucket_name"
					value="<?php echo esc_attr( get_option( 'wp2_r2_bucket_name', '' ) ); ?>">
			</div>
			<button type="button" class="button button-secondary" id="test-storage-connection">Test Connection</button>
			<div id="storage-connection-status"></div>
			<button type="submit" class="button button-primary">Save Changes</button>
		</div>
	</div>
</form>
