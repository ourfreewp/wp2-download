# Manifest Schema Reference

This document describes the schema for package manifests in WP2 Download. Each manifest must conform to the following structure:

## Required Fields
- `$schema`: Path to the schema file (usually `../../schema.json`)
- `name`: Human-readable name of the package
- `slug`: Unique identifier (matches directory name)
- `type`: One of `plugin`, `theme`, `mu-plugin`
- `version`: `auto`, pinned (e.g., `1.2.3`), or range/constraint (e.g., `6.*`)
- `origin`: Object describing the source (see below)
- `meta`: Object with metadata (description, author, etc.)

## Optional Fields
- `channels`: Array of release channels (e.g., `stable`, `beta`, `rc`)
- `default_channel`: Default channel for auto updates
- `parent_slug`: For child themes

## Origin Field Shapes
- **WP.org:** `{ "type": "wporg", "slug": "akismet" }`
- **Composer:** `{ "type": "composer", "package": "vendor/package", "constraint": "^1.2", "registry": "https://composer.example.com" }`
- **GitHub:** `{ "type": "github", "owner": "org", "repo": "name", "tag": "v1.*" }`
- **Google Drive:** `{ "type": "gdrive", "file_id": "drive-file-id" }`
- **Storage:** `{ "type": "storage", "r2_key": "plugins/my-plugin-1.2.3.zip" }`

## Validation
- Validate manifests against `schema.json` using a JSON schema validator.
- Example command: `jsonschema -i manifest.json schema.json`

## Example Manifest
```json
{
  "$schema": "../../schema.json",
  "name": "Example Plugin",
  "slug": "example-plugin",
  "type": "plugin",
  "version": "auto",
  "origin": { "type": "wporg", "slug": "akismet" },
  "meta": { "description": "Akismet anti-spam plugin.", "author": "Automattic" },
  "channels": ["stable", "beta"],
  "default_channel": "stable"
}
```
