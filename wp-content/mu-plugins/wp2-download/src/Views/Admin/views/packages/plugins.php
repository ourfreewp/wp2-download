<?php

/**
 * Plugins table view.
 *
 * @var string $table_html HTML for the plugins table.
 */

defined( 'ABSPATH' ) || exit();

// Table HTML should be passed in from the controller.
echo isset( $table_html ) ? esc_html( $table_html ) : '';
