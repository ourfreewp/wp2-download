# WP2 Update 

This guide explains how to use the `wp2-update.php` SDK to add secure, automated update capabilities to your theme, plugin, or must-use (MU) plugin, powered by a WP2-Download Hub.

The primary purpose of this SDK is to manage the update lifecycle for the single package it is bundled with.

## Installation

The Package Updater is a single-file, drop-in library.

**Place the File:**  
Copy the `wp2-update.php` file into your package's directory (e.g., inside an `/includes` folder).

**Configure `wp-config.php`:**  
The SDK relies on two constants being defined in the site's `wp-config.php` file:

```php
/**
 * WP2 Update SDK Configuration
 */
// The full REST API URL for your WP2-Download Hub
define( 'WP2_HUB_URL', 'https://your-hub-domain.com/wp-json/wp2/v1' );

// The public key used to verify package signatures
define( 'WP2_PUBLIC_KEY', '-----BEGIN PUBLIC KEY-----...-----END PUBLIC KEY-----' );
```

**Include and Initialize:**  
Include the `wp2-update.php` file and instantiate the appropriate wrapper class from your theme's `functions.php` or your plugin's main file.

## Usage Examples

Choose the wrapper class that matches your package type.

### For a Theme

In your theme's `functions.php`:

```php
require_once __DIR__ . '/path/to/wp2-update.php';

// The Theme_Updater class automatically detects the theme's context.
new \WP2\Update\Theme_Updater();
```

### For a Plugin

In your main plugin file (e.g., `my-plugin.php`):

```php
require_once __DIR__ . '/path/to/wp2-update.php';

// Pass the __FILE__ constant to the constructor so the SDK knows which plugin to manage.
new \WP2\Update\Plugin_Updater( __FILE__ );
```

### For a Must-Use (MU) Plugin

In your main MU plugin file:

```php
require_once __DIR__ . '/path/to/wp2-update.php';

// Pass the __FILE__ constant to the constructor.
new \WP2\Update\MU_Plugin_Updater( __FILE__ );
```

## How It Works

- **Initialization:**  
	Once initialized, the SDK automatically determines the package type (theme, plugin, or mu-plugin) and its slug.

- **Update Checks:**  
	- For themes and regular plugins, the SDK hooks into the standard WordPress update process (`pre_set_site_transient_update_*`).
	- For MU plugins, which don't have a UI for updates, the SDK sets up a twice-daily cron job to check for new versions.

- **API Communication:**  
	The SDK communicates with the hub to fetch a manifest file. This manifest contains the latest version number, a secure download URL, and a cryptographic signature.

- **Authentication:**  
	If the Site Connector SDK (`wp2-connect.php`) is active on the site, the updater will automatically use the site's verified API token for communication. This is the preferred method. If not, it can be configured to use a fallback authentication token.

- **Signature Verification:**  
	Before an update is installed, the SDK intercepts the downloaded `.zip` file. It uses the `WP2_PUBLIC_KEY` to verify the package's signature from the manifest. This critical step ensures the code is authentic and has not been tampered with. If verification fails, the update is aborted.

- **MU Plugin Updates:**  
	For MU plugins, the update process is fully automated. If a new, verified version is found, the SDK will download, unzip, and replace the old plugin file with the new one.

- **Reporting:**  
	After a successful update and once daily, the SDK sends a small, anonymous "report-in" payload to the hub with the current package version, helping you track adoption rates.