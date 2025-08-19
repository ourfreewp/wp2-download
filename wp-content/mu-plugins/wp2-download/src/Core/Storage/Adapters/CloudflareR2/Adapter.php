<?php
// wp-content/mu-plugins/wp2-download/src/Storage/Adapters/CloudflareR2Adapter.php
namespace WP2\Download\Storage\Adapters\CloudflareR2;

defined( 'ABSPATH' ) || exit();

use WP2\Download\Storage\ConnectionInterface;

/**
 * @component_id storage_cloudflarer2_adapter
 * @namespace storage.adapters
 * @type Adapter
 * @note "Cloudflare R2 adapter with S3-compatible endpoint awareness."
 */
class Adapter implements ConnectionInterface {

	/**
	 * Cloudflare account ID.
	 *
	 * @var string
	 */
	protected $account_id = '';

	/**
	 * Bucket name.
	 *
	 * @var string
	 */
	protected $bucket = '';

	/**
	 * Access key ID for API authentication.
	 *
	 * @var string
	 */
	protected $access_key_id = '';

	/**
	 * Secret access key for API authentication.
	 *
	 * @var string
	 */
	protected $secret_access_key = '';

	/**
	 * Custom endpoint (optional). Defaults to derived R2 endpoint.
	 *
	 * @var string
	 */
	protected $endpoint = '';

	/**
	 * Whether the bucket is public-only (no signed operations required).
	 *
	 * @var bool
	 */
	protected $use_public_bucket = false;

	/**
	 * Constructor.
	 *
	 * Accepts configuration keys:
	 * - account_id (string)
	 * - bucket (string)
	 * - access_key_id (string)
	 * - secret_access_key (string)
	 * - endpoint (string) optional; defaults to https://{account_id}.r2.cloudflarestorage.com
	 * - public (bool) optional; true allows connect() without credentials.
	 *
	 * @param array<string,mixed> $config Adapter configuration.
	 */
	public function __construct( array $config = [] ) {
		// Precedence: constant → env → config
		$this->account_id = defined( 'WP2_R2_ACCOUNT_ID' ) ? WP2_R2_ACCOUNT_ID : ( getenv( 'WP2_R2_ACCOUNT_ID' ) ?: ( $config['account_id'] ?? '' ) );
		$this->bucket = defined( 'WP2_R2_BUCKET' ) ? WP2_R2_BUCKET : ( getenv( 'WP2_R2_BUCKET' ) ?: ( $config['bucket'] ?? '' ) );
		$this->access_key_id = defined( 'WP2_R2_ACCESS_KEY_ID' ) ? WP2_R2_ACCESS_KEY_ID : ( getenv( 'WP2_R2_ACCESS_KEY_ID' ) ?: ( $config['access_key_id'] ?? '' ) );
		$this->secret_access_key = defined( 'WP2_R2_SECRET_ACCESS_KEY' ) ? WP2_R2_SECRET_ACCESS_KEY : ( getenv( 'WP2_R2_SECRET_ACCESS_KEY' ) ?: ( $config['secret_access_key'] ?? '' ) );
		$this->endpoint = isset( $config['endpoint'] ) ? (string) $config['endpoint'] : $this->build_endpoint();
		$this->use_public_bucket = (bool) ( $config['public'] ?? false );
	}

	/**
	 * {@inheritdoc}
	 */
	public function connect(): bool {
		if ( $this->account_id === '' || $this->bucket === '' ) {
			return false;
		}

		// Allow public-only buckets to "connect" without credentials.
		if ( ! $this->use_public_bucket && ( $this->access_key_id === '' || $this->secret_access_key === '' ) ) {
			return false;
		}

		// Basic structural validation passed.
		return true;
	}

	/**
	 * Build the default R2 endpoint from the account ID.
	 *
	 * @return string
	 */
	protected function build_endpoint(): string {
		if ( $this->account_id === '' ) {
			return '';
		}
		return 'https://' . $this->account_id . '.r2.cloudflarestorage.com';
	}

	/**
	 * Get the base URL for the configured bucket.
	 *
	 * @return string
	 */
	public function get_base_url(): string {
		if ( $this->endpoint === '' || $this->bucket === '' ) {
			return '';
		}
		$base = untrailingslashit( $this->endpoint );
		return $base . '/' . rawurlencode( $this->bucket );
	}

	/**
	 * Build a public object URL for a given key (no signing).
	 *
	 * @param string $key Object key within the bucket.
	 * @return string
	 */
	public function object_url( string $key ): string {
		$base = $this->get_base_url();
		if ( $base === '' ) {
			return '';
		}
		$sanitized = ltrim( $key, '/' );
		// Preserve path separators while encoding.
		return $base . '/' . str_replace( '%2F', '/', rawurlencode( $sanitized ) );
	}

	/**
	 * Returns the last error encountered by the adapter.
	 *
	 * @return string|null
	 */
	public function get_last_error(): ?string {
		// Implement error tracking if needed
		return null;
	}
}