<?php
namespace WP2\Download\REST\Manifests;

use WP2\Download\Release\Channel;

class Controller {
	public function register_routes() {
		add_action( 'rest_api_init', function () {
			register_rest_route( 'wp2/v1', '/manifest/(?P<type>[a-z\-]+)/(?P<slug>[a-z0-9\-]+)', [ 
				'methods' => 'GET',
				'callback' => [ $this, 'generate_package_manifest' ],
				'permission_callback' => '__return_true',
			] );
		} );
	}

	public function generate_package_manifest( $request ) {
		$type = $request->get_param( 'type' );
		$slug = $request->get_param( 'slug' );
		$channel_param = $request->get_param( 'channel' ) ?? Channel::STABLE;
		$channel = Channel::is_valid( $channel_param ) ? $channel_param : Channel::STABLE;
		$parent_post = get_page_by_path( $slug, OBJECT, "wp2_{$type}" );
		if ( ! $parent_post ) {
			return new \WP_REST_Response( [ 'error' => 'Package not found.' ], 404 );
		}
		$release_query = new \WP_Query( [ 
			'post_type' => "wp2_{$type}_rel",
			'post_parent' => $parent_post->ID,
			'posts_per_page' => 1,
			'orderby' => 'date',
			'order' => 'DESC',
			'meta_query' => [ 
				[ 'key' => 'wp2_channel', 'value' => $channel ]
			]
		] );
		if ( ! $release_query->have_posts() ) {
			return new \WP_REST_Response( [ 'error' => 'No releases found.' ], 404 );
		}
		$release_post = $release_query->posts[0];
		$version = get_post_meta( $release_post->ID, 'wp2_version', true );
		$manifest = [ 
			'name' => get_the_title( $parent_post ),
			'slug' => $slug,
			'version' => $version,
			'author' => get_post_meta( $parent_post->ID, 'wp2_author', true ),
			'links' => get_post_meta( $parent_post->ID, 'wp2_links', true ),
			'requires_php' => get_post_meta( $parent_post->ID, 'wp2_requires_php', true ),
			'requires' => get_post_meta( $parent_post->ID, 'wp2_requires_wp', true ),
			'tested' => get_post_meta( $release_post->ID, 'wp2_tested', true ),
			'download_url' => home_url( "/wp2-download/{$type}/{$slug}/{$version}" ),
			'last_updated' => get_the_modified_date( 'Y-m-d H:i:s', $release_post ),
			'sections' => [ 
				'description' => get_post_meta( $parent_post->ID, 'wp2_description', true ),
				'changelog' => 'See repository for changelog.',
			],
			'banners' => [],
		];
		return new \WP_REST_Response( $manifest, 200 );
	}
}
