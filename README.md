# wp2-download

> [!WARNING]
> Development is ongoing, and the code is still a work in progress.

WP2 Download is a manifest-driven package hub for WordPress, designed for secure, scalable distribution of plugins, themes, and must-use packages. It supports automated releases, health checks, analytics, licensing, and integration with native WordPress update flows.

## 🚀 Quick Start

See [Install Guide](wp-content/mu-plugins/wp2-download/docs/install-guide.md) for server setup and client integration.

## 📦 Codebase Structure

- `wp-content/mu-plugins/wp2-download.php`: Loader file
- `wp-content/mu-plugins/wp2-download/`: Main plugin code
- `src/`: Core logic, REST API, admin UI, services
- `data/packages/`: Manifest catalog for plugins, themes, mu-plugins
- `docs/`: Guides, reference, architecture, contributing, glossary

## 🧩 Features

- Manifest-driven catalog (see [Schema Reference](wp-content/mu-plugins/wp2-download/docs/schema.md))
- Secure Cloudflare R2 storage with presigned URLs
- GitHub Actions release automation
- Health checks ([Health Checks](wp-content/mu-plugins/wp2-download/docs/health-checks.md))
- Licensing, analytics, update channels
- REST API endpoints ([API Reference](wp-content/mu-plugins/wp2-download/docs/api-reference.md))
- Service Locator pattern (PSR-4: `WP2\Download\`)

## 🖥️ Compatibility

- WordPress 6.0+
- PHP 7.4+
- Supports native WP update flows for plugins, themes, and must-use packages

## 🔒 Security

- Secrets management ([SECURITY.md](wp-content/mu-plugins/wp2-download/docs/SECURITY.md))
- IAM, HTTPS, GitHub Actions secrets
- Ingest token for release API

## 🗺️ Roadmap

- Multi-origin support ([Origins Reference](wp-content/mu-plugins/wp2-download/docs/origins.md))
- Advanced health checks
- Custom analytics adapters
- Improved admin UI

## 🩺 Health Checks

See [Health Checks](wp-content/mu-plugins/wp2-download/docs/health-checks.md) for catalog and implementation details.

## 🆘 Support

- [Usage Guide](wp-content/mu-plugins/wp2-download/docs/usage-guide.md)
- [Client Plugin Guide](wp-content/mu-plugins/wp2-download/docs/client-plugin-guide.md)
- [Client Updater Guide](wp-content/mu-plugins/wp2-download/docs/client-updater-guide.md)
- [Install Guide](wp-content/mu-plugins/wp2-download/docs/install-guide.md)
- [Architecture](wp-content/mu-plugins/wp2-download/docs/architecture.md)
- [API Reference](wp-content/mu-plugins/wp2-download/docs/api-reference.md)
- [Glossary](wp-content/mu-plugins/wp2-download/docs/GLOSSARY.md)

## 📝 Contributing

See [Contributing Guide](wp-content/mu-plugins/wp2-download/docs/CONTRIBUTING.md) for how to get involved.

## 📚 Documentation Index

See [Documentation Index](wp-content/mu-plugins/wp2-download/docs/index.md) for a full list of guides and references.

## 🏷️ Versioning & Channels

See [Versioning Policy](wp-content/mu-plugins/wp2-download/docs/VERSIONING.md) for release and update channel details.
