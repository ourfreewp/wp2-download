#!/bin/bash
# Auto-fix coding standard violations using PHPCBF
set -e

wp_content_vendor="wp-content/wp2/vendor"
phpcbf_bin="$wp_content_vendor/bin/phpcbf"
phpcs_fixer_bin="$wp_content_vendor/bin/php-cs-fixer"


# Run PHP CS Fixer to auto-fix code style
$phpcs_fixer_bin fix --config=.php-cs-fixer.php

# Run PHPCBF
$phpcbf_bin --report=full