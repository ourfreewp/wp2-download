

# Install Guide (Background Knowledge)

**Note:** Only the hub installs wp2-download. Client plugins and themes should never require or depend on it; they communicate directly with the update server.

For advanced integration, see the [Client Updater Guide](client-updater-guide.md).

## Hub Setup Steps
1. Upload `wp2-download.php` and the `wp2-download` directory to `wp-content/mu-plugins/`.
2. Add required constants to `wp-config.php`.
3. Visit **Settings > Permalinks** to flush rewrite rules.
4. Add origin credentials and configure health checks.

## Post-Install Checklist
- Endpoints reachable
- Health checks passing
- Sample package installed

## Security
- Store secrets in environment or config, never in manifests or composer.json.
- Use least privilege for all credentials.

## Native WP Updater Fundamentals (Appendix)
If you want your theme or plugin to communicate directly with your update server, implement the following method:

### For Plugins
Add this code to your plugin's main file:
```php
add_filter('pre_set_site_transient_update_plugins', function($transient) {
    $plugin_slug = 'your-plugin-slug/your-plugin.php';
    $current_version = '1.0.0';
    $response = wp_remote_get('https://yourdomain.com/updates/?plugin=' . $plugin_slug . '&version=' . $current_version);
    if (is_wp_error($response)) {
        return $transient;
    }
    $data = json_decode(wp_remote_retrieve_body($response));
    if (!empty($data->new_version) && version_compare($current_version, $data->new_version, '<')) {
        $transient->response[$plugin_slug] = (object) [
            'slug' => $plugin_slug,
            'new_version' => $data->new_version,
            'url' => $data->info_url,
            'package' => $data->download_url,
        ];
    }
    return $transient;
});
```

### For Themes
Add this code to your theme's `functions.php`:
```php
add_filter('pre_set_site_transient_update_themes', function($transient) {
    $theme_slug = 'your-theme-slug';
    $current_version = wp_get_theme($theme_slug)->get('Version');
    $response = wp_remote_get('https://yourdomain.com/updates/?theme=' . $theme_slug . '&version=' . $current_version);
    if (is_wp_error($response)) {
        return $transient;
    }
    $data = json_decode(wp_remote_retrieve_body($response));
    if (!empty($data->new_version) && version_compare($current_version, $data->new_version, '<')) {
        $transient->response[$theme_slug] = [
            'new_version' => $data->new_version,
            'url' => $data->info_url,
            'package' => $data->download_url,
        ];
    }
    return $transient;
});
```

### Update Server API
Your update server should respond to requests with JSON like:
```json
{
  "new_version": "1.2.3",
  "download_url": "https://yourdomain.com/downloads/your-plugin.zip",
  "info_url": "https://yourdomain.com/info/your-plugin"
}
```
