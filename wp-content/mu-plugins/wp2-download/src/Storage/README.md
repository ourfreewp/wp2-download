# WP2 Download Storage

Contains storage adapter interfaces and implementations for file and object storage.

## Key Files
- `ConnectionInterface.php` — Interface for storage adapters.
- `Adapters/` — Implementations such as DefaultAdapter and CloudflareR2Adapter.


## Connection
The Service Locator loads the selected storage adapter, which is used by plugin logic to store and retrieve files.

## Cloudflare R2 & Presigned URLs
The primary adapter is Cloudflare R2, which uses presigned URLs for secure, time-limited downloads. Object keys follow the convention: `type/slug/slug-version.zip`. Retention policies are set at the bucket level.

## Security
- Use least-privilege IAM for R2 credentials.
- Set bucket policies to restrict public access.
- Consider CDN integration for download endpoints.
