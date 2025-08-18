# Plugins

Manifests for plugins, organized by slug. Supports multiple origins (Composer, WP.org, etc). Example:

```
{
  "$schema": "../../schema.json",
  "name": "Admin Columns Pro",
  "slug": "admin-columns-pro",
  "type": "plugin",
  "version": "6.*",
  "origin": { "type": "composer", "package": "admin-columns/admin-columns-pro", "constraint": "6.*", "registry": "https://composer.admincolumns.com" },
  "meta": { "description": "Improve list tables with custom columns.", "author": "Admin Columns" }
}
```