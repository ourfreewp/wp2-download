<?php
namespace WP2\Download\Views\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * @component_id admin_accounts
 * @namespace admin
 * @type Page
 * @note "Accounts admin page with native tab navigation."
 */
class Accounts {

	/**
	 * Register hooks for the settings page.
	 *
	 * @return void
	 */
	public function register_hooks(): void {
		add_action( 'admin_menu', [ $this, 'add_submenu_page' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );

		add_filter( 'wp2_download_admin_accounts_tab_order', function ($order) {
			return [ 'system', 'development', 'storage', 'analytics', 'licensing' ];
		} );
	}

	/**
	 * Add the submenu page.
	 *
	 * @return void
	 */
	public function add_submenu_page(): void {
		add_submenu_page(
			'wp2-hub',
			__( 'WP2 Hub Accounts', 'wp2-download' ),
			__( 'Accounts', 'wp2-download' ),
			'manage_options',
			'wp2-hub-accounts',
			[ $this, 'render_page_contents' ]
		);
	}

	/**
	 * Enqueue assets for the accounts page.
	 *
	 * @param string $hook Current admin page hook.
	 * @return void
	 */
	public function enqueue_assets( string $hook ): void {
		if ( 'wp2-hub_page_wp2-hub-accounts' !== $hook && 'wp2-hub-accounts' !== $hook ) {
			return;
		}

		wp_enqueue_style(
			'wp2-hub-styles',
			esc_url( WP2_DOWNLOAD_URL . 'src/Admin/assets/styles/main.css' ),
			[],
			filemtime( WP2_DOWNLOAD_PATH . 'src/Admin/assets/styles/main.css' )
		);

		wp_enqueue_script(
			'wp2-hub-scripts',
			esc_url( WP2_DOWNLOAD_URL . 'src/Admin/assets/scripts/main.js' ),
			[ 'jquery' ],
			filemtime( WP2_DOWNLOAD_PATH . 'src/Admin/assets/scripts/main.js' ),
			true
		);
	}

	/**
	 * Render the accounts page.
	 *
	 * @return void
	 */
	public function render_page_contents(): void {
		$views_dir = WP2_DOWNLOAD_PATH . 'src/Admin/views/accounts/';
		$tabs = $this->get_tabs( $views_dir );
		$current_tab = $this->get_current_tab( $tabs );

		$build_tab_url = static function (string $tab): string {
			$url = add_query_arg(
				[ 
					'page' => 'wp2-hub-accounts',
					'tab' => $tab,
				],
				admin_url( 'admin.php' )
			);

			return esc_url( $url );
		};

		$render_tab = function (string $tab) use ($views_dir, $tabs): void {
			if ( ! isset( $tabs[ $tab ] ) ) {
				return;
			}
			$file = $tabs[ $tab ]['file'];
			if ( is_readable( $file ) ) {
				// Variables available to views:
				$tab_key = $tab;
				$tab_label = $tabs[ $tab ]['label'];

				require $file; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
			}
		};

		require WP2_DOWNLOAD_PATH . 'src/Admin/views/accounts.php';
	}

	/**
	 * Discover tabs from view files.
	 *
	 * @param string $views_dir Absolute path to tabs views directory.
	 * @return array<string,array{label:string,file:string}>
	 */
	private function get_tabs( string $views_dir ): array {
		$tabs = [];

		if ( ! is_dir( $views_dir ) ) {
			return $tabs;
		}

		$files = glob( trailingslashit( $views_dir ) . '*.php' );
		if ( empty( $files ) ) {
			return $tabs;
		}

		foreach ( $files as $file ) {
			$slug = sanitize_key( basename( $file, '.php' ) );
			$label = ucwords( str_replace( [ '-', '_' ], ' ', $slug ) );

			$tabs[ $slug ] = [ 
				'label' => $label,
				'file' => $file,
			];
		}

		// Allow tab order to be set via filter.
		$order = apply_filters( 'wp2_download_admin_accounts_tab_order', array_keys( $tabs ) );
		$ordered_tabs = [];
		foreach ( $order as $slug ) {
			if ( isset( $tabs[ $slug ] ) ) {
				$ordered_tabs[ $slug ] = $tabs[ $slug ];
			}
		}
		// Add any tabs not in the order array.
		foreach ( $tabs as $slug => $tab ) {
			if ( ! isset( $ordered_tabs[ $slug ] ) ) {
				$ordered_tabs[ $slug ] = $tab;
			}
		}

		/**
		 * Filter the discovered accounts tabs.
		 *
		 * @param array<string,array{label:string,file:string}> $tabs Tabs map.
		 */
		return apply_filters( 'wp2_download_admin_accounts_tabs', $ordered_tabs );
	}

	/**
	 * Resolve the current tab.
	 *
	 * @param array<string,array{label:string,file:string}> $tabs Tabs map.
	 * @return string
	 */
	private function get_current_tab( array $tabs ): string {
		$first = array_key_first( $tabs ) ?? '';
		if ( empty( $first ) ) {
			return '';
		}

		$requested = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : $first; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		return array_key_exists( $requested, $tabs ) ? $requested : $first;
	}
}

