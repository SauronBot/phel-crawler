# Changelog

All notable changes to this project will be documented here.

Format follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [Unreleased]

## [0.1.0] - 2026-02-28

### Added
- BFS web crawler CLI in Phel
- HTTP module using Guzzle
- HTML parser module using Symfony DomCrawler
- URL normalization (relative, absolute, protocol-relative)
- Same-domain filtering
- Configurable max-pages, max-depth, concurrency, timeout
- `--all-domains` flag to follow external links
- Unit tests for the parser module
- CI via GitHub Actions (PHP 8.3)
- Docs: architecture, PHP interop patterns, usage examples
