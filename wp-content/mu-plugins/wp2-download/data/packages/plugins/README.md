
# Plugins

Manifests for plugins, organized by slug. Supports multiple origins (Composer, WP.org, GitHub, Google Drive, Storage).

## Supported Origins & Examples

- **Composer:**
  ```json
  {
    "origin": { "type": "composer", "package": "admin-columns/admin-columns-pro", "constraint": "6.*", "registry": "https://composer.admincolumns.com" }
  }
  ```
- **WP.org:**
  ```json
  {
    "origin": { "type": "wporg", "slug": "akismet" }
  }
  ```
- **GitHub:**
  ```json
  {
    "origin": { "type": "github", "owner": "org", "repo": "name", "tag": "v1.*" }
  }
  ```
- **Google Drive:**
  ```json
  {
    "origin": { "type": "gdrive", "file_id": "drive-file-id" }
  }
  ```
- **Storage:**
  ```json
  {
    "origin": { "type": "storage", "r2_key": "plugins/my-plugin-1.2.3.zip" }
  }
  ```

## Version Semantics
- `auto`: hub resolves latest per policy/channel
- pinned: e.g., `1.2.3`
- range/constraint: e.g., `6.*` (composer)

## Licensing
- Premium packages: no mirroring by default; see [Licensing](../../src/Licensing/README.md).

## Channels
- Supports `stable`, `beta`, `rc` if defined.

## Origin Matrix
| Origin   | Required Fields                | Example                      |
|----------|-------------------------------|------------------------------|
| composer | package, constraint, registry | see above                    |
| wporg    | slug                          | see above                    |
| github   | owner, repo, tag              | see above                    |
| gdrive   | file_id                       | see above                    |
| storage  | r2_key                        | see above                    |

## Update Modes
- Native WP for wporg
- Direct vendor for premium composer
- Hub mirrored for self-hosted