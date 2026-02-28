# phel-crawler

A web crawler CLI written in [Phel](https://phel-lang.org) — a Lisp that compiles to PHP.

Uses [Guzzle](https://docs.guzzlephp.org) for HTTP and [Symfony DomCrawler](https://symfony.com/doc/current/components/dom_crawler.html) for link extraction.

## Requirements

- PHP 8.3+
- Composer
- `php-curl`, `php-xml`, `php-mbstring`

## Install

```bash
composer install
```

## Usage

```bash
php vendor/bin/phel run src/main.phel <url> [options]
```

### Options

```
--max-pages N    Max pages to crawl     (default: 50)
--max-depth N    Max link depth          (default: 2)
--concurrency N  Requests per batch      (default: 5)
--timeout N      Request timeout seconds (default: 10)
--all-domains    Follow links to other domains
```

### Examples

```bash
# Crawl up to 50 pages, 2 levels deep
php vendor/bin/phel run src/main.phel https://example.com

# Aggressive crawl
php vendor/bin/phel run src/main.phel https://bashunit.typeddevs.com --max-pages 100 --max-depth 3 --concurrency 10

# Follow all domains
php vendor/bin/phel run src/main.phel https://example.com --all-domains --max-pages 20
```

### Sample output

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
  main.phel                  CLI entry point, arg parsing, output
  modules/
    http.phel                Guzzle HTTP client wrapper
    parser.phel              DomCrawler link extraction + URL normalization
    crawler.phel             BFS crawl engine
```

## How it works

- **BFS** (breadth-first) crawl with configurable depth and page limits
- Same-domain filtering by default (use `--all-domains` to disable)
- Skips anchors, mailto, javascript links automatically
- Normalizes relative, absolute, and protocol-relative URLs
- Stops cleanly when limits are hit mid-batch
