# Health Checks Catalog

This document lists the health checks available in WP2 Download, their purpose, and failure modes.

## Health Checks
- **Manifest Validation:** Ensures manifest.json is valid and matches schema.
  - Failure: Invalid schema, missing fields
- **Origin Reachability:** Checks if origin is reachable and responds as expected.
  - Failure: Timeout, 404, authentication error
- **Artifact Existence:** Verifies that the expected artifact exists in storage or origin.
  - Failure: Missing file, permission denied
- **Version Consistency:** Ensures version in manifest matches available versions in origin.
  - Failure: Version mismatch, missing version
- **Licensing/Entitlement:** Checks if licensing requirements are met for premium packages.
  - Failure: License invalid, entitlement missing
- **Download Gateway:** Verifies that download URLs are valid and accessible.
  - Failure: Expired URL, access denied

## How to Interpret Failures
- Review audit logs in the Admin UI.
- Address missing or invalid fields in manifest.
- Check credentials and permissions for origins and storage.
- For licensing failures, verify entitlement and license configuration.

## Adding New Checks
- Implement new checks in `src/Health/Checks/` and register them in the Health Runner.
