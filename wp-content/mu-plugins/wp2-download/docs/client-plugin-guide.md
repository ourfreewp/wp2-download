

# Client Plugin Integration Guide

This guide explains how to integrate your plugin or theme with the WP2 Download Hub for updates and reporting.

## Integration Paths
- Use the update server API ([Install Guide](install-guide.md)).
- For advanced integration, use the updater classes ([Client Updater Guide](client-updater-guide.md)).

## Compatibility
- Minimum WP: 5.2+
- Minimum PHP: 7.4+

## Reporting
- Client updater reports active version, site URL, and environment weekly.

## What the Client Must Not Do
- Do not mirror artifacts.
- Respect license prompts and entitlements.

## FAQ
- **What happens if the update server is unreachable?**
	- Updates are skipped; plugin/theme continues running.
- **How long is update info cached?**
	- Default cache TTL is 12 hours.

## Further Reading
- [Install Guide](install-guide.md)
- [Client Updater Guide](client-updater-guide.md)
- [API Reference](api-reference.md)
