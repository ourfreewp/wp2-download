<?php
/**
 * Origins page wrapper and tab navigation.
 *
 * Variables provided by controller:
 * @var array  $tabs         Map of tab_key => [ 'label' => string, 'file' => string ].
 * @var string $current_tab  Current tab slug.
 * @var callable $build_tab_url function( string $tab ): string
 * @var callable $render_tab    function( string $tab ): void
 */

defined( 'ABSPATH' ) || exit();
?>

<div class="wrap wp2-origins">
	<h1><?php echo esc_html__( 'WP2 Origins', 'wp2-download' ); ?></h1>
	<p class="description">
		<?php echo esc_html__( '', 'wp2-download' ); ?>
	</p>

	<?php if ( ! empty( $tabs ) ) : ?>
		<h2 class="nav-tab-wrapper" role="tablist">
			<?php foreach ( $tabs as $tab_key => $tab ) : ?>
				<?php
				$active = $current_tab === $tab_key ? ' nav-tab-active' : '';
				$tab_href = $build_tab_url( $tab_key );
				?>
				<a class="nav-tab<?php echo esc_attr( $active ); ?>" id="<?php echo esc_attr( 'tab-' . $tab_key ); ?>"
					href="<?php echo $tab_href; ?>" role="tab"
					aria-selected="<?php echo esc_attr( $current_tab === $tab_key ? 'true' : 'false' ); ?>">
					<?php echo esc_html( $tab['label'] ); ?>
				</a>
			<?php endforeach; ?>
		</h2>

		<div class="wp2-settings__tab-panel" role="tabpanel"
			aria-labelledby="<?php echo esc_attr( 'tab-' . $current_tab ); ?>">
			<?php $render_tab( $current_tab ); ?>
		</div>
	<?php else : ?>
		<div class="notice notice-warning">
			<p><?php echo esc_html__( 'No audit tabs found.', 'wp2-download' ); ?></p>
		</div>
	<?php endif; ?>
</div>