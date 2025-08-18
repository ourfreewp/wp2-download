<?php
namespace WP2\Download\Content\Packages;

class Registrar {
	/**
	 * Get labels for a given package type and context (main or release).
	 *
	 * @param string $type Package type (plugin, mu, theme)
	 * @param bool $isRelease Whether labels are for release post type
	 * @return array
	 */
	public static function get_labels( $type, $isRelease = false, $singular = null, $plural = null, $name = null ) {
		$base = ucwords( str_replace( '-', ' ', $type ) );
		$singular = $singular ?: $base;
		$plural = $plural ?: $base . 's';
		$main_name = $name ?: "WP2 {$plural}";
		if ( $isRelease ) {
			$release_singular = $singular . ' Release';
			$release_plural = $plural . ' Releases';
			$release_name = "WP2 {$release_plural}";
			return [ 
				'name' => $release_name,
				'singular_name' => $release_singular,
				'add_new' => 'Add New',
				'add_new_item' => "Add New {$release_singular}",
				'edit_item' => "Edit {$release_singular}",
				'new_item' => "New {$release_singular}",
				'view_item' => "View {$release_singular}",
				'view_items' => "View {$release_plural}",
				'search_items' => "Search {$release_plural}",
				'not_found' => 'No releases found.',
				'not_found_in_trash' => 'No releases found in Trash.',
				'parent_item_colon' => '',
				'all_items' => "All {$release_plural}",
				'archives' => "{$release_singular} Archives",
				'attributes' => "{$release_singular} Attributes",
				'insert_into_item' => "Insert into {$release_singular}",
				'uploaded_to_this_item' => "Uploaded to this {$release_singular}",
				'featured_image' => "{$release_singular} Featured Image",
				'set_featured_image' => "Set featured image",
				'remove_featured_image' => "Remove featured image",
				'use_featured_image' => "Use as featured image",
				'menu_name' => $release_name,
			];
		}
		return [ 
			'name' => $main_name,
			'singular_name' => $singular,
			'add_new' => 'Add New',
			'add_new_item' => "Add New {$singular}",
			'edit_item' => "Edit {$singular}",
			'new_item' => "New {$singular}",
			'view_item' => "View {$singular}",
			'view_items' => "View {$plural}",
			'search_items' => "Search {$plural}",
			'not_found' => 'No packages found.',
			'not_found_in_trash' => 'No packages found in Trash.',
			'parent_item_colon' => '',
			'all_items' => "All {$plural}",
			'archives' => "{$singular} Archives",
			'attributes' => "{$singular} Attributes",
			'insert_into_item' => "Insert into {$singular}",
			'uploaded_to_this_item' => "Uploaded to this {$singular}",
			'featured_image' => "{$singular} Featured Image",
			'set_featured_image' => "Set featured image",
			'remove_featured_image' => "Remove featured image",
			'use_featured_image' => "Use as featured image",
			'menu_name' => $main_name,
		];
	}
	/**
	 * Register Custom Post Types for all package types.
	 */
	public static function register() {
		$types = [ 
			'plugin' => [ 'singular' => 'Plugin', 'plural' => 'Plugins', 'name' => 'WP2 Plugins' ],
			'mu' => [ 'singular' => 'Must-Use Plugin', 'plural' => 'Must-Use Plugins', 'name' => 'WP2 Must-Use Plugins' ],
			'theme' => [ 'singular' => 'Theme', 'plural' => 'Themes', 'name' => 'WP2 Themes' ],
		];

		$supports = [ 
			'title',
			'editor',
			'thumbnail',
			'excerpt',
			'custom-fields',
			'revisions',
			'comments',
			'author',
			'page-attributes'
		];

		foreach ( $types as $type => $labels ) {
			register_post_type( "wp2_{$type}", [ 
				'labels' => self::get_labels( $type, false, $labels['singular'], $labels['plural'], $labels['name'] ),
				'public' => false,
				'show_ui' => false,
				'supports' => $supports,
				'show_in_rest' => true,
			] );
			register_post_type( "wp2_{$type}_rel", [ 
				'labels' => self::get_labels( $type, true, $labels['singular'], $labels['plural'], $labels['name'] ),
				'public' => false,
				'show_ui' => false,
				'supports' => $supports,
				'show_in_rest' => true,
			] );
		}
	}
}
