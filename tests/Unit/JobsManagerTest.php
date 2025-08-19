<?php

// tests/Unit/JobsManagerTest.php

use WP2\Download\Core\Jobs\Manager;

// Mock the global Action Scheduler functions that our Manager uses.
// This allows us to test our Manager in complete isolation.
function as_schedule_single_action( $timestamp, $hook, $args, $group, $unique, $priority ) {
	// We can just return a dummy value for the test.
	return 123;
}

it( 'schedules a single action', function () {
	$manager = new Manager();

	$timestamp = time();
	$hook = 'my_custom_hook';
	$args = [ 'foo' => 'bar' ];

	$actionId = $manager->schedule_single_action( $timestamp, $hook, $args );

	// Assert that our manager returns the expected value from the underlying function.
	expect( $actionId )->toBe( 123 );
} );