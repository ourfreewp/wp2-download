<?php
namespace WP2\Download\Core\Packages;

/**
 * @component_id admin_manifests
 * @namespace admin
 * @type Service
 * @note "Handles manifest ingestion and debug notices for WP2 Hub."
 */
class Manifests {

	private const PROCESSED_MANIFESTS_OPTION = 'wp2_processed_manifests';
	private const TYPE_MAP                   = array(
		'mu-plugins' => 'mu',
		'plugins'    => 'plugin',
		'themes'     => 'theme',
	);
	private static $debug_messages           = array();

	public function register_hooks(): void {
		add_action( 'admin_init', array( $this, 'ingest_manifests' ) );
	}

	/**
	 * Purge the processed manifests option so manifests can be refetched.
	 */
	public static function purge_processed_manifests(): void {
		delete_option( self::PROCESSED_MANIFESTS_OPTION );
		self::$debug_messages[] = 'Processed manifests option purged.';
	}

	public function display_debug_notices() {
		if ( ! current_user_can( 'manage_options' ) || empty( self::$debug_messages ) ) {
			return;
		}
		echo '<div class="notice notice-warning" style="padding: 10px;">';
		echo '<h3 style="margin-top: 0;">WP2 Download Debug</h3><ul style="margin: 0; list-style: disc; padding-left: 20px;">';
		foreach ( self::$debug_messages as $message ) {
			echo '<li>' . esc_html( $message ) . '</li>';
		}
		echo '</ul></div>';
	}

	public function ingest_manifests(): void {
		self::$debug_messages[] = 'Starting manifest ingestion...';

		$base_dir               = WPMU_PLUGIN_DIR . '/wp2-download/data/packages';
		self::$debug_messages[] = "Checking base directory: {$base_dir}";

		if ( ! is_dir( $base_dir ) ) {
			self::$debug_messages[] = 'Error: Base directory not found. Halting.';
			return;
		}
		self::$debug_messages[] = 'Success: Base directory found.';

		$processed = get_option( self::PROCESSED_MANIFESTS_OPTION, array() );

		foreach ( self::TYPE_MAP as $dir_name => $type ) {
			$type_dir               = "{$base_dir}/{$dir_name}";
			self::$debug_messages[] = "Checking for type directory: {$type_dir}";

			if ( ! is_dir( $type_dir ) ) {
				continue;
			}
			self::$debug_messages[] = "Found type directory for '{$type}'.";

			foreach ( new \DirectoryIterator( $type_dir ) as $package_dir ) {
				if ( $package_dir->isDot() || ! $package_dir->isDir() ) {
					continue;
				}

				$slug                   = $package_dir->getFilename();
				$manifest_path          = $package_dir->getRealPath() . '/manifest.json';
				self::$debug_messages[] = "Found package directory: '{$slug}'. Checking for manifest...";

				if ( file_exists( $manifest_path ) ) {
					$hash = md5_file( $manifest_path );

					if ( isset( $processed[ $manifest_path ] ) && $processed[ $manifest_path ] === $hash ) {
						self::$debug_messages[] = "Skipping '{$slug}': Manifest is unchanged.";
						continue;
					}

					self::$debug_messages[] = "Processing manifest for '{$slug}'...";
					$this->process_manifest( $manifest_path, $type, $slug );
					$processed[ $manifest_path ] = $hash;
				}
			}
		}

		update_option( self::PROCESSED_MANIFESTS_OPTION, $processed );
	}

	private function process_manifest( string $path, string $type, string $slug ): void {
		$content = file_get_contents( $path );
		$data    = json_decode( $content, true );
		if ( ! is_array( $data ) ) {
			return;
		}

		$name          = sanitize_text_field( $data['name'] ?? $slug );
		$post_type     = "wp2_{$type}";
		$existing_post = get_page_by_path( $slug, OBJECT, $post_type );

		$post_data = array(
			'post_type'   => $post_type,
			'post_title'  => $name,
			'post_name'   => $slug,
			'post_status' => 'publish',
		);

		if ( $existing_post ) {
			$post_data['ID'] = $existing_post->ID;
			$post_id         = wp_update_post( $post_data );
		} else {
			$post_id = wp_insert_post( $post_data );
		}

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			return;
		}

		$meta_fields = array( 'description', 'author', 'links', 'requires_php', 'requires_wp', 'tags' );
		foreach ( $meta_fields as $field ) {
			if ( isset( $data[ $field ] ) ) {
				update_post_meta( $post_id, "wp2_{$field}", $data[ $field ] );
			} else {
				delete_post_meta( $post_id, "wp2_{$field}" );
			}
		}
	}
}
