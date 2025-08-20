#!/bin/bash
# Auto-fix coding standard violations using PHPCBF
set -e

wp_content_vendor="wp-content/wp2/vendor"
phpcbf_bin="$wp_content_vendor/bin/phpcbf"

# Run PHPCBF
$phpcbf_bin --report=full