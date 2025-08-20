#!/bin/bash
# Release script for WP2 Download
# Automates tagging and pushing releases. Extend for GitHub API integration if needed.
version=$(cat VERSIONING.md)
git tag -a "v$version" -m "Release $version"
git push --tags
# Add GitHub CLI or API calls for release if needed