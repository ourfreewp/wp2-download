<?php

/**
 * Themes table view.
 *
 * @var string $table_html HTML for the themes table.
 */

defined( 'ABSPATH' ) || exit();

// Table HTML should be passed in from the controller.
echo isset( $table_html ) ? wp_kses_post( $table_html ) : '';
