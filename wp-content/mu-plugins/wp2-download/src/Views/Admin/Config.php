<?php
namespace WP2\Download\Views\Admin;

defined( 'ABSPATH' ) || exit;

class Config {
	public static function get_pages(): array {
		return array(
			'audits' => array(
				'title'     => __( 'Audits', 'wp2-download' ),
				'view_path' => 'audits',
				'tab_order' => array( 'system', 'development', 'storage', 'analytics', 'licensing' ),
			),
			'settings' => array(
				'title'     => __( 'Settings', 'wp2-download' ),
				'view_path' => 'settings',
				'tab_order' => array( 'system', 'development', 'storage', 'analytics', 'licensing' ),
			),
			'origins' => array(
				'title'     => __( 'Origins', 'wp2-download' ),
				'view_path' => 'origins',
				'tab_order' => array( 'settings', 'storage', 'composer', 'wporg', 'github', 'gdrive' ),
			),
			'packages' => array(
				'title'     => __( 'Packages', 'wp2-download' ),
				'view_path' => 'packages',
				'tab_order' => array( 'overview', 'plugins', 'themes', 'mu-plugins' ),
			),
			'releases' => array(
				'title'     => __( 'Releases', 'wp2-download' ),
				'view_path' => 'releases',
				'tab_order' => array( 'overview', 'plugins', 'themes', 'mu-plugins' ),
			),
			'accounts' => array(
				'title'     => __( 'Accounts', 'wp2-download' ),
				'view_path' => 'accounts',
				'tab_order' => array( 'system', 'development', 'storage', 'analytics', 'licensing' ),
			),
			'machines' => array(
				'title'     => __( 'Machines', 'wp2-download' ),
				'view_path' => 'machines',
				'tab_order' => array( 'system', 'development', 'storage', 'analytics', 'licensing' ),
			),
		);
	}
}
