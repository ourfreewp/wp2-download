
# WP2 Download Development

Contains development service adapter interfaces and implementations for integration with development platforms (e.g., GitHub for tag discovery and repo metadata).

## Key Files
- `ConnectionInterface.php` — Interface for development adapters.
- `Adapters/` — Implementations such as DefaultAdapter and GithubAdapter.

## Supported Providers
- GitHub (initial)

## Credentials
Required credentials (e.g., GitHub PAT) are read from environment variables or `wp-config.php` constants.

## Connection
The Service Locator loads the selected development adapter, which is used by plugin logic for development platform integration.
