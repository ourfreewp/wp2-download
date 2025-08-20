#!/bin/bash
# Run WP2 Download linting and tests locally or in CI
set -e

# Define paths to executables
wp_content_vendor="wp-content/wp2/vendor"
phpcs_bin="$wp_content_vendor/bin/phpcs"
phpunit_bin="$wp_content_vendor/bin/phpunit"
phpunit_config="phpunit.xml"

# Run PHPCS
$phpcs_bin --report=full

# Run PHPUnit
$phpunit_bin -c $phpunit_config