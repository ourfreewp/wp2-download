
# Themes

Manifests for themes, organized by slug.

## Canonical Origin Example
```json
{
  "origin": { "type": "wporg", "slug": "twentytwentyfive" }
}
```

## Version Strategy
- Core themes often use `auto` for latest.

## FSE/Block Compatibility
- Indicate required WP version and FSE/block support in `meta`.

## Child Themes
- Represent child themes with a `parent_slug` field in `meta`.

## Example Manifest
```json
{
  "$schema": "../../schema.json",
  "name": "Twenty Twenty-Five",
  "slug": "twentytwentyfive",
  "type": "theme",
  "version": "auto",
  "origin": { "type": "wporg", "slug": "twentytwentyfive" },
  "meta": { "description": "Default WordPress theme for 2025.", "author": "WordPress.org", "required_wp": "6.0+", "fse": true }
}
```