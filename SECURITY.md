# Security & Secrets Guidance

This document describes best practices for secrets management and security in WP2 Download.

## Secrets Management
- Store secrets (API keys, tokens, credentials) in environment variables or `wp-config.php`.
- Never commit secrets to manifests, composer.json, or version control.
- Use least privilege for all credentials.

## Composer Auth
- Store Composer credentials in `auth.json` (never in composer.json).
- Restrict access to `auth.json` and use environment variables for CI/CD.

## GitHub PAT
- Store Personal Access Tokens in environment or CI secrets.
- Rotate tokens regularly and restrict scopes.

## R2 Keys
- Store Cloudflare R2 keys in environment or config.
- Set bucket policies to restrict public access.

## File Permissions
- Restrict permissions on sensitive files and directories.
- Use principle of least privilege for all users and services.

## HTTPS
- Always use HTTPS for update and download endpoints.

## Auditing
- Review audit logs for suspicious activity.
- Enable health checks for security validation.
