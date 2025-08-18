# Origins Policy Reference

This document describes origin types, mirroring defaults, licensing constraints, and update modes for WP2 Download.

## Origin Types & Policy
| Type      | Mirroring Default | Licensing Required | Update Mode         |
|-----------|-------------------|--------------------|---------------------|
| wporg     | Disabled          | No                 | Native WP           |
| composer  | Disabled for premium | Yes for premium | Direct vendor       |
| github    | Disabled          | Optional           | Mirrored if allowed |
| gdrive    | Disabled          | Optional           | Mirrored if allowed |
| storage   | Enabled           | Optional           | Hub mirrored        |

## Licensing Constraints
- Premium packages require entitlement checks before download.
- Licensing adapters enforce policy at artifact resolution.

## Update Modes
- **Native WP:** Uses WordPress.org update system.
- **Direct vendor:** Uses vendor registry (e.g., Composer).
- **Mirrored:** Hub serves package from its own storage.

## Example Origin Objects
- WP.org: `{ "type": "wporg", "slug": "akismet" }`
- Composer: `{ "type": "composer", "package": "vendor/package", "constraint": "^1.2" }`
- GitHub: `{ "type": "github", "owner": "org", "repo": "name", "tag": "v1.*" }`
- Google Drive: `{ "type": "gdrive", "file_id": "drive-file-id" }`
- Storage: `{ "type": "storage", "r2_key": "plugins/my-plugin-1.2.3.zip" }`

## Policy Enforcement
- Mirroring and licensing are enforced in Origin and Gateway subsystems.
- See [Licensing](licensing.md) for details.
