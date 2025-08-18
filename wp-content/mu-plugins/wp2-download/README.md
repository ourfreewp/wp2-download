# ☁️ WP2 Download & Update Hub

This is a self-contained WordPress mu-plugin that serves as an automated hub for distributing and monitoring software packages. It provides a comprehensive, hands-off solution for managing the entire lifecycle of your plugins and themes, from GitHub to your client sites.

---

## 🏗️ Architecture Overview

The hub is designed to be a single pane of glass for your software distribution pipeline. The workflow is seamless and highly automated:

- **Source of Truth:** A `data/packages` directory contains `manifest.json` files that serve as the canonical catalog of all packages managed by the hub.
- **Release Trigger:** A developer pushes a new version tag (e.g., `v1.2.3`) to a managed GitHub repository.
- **Build & Store:** A GitHub Actions workflow automatically packages the release into a `.zip` artifact and uploads it to a private Cloudflare R2 bucket.
- **Register:** The workflow securely notifies the hub via a dedicated API endpoint. The hub then registers the new release, linking the version to its R2 artifact.
- **Monitor & Enrich:** Asynchronous background health checks continuously run to enrich package data, verify R2 artifacts, and track new releases.
- **Distribute:** Client-side plugins and themes periodically check the hub's public API for updates and download new versions via a secure, expiring URL gateway.
- **Report:** Client sites "phone home" on a weekly schedule, providing valuable usage data on which versions are currently active.

---

## ✨ Core Features

- **Manifest-Driven Catalog:** All packages are pre-registered via version-controlled `manifest.json` files for a reliable and auditable source of truth.
- **Automated Release Pipeline:** A push-to-deploy workflow handles versioning, packaging, and registration automatically using GitHub Actions.
- **Secure R2 Download Gateway:** Packages are served securely from a private R2 bucket using expiring, pre-signed URLs.
- **Full Lifecycle Health Monitoring:**
  - **GitHub Checks:** Automatically detects new tags and enriches package data with repository statistics and pending release notifications.
  - **R2 Artifact Verification:** Audits every registered release to ensure a corresponding `.zip` file exists in cloud storage, alerting you to any missing artifacts.
  - **Client-Site Reporting:** Provides real-time visibility into your user base by tracking which package versions are active on client websites.
- **Self-Updating:** The hub application can update itself using its own infrastructure, ensuring it's always running the latest code.
- **Robust Error Handling:** AJAX handlers and health check logic provide structured, descriptive error messages and input validation for improved reliability.
- **Extensible Licensing & Analytics:** The system includes adapters for licensing and analytics, allowing for easy integration with external services. It is permissive by default, but can be configured for stricter enforcement and detailed tracking.

---

## 🛠️ Installation & Setup

This guide covers the one-time server setup required to get the hub running.

### 1. Server Setup

First, add the following constants to your `wp-config.php` file. Replace the placeholder values with your actual credentials:

```php
define('WP2_DOWNLOAD_R2_ACCOUNT_ID', 'your-r2-account-id');
define('WP2_DOWNLOAD_R2_ACCESS_KEY', 'your-r2-access-key');
define('WP2_DOWNLOAD_R2_SECRET_KEY', 'your-r2-secret-key');
define('WP2_DOWNLOAD_R2_BUCKET', 'your-r2-bucket-name');
define('WP2_GITHUB_PAT', 'your-github-personal-access-token');
define('WP2_HUB_INGEST_TOKEN', 'your-strong-unique-ingest-token');
```

> **WP2_HUB_INGEST_TOKEN:** This token is crucial for securing the `/ingest-release` API endpoint. Generate a strong, unique value and keep it secret.

- **Initial Upload:** Manually upload the `wp2-download.php` loader file and the entire `wp2-download` directory (including the `vendor` folder) to your `wp-content/mu-plugins/` directory via SFTP or SSH.
- **Permalinks:** After installation, log in to your WordPress admin panel and navigate to **Settings > Permalinks**. Simply visiting this page will flush the rewrite rules and activate the custom API endpoints.

### 2. GitHub Repository Setup

In the GitHub repository for each of your managed packages, you need to add secrets for your hub's credentials. These secrets will be used by the GitHub Actions workflow to communicate with the hub.

1. Go to **Settings > Secrets and variables > Actions** in your repository.
2. Add the following secrets, using the same values you set in your `wp-config.php` file:
   - `WP2_DOWNLOAD_R2_ACCOUNT_ID`
   - `WP2_DOWNLOAD_R2_ACCESS_KEY`
   - `WP2_DOWNLOAD_R2_SECRET_KEY`
   - `WP2_HUB_INGEST_TOKEN`

---

## 🚀 API Endpoints

The hub exposes several REST API endpoints for managing and interacting with packages.

| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/wp-json/wp2/v1/ingest-release` | Used by GitHub Actions to register a new release. |
| `GET`  | `/wp-json/wp2/v1/packages/{type}/{slug}` | Checks for the latest available version of a package. |
| `GET`  | `/wp2-download/{type}/{slug}/{version}` | Provides a secure download URL for a specific package version. |
| `POST` | `/wp-json/wp2/v1/report-in` | Receives usage reports from client sites. |
| `POST` | `/wp-json/wp2/v1/packages/{type}/{slug}/run-health-check` | Manually triggers a health check for a single package. |
