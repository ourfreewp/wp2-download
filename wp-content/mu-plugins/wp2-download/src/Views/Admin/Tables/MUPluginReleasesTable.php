<?php
namespace WP2\Download\Views\Admin\Tables;

use WP2\Download\Config;
use WP2\Download\Helpers\PrepareTable;

class MUPluginReleasesTable extends PrepareTable {
	public function __construct() {
		parent::__construct(
			array(
				'singular' => 'mu-plugin-release',
				'plural'   => 'mu-plugin-releases',
				'ajax'     => false,
			)
		);
	}

	public function get_columns() {
		return array(
			'cb'     => '<input type="checkbox" />',
			'title'  => __( 'Title', 'wp2-download' ),
			'author' => __( 'Author', 'wp2-download' ),
			'date'   => __( 'Date', 'wp2-download' ),
		);
	}

	protected function get_post_type() {
		return Config::WP2_POST_TYPE_MU_REL;
	}

	protected function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="post[]" value="%s" />', $item['ID'] );
	}

	protected function column_title( $item ) {
		$edit_link = get_edit_post_link( $item['ID'] );
		return sprintf( '<a href="%s">%s</a>', esc_url( $edit_link ), esc_html( $item['title'] ) );
	}

	protected function column_author( $item ) {
		return esc_html( $item['author'] );
	}

	protected function column_date( $item ) {
		return esc_html( $item['date'] );
	}

	protected function column_default( $item, $column_name ) {
		return isset( $item[ $column_name ] ) ? $item[ $column_name ] : '';
	}
}
