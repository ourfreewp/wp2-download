#!/bin/bash
# Validate composer configuration
set -e

wp_content_vendor="wp-content/wp2/vendor"
composer_bin="$wp_content_vendor/bin/composer"

# Check composer configuration and lock file validity
$composer_bin validate --strict