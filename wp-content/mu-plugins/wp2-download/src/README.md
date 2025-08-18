# WP2 Download Source Code

This directory contains the main PHP source code for the WP2 Download plugin. It is organized by service and adapter type, following a modular architecture.

## Key Subdirectories
- `Admin/` — Admin UI, settings views, and management logic.
- `Analytics/` — Analytics adapters and interfaces.
- `API/` — REST API endpoints and logic.
- `Client/` — Client-side plugin, theme, and must-use logic.
- `Content/` — Content management and package logic.
- `Development/` — Development service adapters and interfaces.
- `Gateway/` — Gateway and rewrite logic for downloads.
- `Health/` — Health checks and diagnostics.
- `Helpers/` — Helper utilities for rendering and tables.
- `Licensing/` — Licensing adapters and interfaces.
- `Release/` — Release channel logic.
- `REST/` — REST API endpoints for various services.
- `Services/` — Service locator and initialization logic.
- `Storage/` — Storage adapters and interfaces.
- `Util/` — Utility classes (logging, etc).

## Connection
All plugin logic is loaded from this directory. The Service Locator (`Services/Locator.php`) connects adapters and services from their respective subdirectories.
