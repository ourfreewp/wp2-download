# Origin Subsystem

The **Origin** module is responsible for discovering, validating, and resolving packages from multiple sources (“origins”), and presenting them to the hub in a normalized way. Each origin has an **adapter** that implements a shared contract so the rest of the system can treat all packages consistently.

## Goals

- Normalize discovery (metadata and versions) across different markets and storage systems.
- Resolve the correct artifact for delivery, along with integrity material (checksum/signature) where available.
- Respect licensing, rate limits, and terms-of-service for each origin.
- Indicate whether mirroring is permitted and what update mode is recommended.

## Concepts

- **Origin**: The external system where a package lives (WordPress.org, Composer, GitHub, Google Drive, Hub Storage).
- **source_ref**: The origin-specific identifier for a package (e.g., `slug`, `vendor/package`, `owner/repo`, `file_id`, `r2_key`).
- **Adapter**: Code that translates between an origin’s API and the hub’s normalized expectations.
- **Gateway**: The layer that applies policy (licensing, mirroring, integrity checks) and actually delivers artifacts.

## Required Adapter Behaviors

- Validate the `source_ref` is minimally complete and well-formed.
- Fetch normalized **metadata** (name, description, links, requirements).
- Discover **versions** (list newest-first when possible).
- Resolve an **artifact** for a given version (URL, optional headers, checksum and/or signature data).
- Report whether **mirroring** is allowed, and provide a **default update mode** hint.
- Surface **errors** through a consistent channel.

## Source Reference Expectations (per adapter)

- **WP.org**: `{ "slug": "<wporg-slug>" }`
- **Composer**: `{ "package": "vendor/package", "constraint": "^1.2" }`
- **GitHub**: `{ "owner": "org", "repo": "name", "tag": "v1.*" }`
- **Google Drive**: `{ "file_id": "<drive-file-id>" }`
- **Hub Storage**: `{ "r2_key": "plugins/my-plugin-1.2.3.zip" }`

## Integrity & Policy

- If the origin supplies checksums or signatures, adapters should expose them when available.
- Mirroring is **disabled by default** unless clearly permitted (policy controlled at higher layers).
- Premium/licensed packages should be handled by specialized adapters that enforce entitlements before artifact resolution.

## Notes

- Adapters should be conservative: do not assume mirroring rights and avoid aggressive polling.
- Keep responses minimal and normalized; the Gateway enriches and applies policy later.
