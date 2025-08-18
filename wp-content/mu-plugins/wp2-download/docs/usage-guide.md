# Usage Guide

This guide explains how to use the `wp2-download` mu-plugin in your WordPress environment.

## Installation

1. Place the `wp2-download` directory in `wp-content/mu-plugins/`.
2. Ensure Composer dependencies are installed (`composer install`).
3. Activate via WordPress admin if required.

## Basic Usage

- The plugin automatically registers services and hooks on load.
- Admin UI is available under the WordPress admin menu.
- Health checks and audits run automatically or can be triggered manually.
- Licensing and update logic are handled in the background.

## Extending Functionality

- Add new adapters or checks by creating classes in the appropriate domain (`src/Analytics/Adapters`, `src/Health/Checks`, etc.).
- Register new services via `ServiceLocator`.



## Common Tasks
- **Register a package via manifest:** Add manifest to `data/packages`, validate, and reload hub.
- **Run health checks and read results:** Trigger via admin UI or API, view results in Audits tab.
- **Promote to stable channel:** Update manifest channel, reload hub.
- **Sync from Composer lock:** Add composer package manifest, validate, and reload.
- **Configure origin:** Add origin details to manifest, validate, and reload.

## Troubleshooting by Subsystem
- **REST:** Check endpoint URLs and authentication.
- **Origins:** Validate manifest and origin fields.
- **Storage:** Check R2 credentials and bucket policy.
- **Health:** Review audit logs and check runner status.

## First 10 Minutes Walkthrough
1. Add a wporg plugin manifest.
2. Add a composer premium plugin manifest.
3. Run health checks.
4. Validate gateway works by downloading a package.

## Troubleshooting

- Check `debug.log` in `wp-content` for errors.
- Ensure all dependencies are up to date.

---
For more details, see the [Architecture](architecture.md) and [API Reference](api-reference.md).
