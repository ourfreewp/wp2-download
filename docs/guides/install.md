

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
