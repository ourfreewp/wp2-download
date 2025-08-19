# WP2 Download Analytics

Contains analytics adapter interfaces and implementations for tracking events and usage.

## Key Files
- `ConnectionInterface.php` — Interface for analytics adapters.
- `Adapters/` — Implementations such as DefaultAdapter and PostHogAdapter.


## Default Adapter & Configuration
The default adapter is `DefaultAdapter`. Configure analytics in the admin settings or via constants.

## Core Events
- `package_update`
- `health_check_run`
- `download_issued`

## Privacy
- Supports sampling and retention policies.
- Opt-out available via settings.

## Connection
The Service Locator loads the selected analytics adapter, which is used by plugin logic to track events.
