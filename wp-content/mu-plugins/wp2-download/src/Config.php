<?php

/**
 * Central configuration constants for WP2 Download.
 *
 * Provides post types, meta keys, and post status used throughout the package hub.
 *
 * @package WP2 Download
 */

namespace WP2\Download;

/**
 *
 * WP2 Archi
 *
 * @component_id config
 * @namespace core
 * @type Service
 * @title "Configuration Service"
 * @facets [
 *   { "name": "WP2_POST_TYPE_PLUGIN",     "visibility": "+", "classifier": "const", "returnType": "string" },
 *   { "name": "WP2_POST_TYPE_THEME",      "visibility": "+", "classifier": "const", "returnType": "string" },
 *   { "name": "WP2_POST_TYPE_MU",         "visibility": "+", "classifier": "const", "returnType": "string" },
 *   { "name": "WP2_META_VERSION",         "visibility": "+", "classifier": "const", "returnType": "string" },
 *   { "name": "WP2_META_R2_FILE_KEY",     "visibility": "+", "classifier": "const", "returnType": "string" },
 *   { "name": "WP2_META_CHANNEL",         "visibility": "+", "classifier": "const", "returnType": "string" },
 *   { "name": "WP2_POST_STATUS_PUBLISH",  "visibility": "+", "classifier": "const", "returnType": "string" }
 * ]
 * @note Central configuration constants for WP2 Download.
 **/
class Config {

	// Post types.
	public const WP2_POST_TYPE_PLUGIN = 'wp2_plugin';
	public const WP2_POST_TYPE_THEME = 'wp2_theme';
	public const WP2_POST_TYPE_MU = 'wp2_mu';
	public const WP2_POST_TYPE_PLUGIN_REL = 'wp2_plugin_rel';
	public const WP2_POST_TYPE_THEME_REL = 'wp2_theme_rel';
	public const WP2_POST_TYPE_MU_REL = 'wp2_mu_rel';

	// Meta keys.
	public const WP2_META_VERSION = 'wp2_version';
	public const WP2_META_R2_FILE_KEY = 'wp2_r2_file_key';
	public const WP2_META_CHANNEL = 'wp2_channel';

	// Post status.
	public const WP2_POST_STATUS_PUBLISH = 'publish';
}
