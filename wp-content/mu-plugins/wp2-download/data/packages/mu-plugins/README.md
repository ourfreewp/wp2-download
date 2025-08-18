
# MU-Plugins

Manifests for must-use plugins. Each manifest describes a single MU-plugin, its origin, and metadata.

## MU Semantics
- MU plugins are always loaded; activation toggles are ignored.
- Recommended deployment: deliver via hub storage for atomic swap/rollback.

## Expected Fields
- Required: `$schema`, `name`, `slug`, `type`, `version`, `origin`, `meta`
- Optional: `channels`, `default_channel`

## Versioning
- Typically pinned; `auto` can resolve via registry if supported.

## Operational Guidance
- Use hub storage for releases and rollbacks.

## Compatibility
- Minimum WP/PHP version: WP 5.2+, PHP 7.4+

## Example Manifest
```
{
  "$schema": "../../schema.json",
  "name": "WP2 Download",
  "slug": "wp2-download",
  "type": "mu-plugin",
  "version": "auto",
  "origin": { "type": "storage", "r2_key": "mu-plugins/wp2-download/wp2-download-{{version}}.zip" },
  "meta": { "description": "Unified download and package origin handling.", "author": "Web Multipliers" }
}
```