# WP2 Download Services

Contains service classes for the WP2 Download plugin, including the Service Locator and initialization logic.

## Key Files

- `Locator.php` — Discovers and instantiates adapters for analytics, licensing, storage, development, and origin sources. Handles lazy origin instantiation and unified origin keys (`composer`, `github`, `gdrive`, `storage`, `wporg`).
- `Init.php` — Registers plugin settings and initializes services.

## Service Locator

The Service Locator connects the admin UI and plugin logic to the correct adapter implementations, enabling modular service selection. It supports:

- Unified origin keys for all source types: `composer`, `github`, `gdrive`, `storage`, `wporg`.
- Lazy instantiation of origin adapters if not registered.
- Correct adapter scan paths for all service types.

## Adapter Discovery

Adapter directories are scanned using the correct paths, ensuring all available adapters are listed for each service type.

## Usage

Use the Locator to resolve adapters for analytics, licensing, storage, development, and origin sources. The system will return `null` if no adapter is selected or available, requiring explicit configuration.
