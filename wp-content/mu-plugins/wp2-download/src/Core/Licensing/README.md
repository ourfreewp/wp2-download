# WP2 Download Licensing

Contains licensing adapter interfaces and implementations for license validation and management.

## Key Files
- `ConnectionInterface.php` — Interface for licensing adapters.
- `Adapters/` — Implementations such as DefaultAdapter and KeygenAdapter.


## Policy & Enforcement
Licensing is optional by default. Enforcement can be configured per package. Entitlement is checked before artifact resolution.

## Data & Privacy
License key hashes may be persisted. No personal data is stored.

## Update Decision Points
- Block update if not entitled
- Serve degraded manifest if license invalid

## Connection
The Service Locator loads the selected licensing adapter, which is used by plugin logic for license validation and management.
