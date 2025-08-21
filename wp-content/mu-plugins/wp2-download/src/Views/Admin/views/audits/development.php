<?php
/**
 * Settings for the Development Audit.
 */

defined( 'ABSPATH' ) || exit;
?>


<div class="card p-3">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h5 class="mb-0">
			<?php echo esc_html( sprintf( /* translators: %s: provider name */ __( 'Development Audit: %s', 'wp2-download' ), 'GitHub' ) ); ?>
		</h5>
		<a class="button button-primary" href="<?php echo esc_url( '#' ); ?>">
			<span class="dashicons dashicons-update" aria-hidden="true"></span>
			<?php echo esc_html__( 'Run Audit', 'wp2-download' ); ?>
		</a>
	</div>

	<div class="row">
		<div class="col-md-6">
			<h6 class="fw-semibold mt-2"><?php echo esc_html__( 'Repository Info', 'wp2-download' ); ?></h6>
			<p>
				<strong><?php echo esc_html__( 'Name:', 'wp2-download' ); ?></strong>
				<?php echo esc_html( 'wp2-download' ); ?>
			</p>
			<p>
				<strong><?php echo esc_html__( 'Owner:', 'wp2-download' ); ?></strong>
				<a href="<?php echo esc_url( 'https://github.com/webmultipliers' ); ?>" target="_blank" rel="noopener noreferrer">webmultipliers</a>
			</p>
			<p>
				<strong><?php echo esc_html__( 'Description:', 'wp2-download' ); ?></strong>
				<?php echo esc_html__( 'This is a mock description for the WP2-download package.', 'wp2-download' ); ?>
			</p>
			<p>
				<strong><?php echo esc_html__( 'Language:', 'wp2-download' ); ?></strong>
				<?php echo esc_html( 'PHP' ); ?>
			</p>
			<p>
				<strong><?php echo esc_html__( 'Visibility:', 'wp2-download' ); ?></strong>
				<?php echo esc_html__( 'private', 'wp2-download' ); ?>
			</p>
			<p>
				<strong><?php echo esc_html__( 'Repository URL:', 'wp2-download' ); ?></strong>
				<a href="<?php echo esc_url( 'https://github.com/webmultipliers/wp2-download' ); ?>" target="_blank" rel="noopener noreferrer">https://github.com/webmultipliers/wp2-download</a>
			</p>
		</div>
		<div class="col-md-6">
			<h6 class="fw-semibold mt-2"><?php echo esc_html__( 'Activity & Health', 'wp2-download' ); ?></h6>
			<p>
				<strong><?php echo esc_html__( 'Latest Tag:', 'wp2-download' ); ?></strong>
				<span class="code">v1.0.0</span>
			</p>
			<p>
				<strong><?php echo esc_html__( 'Last Push:', 'wp2-download' ); ?></strong>
				<?php echo esc_html( '8/16/2025, 8:34:16 PM' ); ?>
			</p>
			<p>
				<strong><?php echo esc_html__( 'Stars:', 'wp2-download' ); ?></strong>
				<?php echo esc_html( '5' ); ?>
			</p>
			<p>
				<strong><?php echo esc_html__( 'Forks:', 'wp2-download' ); ?></strong>
				<?php echo esc_html( '1' ); ?>
			</p>
			<p>
				<strong><?php echo esc_html__( 'Open Issues:', 'wp2-download' ); ?></strong>
				<?php echo esc_html( '2' ); ?>
			</p>
		</div>
	</div>
</div>
