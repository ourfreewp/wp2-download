#!/bin/bash
# Lint all code (PHP, JS, CSS)
set -e

wp_content_vendor="wp-content/wp2/vendor"
phpcs_bin="$wp_content_vendor/bin/phpcs"
eslint_bin="./node_modules/.bin/eslint"
stylelint_bin="./node_modules/.bin/stylelint"

# Run PHPCS for PHP files
echo "Running PHPCS..."
$phpcs_bin --report=full

# Run ESLint for JavaScript files
# This assumes you have ESLint installed in the project root.
echo "Running ESLint..."
$eslint_bin packages/eslint-config-humanmade/fixtures/pass packages/eslint-config-humanmade/fixtures/fail

# Run Stylelint for CSS/SCSS files
# This assumes you have Stylelint installed in the project root.
echo "Running Stylelint..."
$stylelint_bin packages/stylelint-config/fixtures/pass packages/stylelint-config/fixtures/fail