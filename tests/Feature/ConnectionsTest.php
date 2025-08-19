<?php
// tests/Feature/ConnectionsTest.php

use WP2\Download\Services\Locator;
use WP2\Download\Core\Storage\ConnectionInterface;

it( 'returns a successful connection status', function () {
	// 1. Mock the Service Locator
	// We will tell the Locator to return a mock adapter that we control.
	$mockAdapter = Mockery::mock( ConnectionInterface::class);
	$mockAdapter->shouldReceive( 'connect' )->andReturn( true );

	// Replace the real Locator's storage() method with our mock.
	Locator::shouldReceive( 'storage' )->andReturn( $mockAdapter );

	// 2. Make the API Request
	// This will make a real request to the WordPress REST API, which will
	// then execute our controller's logic.
	$response = $this->post( '/wp-json/wp2-download/v1/test-connection', [ 
		'service' => 'storage',
	] );

	// 3. Assert the Response
	// We check that the HTTP status is OK and that the JSON response
	// contains the data we expect.
	$response->assertStatus( 200 );
	$response->assertJson( [ 
		'ok' => true,
		'message' => 'Storage connection successful.',
	] );
} );