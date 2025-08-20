<?php

// wp-content/mu-plugins/wp2-download/src/Services/Init.php
namespace WP2\Download\Services;

defined( 'ABSPATH' ) || exit();

/**
 * @component_id services_init
 * @namespace services
 * @type Bootstrap
 * @note "Registers settings and helpers for bootstrap."
 */
class Init {

	/**
	 * Register wp2_download_settings and related fields.
	 *
	 * @return void
	 */
	public static function init(): void {
		add_action(
			'admin_init',
			static function (): void {
				register_setting(
					'wp2_download_settings',
					'wp2_download_storage_adapter',
					array(
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					)
				);
				register_setting(
					'wp2_download_settings',
					'wp2_download_development_adapter',
					array(
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					)
				);
				register_setting(
					'wp2_download_settings',
					'wp2_download_licensing_adapter',
					array(
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					)
				);
				register_setting(
					'wp2_download_settings',
					'wp2_download_analytics_adapter',
					array(
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					)
				);

				// Storage tab options.
				register_setting(
					'wp2_download_settings',
					'wp2_r2_account_id',
					array(
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					)
				);
				register_setting(
					'wp2_download_settings',
					'wp2_r2_access_key',
					array(
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					)
				);
				register_setting(
					'wp2_download_settings',
					'wp2_r2_secret_key',
					array(
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					)
				);
				register_setting(
					'wp2_download_settings',
					'wp2_r2_bucket_name',
					array(
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					)
				);

				// Development tab options.
				register_setting(
					'wp2_download_settings',
					'wp2_github_pat',
					array(
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					)
				);
				register_setting(
					'wp2_download_settings',
					'wp2_github_org',
					array(
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					)
				);

				// Licensing tab options.
				register_setting(
					'wp2_download_settings',
					'wp2_keygen_account_id',
					array(
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					)
				);
				register_setting(
					'wp2_download_settings',
					'wp2_keygen_product_token',
					array(
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					)
				);

				// Analytics tab options.
				register_setting(
					'wp2_download_settings',
					'wp2_posthog_api_key',
					array(
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					)
				);
				register_setting(
					'wp2_download_settings',
					'wp2_posthog_api_url',
					array(
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					)
				);

				// (Reserved) Origin-related initialization could be added here in the future.
			}
		);
	}

	/**
	 * Get origin snapshot info from a package manifest.
	 *
	 * Accepts a normalized manifest array that includes:
	 *   - origin.kind        (string)
	 *   - origin.source_ref  (array)
	 *
	 * Returns a light-weight snapshot suitable for UI or logs:
	 *   [
	 *     'ok'                  => bool,
	 *     'kind'                => string,
	 *     'label'               => string,
	 *     'supports_mirror'     => bool,
	 *     'default_update_mode' => string,
	 *     'metadata'            => array,
	 *     'versions'            => array,
	 *     'error'               => string (when ok = false)
	 *   ]
	 *
	 * @param array $manifest Package manifest data.
	 * @return array
	 */
	public static function get_origin_snapshot( array $manifest ): array {
		$kind       = isset( $manifest['origin']['kind'] ) ? strtolower( (string) $manifest['origin']['kind'] ) : '';
		$source_ref = isset( $manifest['origin']['source_ref'] ) && is_array( $manifest['origin']['source_ref'] )
			? $manifest['origin']['source_ref']
			: array();

		if ( $kind === '' ) {
			return array(
				'ok'    => false,
				'error' => 'Missing origin.kind',
			);
		}

		// Resolve adapter via Service Locator.
		try {
			$adapter = \WP2\Download\Services\Locator::origin( $kind );
		} catch ( \Throwable $e ) {
			$adapter = null;
		}

		if ( ! $adapter ) {
			return array(
				'ok'    => false,
				'kind'  => $kind,
				'error' => 'Unsupported origin kind',
			);
		}

		if ( empty( $source_ref ) ) {
			return array(
				'ok'    => false,
				'kind'  => $kind,
				'error' => 'Missing origin.source_ref',
			);
		}

		// Validate the source_ref if the adapter exposes a validator.
		if ( method_exists( $adapter, 'validate_source_ref' ) && ! $adapter->validate_source_ref( $source_ref ) ) {
			return array(
				'ok'    => false,
				'kind'  => $kind,
				'error' => 'Invalid origin.source_ref',
			);
		}

		$label = method_exists( $adapter, 'get_label' )
			? (string) $adapter->get_label()
			: ucfirst( $kind );

		$supports_mirror = method_exists( $adapter, 'supports_mirror' )
			? (bool) $adapter->supports_mirror( $source_ref )
			: false;

		$default_update_mode = method_exists( $adapter, 'default_update_mode' )
			? (string) $adapter->default_update_mode( $source_ref )
			: '';

		$metadata = method_exists( $adapter, 'fetch_metadata' )
			? (array) $adapter->fetch_metadata( $source_ref )
			: array();

		$versions = method_exists( $adapter, 'fetch_versions' )
			? (array) $adapter->fetch_versions( $source_ref, array() )
			: array();

		return array(
			'ok'                  => true,
			'kind'                => $kind,
			'label'               => $label,
			'supports_mirror'     => $supports_mirror,
			'default_update_mode' => $default_update_mode,
			'metadata'            => $metadata,
			'versions'            => $versions,
		);
	}
}
