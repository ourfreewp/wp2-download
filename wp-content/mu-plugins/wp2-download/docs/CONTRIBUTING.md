# Contributing Guide

This document describes how to propose new manifests, review gates, and CI validation for WP2 Download.

## Proposing a Manifest
- Fork the repository and create a new branch.
- Add your manifest to the appropriate directory (`mu-plugins`, `plugins`, `themes`).
- Validate your manifest against `schema.json`.
- Submit a pull request with a description of your package.

## Review Gates
- Manifest must pass schema validation.
- Manifest must include all required fields.
- Manifest must follow naming conventions.
- Manifest must not include secrets or credentials.

## CI Validation
- Automated checks validate manifest structure and required fields.
- Lint JSON and run schema validation in CI.

## Discussion
- Use GitHub Issues or Discussions for questions and feedback.
