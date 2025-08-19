<?php
// tests/Architecture.php

// This test ensures that no file within the `Core` directory ever uses
// a class from the `Views` directory. This is a powerful way to enforce
// the separation of concerns between your business logic and presentation layers.
arch( 'Core does not depend on Views' )
	->expect( 'WP2\Download\Core' )
	->not->toUse( 'WP2\Download\Views' );

// This test enforces that all interfaces end with the word "Interface".
arch( 'Interfaces are named correctly' )
	->expect( 'WP2\Download\Core' )
	->interfaces()
	->toHaveSuffix( 'Interface' );

// Ensure all adapters in Core implement ConnectionInterface
arch( 'Core adapters implement ConnectionInterface' )
	->expect( 'WP2\Download\Core\Adapters' )
	->classes()
	->toImplement( 'WP2\Download\Extensions\ConnectionInterface' );

// REST controllers do not contain business logic (should only depend on Services/Locator)
arch( 'REST controllers do not contain business logic' )
	->expect( 'WP2\Download\REST' )
	->classes()
	->not->toDependOn( 'WP2\Download\Core' );

// Views do not directly access the database (no dependency on Adapters/Storage)
arch( 'Views do not access the database' )
	->expect( 'WP2\Download\Views' )
	->not->toDependOn( 'WP2\Download\Core\Adapters' )
	->not->toDependOn( 'WP2\Download\Core\Storage' );