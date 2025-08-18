
# Client Updater Integration Guide

This guide explains how to use the WP2 client updater classes in your own plugins and themes.

## Concept
The updater hooks into WordPress's update system via transients and scheduled checks. It compares local and remote versions, fetches manifests, and reports usage.

## Lifecycle
- Check for updates (cron or admin page)
- Compare local/remote version
- Fetch manifest from hub
- Decide if update is needed
- Download and install update
- Report-in usage

## Offline Behavior
- If the hub is unreachable, updater retries with exponential backoff and skips update for that cycle.

## Security
- Update URLs are validated and must use HTTPS.
- Signed URLs are used for downloads.

## Environmental Constraints
- Requires external HTTP access.
- Should run on cron for scheduled checks.

## ThemeUpdater
The `ThemeUpdater` class enables your theme to receive updates from the WP2 Hub.

**Usage Example:**
```php
use WP2\Download\Client\ThemeUpdater;
$updater = new ThemeUpdater('https://your-hub-url.com');
```

## Updater
The `Updater` class enables your plugin to receive updates from the WP2 Hub.

**Usage Example:**
```php
use WP2\Download\Client\Updater;
$updater = new Updater(__FILE__, 'https://your-hub-url.com');
```

## Integration Steps
1. Copy the relevant updater class into your plugin or theme.
2. Instantiate the updater in your main file.
3. Ensure your hub URL is correct and accessible.
4. The updater will automatically check for updates and report usage to the hub.

## Advanced
- You can customize reporting, caching, and hooks by extending the updater classes.
- See the source code for more details and available methods (`src/Client/ThemeUpdater.php` and `src/Client/Updater.php`).

## Troubleshooting
- Ensure the updater class is present in your plugin or theme codebase.
- Check your hub URL and network connectivity.
- Review the [API Reference](api-reference.md) for endpoint details.

---
For further help, see the [API Reference](api-reference.md) and source code in `src/Client/ThemeUpdater.php` and `src/Client/Updater.php`.
