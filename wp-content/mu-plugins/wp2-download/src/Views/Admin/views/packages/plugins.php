<?php
// wp-content/mu-plugins/wp2-download/src/Admin/views/packages/plugins.php
defined( 'ABSPATH' ) || exit();

// Table HTML should be passed in from the controller.
echo isset( $table_html ) ? esc_html( $table_html ) : '';
