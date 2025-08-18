<?php
namespace WP2\Download\Helpers;

use WP_List_Table;

class RenderTable {
	/**
	 * Render a WP_List_Table instance with standard form markup.
	 *
	 * @param WP_List_Table $table
	 * @param string $title
	 * @param string $page_slug
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
