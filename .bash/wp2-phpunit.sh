#!/bin/bash
# Test script for WP2 Download
# Runs only PHPUnit tests using the vendor binary.
wp_content_vendor="wp-content/wp2/vendor"
phpunit_bin="$wp_content_vendor/bin/phpunit"
phpunit_config="phpunit.xml"
$phpunit_bin -c $phpunit_config