
# WP2 Package Manifests

This directory contains manifests for WordPress installables, organized by kind:

- `mu-plugins/` — Must-use plugins
- `plugins/` — Standard plugins
- `themes/` — Themes

## How to Add a New Package
1. Create a folder named after the package slug (e.g., `my-plugin`).
2. Add a `manifest.json` file with required keys: `$schema`, `name`, `slug`, `type`, `version`, `origin`, `meta`.
3. Validate your manifest against [`schema.json`](schema.json).
4. Follow naming conventions: slug = directory name; versioning can be `auto`, pinned, or a range.
5. Artifacts are resolved from the `origin` field.

## Folder Structure Example
```
plugins/
	my-plugin/
		manifest.json
```

## Validation
Lint your JSON and validate against the schema. Optionally use a CI script for automated checks.

## Policy Pointers
- Mirroring and licensing are controlled by origin and licensing docs ([Origin](../../src/Origin/README.md), [Licensing](../../src/Licensing/README.md)).