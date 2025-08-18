# Versioning & Channels Policy

This document describes versioning strategies and channel policies for WP2 Download.

## Versioning Strategies
- **auto:** Hub resolves latest version per policy/channel.
- **pinned:** Specific version (e.g., `1.2.3`).
- **range/constraint:** Composer-style constraints (e.g., `^1.2`, `6.*`).

## Channels
- Supported channels: `stable`, `beta`, `rc`, custom.
- Use `channels` array in manifest to define available channels.
- `default_channel` sets the default for auto updates.

## Promoting Releases
- Update manifest to promote a version to a channel.
- Reload hub to apply changes.

## Policy
- Only promote tested releases to `stable`.
- Use `beta` and `rc` for pre-release testing.
