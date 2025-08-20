# WP2 Download Bash Scripts

This directory contains automation scripts for common development, CI, and release tasks in the WP2 Download project.

## Scripts

- **wp2-build.sh**: Runs Composer autoload and any additional build steps. Extend for asset compilation or other build tasks.
- **wp2-clean.sh**: Cleans up cache, uploads, and logs for a fresh workspace.
- **wp2-composer-lint.sh**: Validates `composer.json` and `composer.lock` for syntax and correctness using the Composer binary from vendor.
- **wp2-composer-validate.sh**: Validates `composer.json` and `composer.lock` strictly using the Composer binary from vendor.
- **wp2-fix-lint.sh**: Auto-fixes coding standard violations using PHPCBF. Relies on Composer's config for coding standards.
- **wp2-lint-and-test.sh**: Runs PHPCS for linting and PHPUnit for tests. No manual installed_paths override; uses Composer config.
- **wp2-phpcbf.sh**: Alternative script for running PHPCBF to auto-fix coding standards. No manual installed_paths override.
- **wp2-phpunit.sh**: Runs PHPUnit tests using the vendor binary. Use for test-only runs.
- **wp2-release.sh**: Automates tagging and pushing releases. Extend for GitHub API integration if needed.
- **wp2-test.sh**: Runs only PHPUnit tests using the vendor binary (functionally similar to wp2-phpunit.sh).

## Usage

All scripts assume Composer dependencies are installed and the vendor directory is up to date. Run scripts from the project root unless otherwise noted.

## Notes

- Coding standards are managed by Composer and the installer plugin. No manual installed_paths configuration is needed.
- Scripts are annotated for clarity and maintainability.
- Some scripts have alternative versions for convenience or CI integration.
