<?php
// tests/Unit/Storage/CloudflareR2AdapterTest.php

use WP2\Download\Core\Storage\Adapters\CloudflareR2\Adapter;

it( 'builds the correct endpoint URL from the account ID', function () {
	$config = [ 
		'account_id' => 'my-account-id',
		'bucket' => 'my-bucket',
	];

	$adapter = new Adapter( $config );

	// Assert that the adapter constructs the URL as expected.
	expect( $adapter->get_base_url() )->toBe( 'https://my-account-id.r2.cloudflarestorage.com/my-bucket' );
} );

it( 'returns false from connect when required config is missing', function () {
	// Test without an account ID
	$adapter = new Adapter( [ 'bucket' => 'my-bucket' ] );

	expect( $adapter->connect() )->toBeFalse();
} );