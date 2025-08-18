# Architecture Overview

This document provides a high-level overview of the architecture for the `wp2-download` mu-plugin.


## Structure

- **init.php**: Entry point for the plugin, responsible for bootstrapping services and loading dependencies.
- **src/**: Contains all core PHP classes, organized by domain (Admin, Analytics, Client, Content, Development, Gateway, Health, Licensing, Origin, Release, REST, Services, Storage, Util).
- **blocks/**: Contains block editor components, including admin UI blocks.
- **assets/**: Static assets such as scripts and styles.
- **data/**: Package data for plugins, themes, and mu-plugins.
- **vendor/**: Composer dependencies.

## Subsystems
- **Origin:** Adapters for external sources, normalization, mirroring/licensing policy.
- **Gateway:** Delivery, policy enforcement, artifact resolution.
- **Health:** Runner, checks, audit results.
- **REST:** API controllers for endpoints.

## Data Artifacts
- `manifest.json`: Package metadata and origin.
- Audit entries: Health check results.
- Health results: Status, errors, recommendations.

## Update Modes
- Decision points for update mode (native, mirrored, direct vendor) are handled in Gateway and Origin subsystems.

## Policy Hooks
- Licensing and mirroring rules are enforced in Origin and Gateway.

## Main Components

- **ServiceLocator**: Central registry for accessing services.
- **Admin**: Handles admin UI, manifests, and views.
- **Analytics**: Provides analytics interfaces and adapters.
- **Client**: Updater logic for themes and plugins.
- **Health**: System health checks and audits.
- **Licensing**: Licensing logic and adapters.
- **Release**: Channel management for releases.
- **Util**: Utility classes (e.g., Logger).


## Main Flows

1. **Release Registration:** GitHub Actions notifies the hub, which registers new releases and links R2 artifacts.
2. **Health Checks:** Automated and manual audits verify package integrity and storage.
3. **Client Update Reporting:** Client plugins/themes report active versions to the hub.
4. **Admin Management:** Admin UI provides package, audit, and health status views.

## Data Flow

1. **Bootstrap:** `init.php` loads and registers services.
2. **Admin UI:** Admin blocks and views are rendered for management.
3. **Updates:** Client updaters handle update logic for plugins/themes.
4. **Health Checks:** Health module runs audits and checks.
5. **Licensing:** Licensing adapters validate and manage licenses.

## Extensibility

- Modular design allows for easy addition of new adapters, checks, and blocks.
- Uses Composer for dependency management.


---
For more details, see the [API Reference](api-reference.md) and [Usage Guide](usage-guide.md).
