<?php
// wp-content/mu-plugins/wp2-download/src/Admin/views/packages/themes.php
defined( 'ABSPATH' ) || exit();

// Table HTML should be passed in from the controller.
echo isset( $table_html ) ? wp_kses_post( $table_html ) : '';
