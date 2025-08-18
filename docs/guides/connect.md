# WP2-Download: Site Connector SDK Guide

This guide explains how to use the `wp2-connect.php` Site Connector SDK. The purpose of this SDK is to establish a persistent, secure, and verified identity for your entire WordPress site with a WP2-Download Hub.

This site-level connection is the foundation for advanced features like trusted package mirroring, where the hub can provide updates for third-party plugins and themes, knowing it's communicating with a legitimate, registered site.

## Installation

The Site Connector is designed as a single-file Must-Use (MU) plugin for simplicity and robustness.

**Place the File:**  
Drop the `wp2-connect.php` file directly into your `wp-content/mu-plugins/` directory. If this directory does not exist, you can create it.

**Configure `wp-config.php`:**  
Add the following required constants to your site's `wp-config.php` file:

```php
/**
 * WP2 Site Connector Configuration
 */
// The full REST API URL for your WP2-Download Hub
define( 'WP2_CONNECT_HUB_URL', 'https://your-hub-domain.com/wp-json/wp2/v1' );

// The pre-shared registration token provided by your hub administrator
define( 'WP2_CONNECT_REG_TOKEN', 'your-initial-registration-token' );

// Optional: Adjust the API request timeout (in seconds)
// define( 'WP2_CONNECT_TIMEOUT', 20 );
```

Once the file is in place and the constants are defined, the Site Connector will automatically initialize.

## How It Works

- **Automatic Registration:**  
	On the next admin page load, the Site Connector will automatically perform a one-time handshake with the hub. It sends the temporary `WP2_CONNECT_REG_TOKEN` and receives a unique, permanent Site API Token in return.

- **Secure Storage:**  
	This unique Site API Token is stored securely in your site's options table (`wp_options`). It will be used for all future communication with the hub.

- **Mirrored Updates:**  
	During the standard WordPress update checks, the Site Connector sends a list of all installed plugins and themes to the hub. If the hub has a trusted, mirrored version of any of these packages, it will provide the update information, which will then appear on your site's "Updates" screen.

- **Authenticated Downloads:**  
	When you download a package from the hub (either a mirrored package or one using the `wp2-update.php` SDK), the Site Connector automatically adds the necessary authentication headers to the request, ensuring the download is secure and authorized.

- **Daily Heartbeat:**  
	Once a day, the connector sends a small, non-blocking "heartbeat" to the hub to keep its status as an active, registered site fresh.

## Public Access for Other Developers

The Site Connector makes the unique Site API Token available to other themes and plugins on the same site. This allows other tools that integrate with the same hub to use the existing site-level connection without needing to perform their own registration.

Developers can retrieve the token using the static method:

```php
if ( class_exists('\WP2\Connect\Site') && method_exists('\WP2\Connect\Site', 'get_site_api_token') ) {
		$site_api_token = \WP2\Connect\Site::get_site_api_token();
		if ( $site_api_token ) {
				// Use the token for your own API requests...
		}
}
```
