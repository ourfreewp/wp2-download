
# API Reference

This document describes the main modules and interfaces in the `WP2\Download` mu-plugin.

## Surface Area Map
- **Admin:** UI, settings, management ([Admin](../src/Admin/README.md))
- **Analytics:** Usage/event tracking ([Analytics](../src/Analytics/README.md))
- **Client:** Updater logic ([Client Updater Guide](client-updater-guide.md))
- **Content:** Package management
- **Development:** Dev platform adapters ([Development](../src/Development/README.md))
- **Gateway:** Download gateway logic
- **Health:** Health checks/audits ([Health](../src/Health/README.md))
- **Licensing:** Licensing adapters ([Licensing](../src/Licensing/README.md))
- **Origin:** Origin adapters ([Origin](../src/Origin/README.md))
- **Release:** Release channel logic
- **REST:** REST API endpoints
- **Services:** Service locator/init ([Services](../src/Services/README.md))
- **Storage:** Storage adapters ([Storage](../src/Storage/README.md))
- **Util:** Utilities

## Endpoints
See [Architecture](architecture.md) for endpoint details and payload shapes.

## Usage
- All classes are namespaced under `WP2\Download`.
- Use `ServiceLocator` to access registered services:
```php
$service = WP2\Download\Services\Locator::get('Admin');
```
- For updater integration:
```php
$updater = new WP2\Download\Client\Updater(__FILE__, 'https://your-hub-url.com');
```
See the [Usage Guide](usage-guide.md) for more examples and details.
