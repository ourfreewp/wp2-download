<?php

namespace WP2\Download\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * @component_id prepare_table
 * @namespace helpers
 * @type Utility
 * @note "Base class for all admin tables."
 */
abstract class PrepareTable extends \WP_List_Table {
	/**
	 * Items to display in the table.
	 *
	 * @var array
	 */
	public $items = [];
	/**
	 * Pass args to WP_List_Table constructor.
	 */
	public function __construct( $args = [] ) {
		parent::__construct( $args );
	}

	/**
	 * Prepare the items for display.
	 *
	 * @return void
	 */
	public function prepare_items(): void {
		// Default: fetch posts for a given post type
		$per_page = 20;
		$paged = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
		$args = $this->get_query_args( $per_page, $paged );
		$query = new \WP_Query( $args );

		$columns = $this->get_columns();
		$column_keys = array_keys( $columns );
		$hidden = [];
		$sortable = method_exists( $this, 'get_sortable_columns' ) ? (array) $this->get_sortable_columns() : [];
		$this->_column_headers = [ $columns, $hidden, $sortable ];

		$items = [];
		foreach ( $query->posts as $post ) {
			$item = [ 
				'ID' => $post->ID,
				'title' => $post->post_title,
				'author' => get_the_author_meta( 'display_name', $post->post_author ),
				'date' => get_the_date( '', $post->ID ),
			];
			// Ensure all expected columns are present
			foreach ( $column_keys as $col ) {
				if ( ! isset( $item[ $col ] ) ) {
					$item[ $col ] = '';
				}
			}
			$items[] = $item;
		}
		$this->items = $items;
		$this->set_pagination_args( [ 
			'total_items' => $query->found_posts,
			'per_page' => $per_page,
			'total_pages' => $query->max_num_pages,
		] );
	}

	/**
	 * Get WP_Query args for pagination. Child classes can override.
	 */
	protected function get_query_args( $per_page, $paged ) {
		return [ 
			'post_type' => $this->get_post_type(),
			'posts_per_page' => $per_page,
			'paged' => $paged,
			'post_status' => [ 'any' ],
		];
	}

	/**
	 * Get post type for query. Child classes must override.
	 */
	protected function get_post_type() {
		return '';
	}

	/**
	 * Default checkbox column.
	 */
	protected function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="post[]" value="%s" />', $item['ID']
		);
	}


	/**
	 * Default title column.
	 */
	protected function column_title( $item ) {
		$edit_link = get_edit_post_link( $item['ID'] );
		return sprintf( '<a href="%s">%s</a>', esc_url( $edit_link ), esc_html( $item['title'] ) );
	}


	/**
	 * Default author column.
	 */
	protected function column_author( $item ) {
		return esc_html( $item['author'] );
	}


	/**
	 * Default date column.
	 */
	protected function column_date( $item ) {
		return esc_html( $item['date'] );
	}

	/**
	 * Set pagination args for the table.
	 *
	 * @param array $args
	 * @return void
	 */
	public function set_pagination_args( $args ) {
		parent::set_pagination_args( $args );
	}

	/**
	 * Display the table.
	 *
	 * @return void
	 */
	public function display(): void {
		$this->prepare_items();
		parent::display();
	}
}
