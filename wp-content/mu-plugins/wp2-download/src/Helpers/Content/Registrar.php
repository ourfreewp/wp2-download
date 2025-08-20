<?php
namespace WP2\Download\Helpers\Content;

/**
 * @component_id helpers_registrar
 * @namespace helpers
 * @type Utility
 * @note "Standardized labels for custom post types."
 */
class Registrar {
	/**
	 * Returns standardized labels for custom post types.
	 *
	 * @param string $singular Singular name.
	 * @param string $plural Plural name.
	 * @return array
	 */
	public static function get_labels( string $singular, string $plural ): array {
		return array(
			'name'                  => $plural,
			'singular_name'         => $singular,
			'add_new'               => 'Add New',
			'add_new_item'          => "Add New $singular",
			'edit_item'             => "Edit $singular",
			'new_item'              => "New $singular",
			'view_item'             => "View $singular",
			'search_items'          => "Search $plural",
			'not_found'             => "No $plural found",
			'not_found_in_trash'    => "No $plural found in Trash",
			'parent_item_colon'     => "Parent $singular:",
			'all_items'             => "All $plural",
			'archives'              => "$singular Archives",
			'insert_into_item'      => "Insert into $singular",
			'uploaded_to_this_item' => "Uploaded to this $singular",
			'featured_image'        => 'Featured image',
			'set_featured_image'    => 'Set featured image',
			'remove_featured_image' => 'Remove featured image',
			'use_featured_image'    => 'Use as featured image',
			'menu_name'             => $plural,
			'filter_items_list'     => "Filter $plural list",
			'items_list_navigation' => "$plural list navigation",
			'items_list'            => "$plural list",
		);
	}
}
