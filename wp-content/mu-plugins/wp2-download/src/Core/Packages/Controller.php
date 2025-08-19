<?php
namespace WP2\Download\Views\Admin\Data\Packages;

/**
 * @component_id api_packages_controller
 * @namespace api.packages
 * @type Controller
 * @note "Handles package data retrieval for dashboard view."
 */
class Controller {

	/**
	 * Retrieves all package data for the dashboard view.
	 *
	 * @return array An array of package data.
	 */
	public function get_all_packages_data(): array {
		$packages_data = [];
		// Strictly manage allowed post types
		$package_types = [ 'plugin', 'mu', 'theme' ];

		foreach ( $package_types as $type ) {
			$query = new \WP_Query( [ 'post_type' => "wp2_{$type}", 'posts_per_page' => -1 ] );

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					$post_id = get_the_ID();
					$slug = get_post_field( 'post_name', $post_id );

					// --- Package Array ---
					$packages_data[] = [ 
						'id' => $post_id ?? '',
						'name' => get_the_title() ?? '',
						'type' => $type ?? '',
						'slug' => $slug ?? '',
					];
				}
			}
		}

		\wp_reset_postdata();
		return $packages_data;
	}

	/**
	 * Get all releases for a package (version, date, R2 status)
	 */
	public function get_package_releases( int $package_post_id, string $type ): array {
		$releases = [];
		$release_query = new \WP_Query( [ 
			'post_type' => "wp2_{$type}_rel",
			'post_parent' => $package_post_id,
			'posts_per_page' => -1,
			'orderby' => 'date',
			'order' => 'DESC',
		] );
		if ( $release_query->have_posts() ) {
			foreach ( $release_query->posts as $release_post ) {
				$version = get_post_meta( $release_post->ID, 'wp2_version', true );
				$date = get_the_date( 'Y-m-d', $release_post->ID );
				$is_present = get_post_meta( $release_post->ID, 'wp2_r2_present', true );
				$releases[] = [ 
					'version' => $version,
					'date' => $date,
					'is_present' => (bool) $is_present,
				];
			}
		}
		\wp_reset_postdata();
		return $releases;
	}

	/**
	 * Gets the latest version for a given package.
	 *
	 * @param int $package_post_id The post ID of the package.
	 * @param string $type The package type (plugin, mu, theme).
	 * @return string|null The latest version string or null if not found.
	 */
	public function get_latest_package_version( int $package_post_id, string $type ): ?string {
		$release_query = new \WP_Query( [ 
			'post_type' => "wp2_{$type}_rel",
			'post_parent' => $package_post_id,
			'posts_per_page' => 1,
			'orderby' => 'date',
			'order' => 'DESC',
			'meta_key' => 'wp2_version',
			'meta_type' => 'STRING',
		] );

		if ( $release_query->have_posts() ) {
			return get_post_meta( $release_query->posts[0]->ID, 'wp2_version', true );
		}

		return null;
	}


	public static function get_repo_data( array $package ): array {
		return [];
	}

	public static function get_storage_data( array $package ): array {
		return [];
	}

	public static function get_health_data( array $package ): array {
		return [];
	}


	public static function get_licensing_data( array $package ): array {
		return [];
	}

	public static function get_analytics_data( array $package ): array {
		return [];
	}

}