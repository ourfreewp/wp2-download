# WP2 Download Admin

Contains all admin-side logic for the WP2 Download plugin, including settings pages, views, and management UI.

## Key Files
- `views/` — PHP templates for admin settings and system configuration.
- `assets/` — Admin-specific JS, CSS, and images.


## Pages & Tabs
- **Hub:** Overview, status, quick actions
- **Audits:** Health checks, audit logs
- **Settings:** Configuration options
- **Origins:** Manage package origins

## UI Mock
```
Hub | Audits | Settings | Origins
---------------------------------
| Table/List/Status Panels |
```

## Service Locator Interaction
- Admin UI can read and configure adapters via the Service Locator.

## Permissions
- Requires `manage_options` capability for most actions.
