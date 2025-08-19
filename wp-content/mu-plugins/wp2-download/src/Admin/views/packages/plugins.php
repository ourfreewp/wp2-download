<?php
// wp-content/mu-plugins/wp2-download/src/Admin/views/packages/plugins.php
defined( 'ABSPATH' ) || exit();

use WP2\Download\Config as Config;

class Plugins extends \WP2\Download\Admin\Helpers\PrepareTable {
	public function __construct() {
		parent::__construct( [ 
			'singular' => 'plugin',
			'plural' => 'plugins',
			'ajax' => false,
		] );
	}

	public function get_columns() {
		return [ 
			'cb' => '<input type="checkbox" />',
			'title' => \__( 'Title', 'wp2-download' ),
			'author' => \__( 'Author', 'wp2-download' ),
			'date' => \__( 'Date', 'wp2-download' ),
		];
	}

	protected function get_post_type() {
		return Config::WP2_POST_TYPE_PLUGIN;
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

$table = new Plugins();
use WP2\Download\Helpers\RenderTable;
RenderTable::render( $table, \__( 'Plugins', 'wp2-download' ), 'wp2-hub-plugins' );