<?php
/**
 * Table rendering helper.
 *
 * @package WP2_Download
 */

namespace WP2\Download\Helpers\Table;

use WP_List_Table;

/**
 * Table rendering helper.
 *
 * @component_id helpers_render_table
 * @namespace helpers
 * @type Utility
 * @note "Renders WP_List_Table instances with standard markup."
 */
class RenderTable {

	/**
	 * Render a WP_List_Table instance with standard form markup.
	 *
	 * @param WP_List_Table $table
	 * @param string        $title
	 * @param string        $page_slug
	 */
	public static function render( WP_List_Table $table, string $title, string $page_slug ) {
		?>
		<div class="wrap">
			<h1><?php echo esc_html( $title ); ?></h1>
			<form method="get">
				<input type="hidden" name="page" value="<?php echo esc_attr( $page_slug ); ?>" />
				<?php $table->display(); ?>
			</form>
		</div>
		<?php
	}
}
