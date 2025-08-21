<?php

/**
 * MU Plugins admin view.
 *
 * @package WP2_Download
 */

defined( 'ABSPATH' ) || exit();

// Table HTML should be passed in from the controller.
echo isset( $table_html ) ? esc_html( $table_html ) : '';
