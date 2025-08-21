# Copilot Instructions for wp2-download

## Project Overview

WP2 Download is a manifest-driven package hub for WordPress, enabling secure, scalable distribution of plugins, themes, and must-use packages. It integrates with native WordPress update flows and supports automated releases, health checks, analytics, licensing, and REST APIs.

## Architecture & Key Patterns

- **Service Locator Pattern:**
  - Centralized in `src/Services/Locator.php`.
  - Resolves adapters for analytics, licensing, storage, development, and origin sources.
  - Adapters are discovered via directory scan and support lazy instantiation.
  - Unified origin keys: `composer`, `github`, `gdrive`, `storage`, `wporg`.
- **Extensions & Adapters:**
  - Modular adapters for third-party integrations in `src/Extensions/*`.
  - Each adapter implements `ConnectionInterface` for predictable interaction.
  - Example: Storage adapters in `src/Extensions/Storage/` (e.g., Cloudflare R2).
- **Manifest Catalog:**
  - Package manifests in `data/packages/{mu-plugins,plugins,themes}/`.
  - Each manifest references a shared schema and describes origin, version, and metadata.
  - Example manifest fields: `$schema`, `name`, `slug`, `type`, `version`, `origin`, `meta`.
- **REST API:**
  - Endpoints in `src/REST/` for packages, origins, jobs, health, etc.
- **Admin UI:**
  - Views in `src/Views/Admin/`.

## Developer Workflows

- **Build/Autoload:**
  - Use Composer for autoloading: `composer dump-autoload`.
- **Testing:**
  - Tests in `tests/` (unit, feature, architecture).
  - Run with PHPUnit or Pest: `vendor/bin/phpunit` or `vendor/bin/pest`.
- **Coding Standards:**
  - PHPCS config in `phpcs.xml`.
  - Run: `vendor/bin/phpcs` and auto-fix: `vendor/bin/phpcbf`.
- **Release Automation:**
  - GitHub Actions for releases and health checks.

## Conventions & Integration Points

- **Adapters:**
  - Place new adapters in the appropriate `src/Extensions/{Service}/Adapters/` directory.
  - Implement `ConnectionInterface` for service compatibility.
- **Manifests:**
  - Follow schema in `data/packages/schema.json`.
  - Use correct origin type and metadata fields.
- **Security:**
  - Secrets managed via GitHub Actions and IAM.
  - Ingest token required for release API.

## References

- [README.md](../../README.md)
- [docs/architecture.md](../../docs/architecture.md)
- [docs/api-reference.md](../../docs/api-reference.md)
- [docs/schema.md](../../docs/schema.md)
- [docs/health-checks.md](../../docs/health-checks.md)

---

**Feedback:** If any section is unclear or missing, please specify so it can be improved for future AI agents.

## PHP Tags

Always use the full PHP opening tag `<?php` and closing tag `?>`.  
Avoid short tags (`<?`), as they are often disabled and can break code.

## Code Alignment

When declaring multiple variables in a row, align the equals signs for readability.

```php
// Incorrect
$variable1 = 'value';
$long_variable_name = 'another value';

// Correct
$variable1          = 'value';
$long_variable_name = 'another value';
```

## Ternary Operators

Avoid short ternary operators (`?:`).
Always use the full ternary form:

```php
$foo = $condition ? 'yes' : 'no';
```

## Yoda Conditions

Write conditions with the literal value on the left.
This prevents accidental assignments.

```php
// Incorrect
if ( $variable == true ) {}

// Correct
if ( true == $variable ) {}
```

## Inline Comments

End inline comments with a period, exclamation mark, or question mark.

```php
// Incorrect: This is a comment
// Correct: This is a comment.
```

## File Endings

Every file must end with a blank newline.

## Escaping Output

Do not use `htmlspecialchars`.
Use WordPress-specific escaping functions:
• `esc_html()` for HTML
• `esc_attr()` for attributes
• `esc_url()` for URLs

```php
// Incorrect
echo htmlspecialchars( $variable );

// Correct
echo esc_html( $variable );
```

## WordPress Functions

Prefer WordPress-specific helpers over PHP natives.
For example, use `wp_json_encode()` instead of `json_encode()`.

# Amendment: ABSPATH Guards & PSR-1 Side Effects

## Rules

- **Symbols-only files (`src/…`)**: _Do not_ include ABSPATH guards.
- **Bootstrap/init/templates**: Include ABSPATH guard and allow side effects (hooks, execution).
- **If mixing is unavoidable**: Add a one-line PHPCS ignore on the guard only.
- **Placement**: Namespace first, then `defined( 'ABSPATH' ) || exit;`.
- **Alt templates**: Use PHP’s alternative control-structure syntax in templates.

## Patterns

### 1 Symbols-only (classes, functions, constants)

```php
<?php
namespace WP2\Feature\Service;

final class Loader {
	public function init(): void {}
}
```

Bootstrap / init with side effects

```php
<?php
namespace WP2\Feature;

defined( 'ABSPATH' ) || exit;

add_action( 'init', static function (): void {
	// Register hooks, kick off services, etc.
} );

```

Mixed (temporary) with PHPCS exception

```php
<?php
namespace WP2\Feature;

// phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols
defined( 'ABSPATH' ) || exit;

final class Controller {
	public function register(): void {}
}
```

PHPCS Configuration Options

Exclude side-effects sniff for symbols-only path

```xml
<?xml version="1.0"?>
<ruleset name="WP2 Coding Standards">
	<rule ref="WordPress" />
	<file>wp-content/mu-plugins/wp2-download/src</file>

	<rule ref="PSR1.Files.SideEffects">
		<exclude-pattern>wp-content/mu-plugins/wp2-download/src/*</exclude-pattern>
	</rule>
</ruleset>
```

Keep the sniff globally; allow narrow per-line ignores (preferred)

```php
// phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols
defined( 'ABSPATH' ) || exit;
```

Checklist
• Symbols in src/… only; no side effects.
• Guards only in bootstrap/init/templates.
• Namespace → ABSPATH guard (when present) → code.
• Use precise, one-line PHPCS ignores when necessary.
