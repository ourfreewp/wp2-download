#!/bin/bash
# Bump version and create a new release tag
set -e

# You would need to add a versioning tool like 'npm version' or 'lerna version' here.
# For example:
# npm version patch --no-git-tag-version

# Get the new version number (this is just an example)
new_version=$(node -p "require('./package.json').version")

# Create a Git tag
git add .
git commit -m "chore(release): Bump version to $new_version"
git tag -a "v$new_version" -m "Release $new_version"

# Push the new tag
git push --tags