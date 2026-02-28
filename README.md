# phel-crawler

[![CI](https://github.com/SauronBot/phel-crawler/actions/workflows/ci.yml/badge.svg)](https://github.com/SauronBot/phel-crawler/actions)
[![PHP >=8.4](https://img.shields.io/badge/PHP-%3E%3D8.4-blue)](https://www.php.net/)
[![Phel](https://img.shields.io/badge/Phel-lang-purple)](https://phel-lang.org)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

A web crawler CLI written in [Phel](https://phel-lang.org) — a Lisp that compiles to PHP.

Built to explore PHP interoperability in Phel using real-world libraries:
- **[Guzzle](https://docs.guzzlephp.org)** for HTTP
- **[Symfony DomCrawler](https://symfony.com/doc/current/components/dom_crawler.html)** for link extraction

## Features

- BFS (breadth-first) crawl with configurable depth and page limits
- Batched concurrent requests
- Same-domain filtering by default
- Normalizes relative, absolute, and protocol-relative URLs
- Skips anchors, `mailto:`, and `javascript:` links automatically

## Requirements

- PHP >= 8.4 with extensions: `curl`, `xml`, `mbstring`, `dom`
- [Composer](https://getcomposer.org/)

## Install

```bash
git clone https://github.com/SauronBot/phel-crawler.git
cd phel-crawler
composer install
```

## Usage

```bash
php vendor/bin/phel run src/main.phel <url> [options]
```

### Options

| Option | Default | Description |
|---|---|---|
| `--max-pages N` | 50 | Maximum pages to crawl |
| `--max-depth N` | 2 | Maximum link depth |
| `--concurrency N` | 5 | Requests per batch |
| `--timeout N` | 10 | Request timeout in seconds |
| `--all-domains` | off | Follow links to external domains |

### Examples

```bash
# Basic crawl
php vendor/bin/phel run src/main.phel https://example.com

# Shallow, more pages
php vendor/bin/phel run src/main.phel https://bashunit.typeddevs.com \
  --max-pages 100 --max-depth 1

# Deep, high concurrency
php vendor/bin/phel run src/main.phel https://phel-lang.org \
  --max-pages 50 --max-depth 3 --concurrency 10

# Follow all domains
php vendor/bin/phel run src/main.phel https://example.com \
  --all-domains --max-pages 20
```

### Output

```
Crawling: https://bashunit.typeddevs.com
  max-pages=15  max-depth=2  concurrency=5
------------------------------------------------------------------------
  [200] d=0  bashunit - A simple testing library for bash scripts
             https://bashunit.typeddevs.com
  [200] d=1  Quickstart | bashunit
             https://bashunit.typeddevs.com/quickstart
  [200] d=2  Assertions | bashunit
             https://bashunit.typeddevs.com/assertions
------------------------------------------------------------------------

Done in 0.91s  |  pages: 15  visited: 15
```

## Project structure

```
src/
  main.phel           CLI entry point — arg parsing and output
  modules/
    http.phel         Guzzle HTTP client wrapper
    parser.phel       Link extraction, URL normalization, title parsing
    crawler.phel      BFS crawl engine
tests/
  parser-test.phel    Unit tests for the parser module
docs/
  architecture.md     How the crawler works internally
  phel-interop.md     PHP interoperability patterns used in this project
  examples.md         Annotated real-world usage examples
```

## Running tests

```bash
composer test
# or
vendor/bin/phel test
```

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md).

## License

MIT — see [LICENSE](LICENSE).
