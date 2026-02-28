# Usage Examples

Practical examples for common crawling scenarios.

## Site map extraction

Crawl a site and print all discovered URLs — useful for building sitemaps or auditing structure.

```bash
php vendor/bin/phel run src/main.phel https://phel-lang.org \
  --max-pages 200 \
  --max-depth 4 \
  --concurrency 10
```

## Shallow link audit

Check all top-level links from a homepage — fast, low depth.

```bash
php vendor/bin/phel run src/main.phel https://bashunit.typeddevs.com \
  --max-pages 50 \
  --max-depth 1 \
  --concurrency 5
```

## Broken link detection

Crawl with a low timeout to surface slow or dead pages quickly. Any non-200 status will show in the output.

```bash
php vendor/bin/phel run src/main.phel https://example.com \
  --max-pages 100 \
  --max-depth 2 \
  --timeout 5
```

## Cross-domain crawl

Follow links beyond the starting domain — useful for exploring link graphs.

```bash
php vendor/bin/phel run src/main.phel https://example.com \
  --all-domains \
  --max-pages 30 \
  --max-depth 1
```

## Using as a Phel library

Import `crawler/crawl` directly from another Phel namespace:

```phel
(ns my-app\main
  (:require phel-crawler\modules\crawler :as crawler))

(crawler/crawl "https://example.com"
  {:max-pages 10 :max-depth 2 :concurrency 5 :timeout 10 :same-domain true}
  ; called for each page crawled
  (fn [page]
    (println (str (get page :status) " " (get page :url))))
  ; called when done
  (fn [summary]
    (println (str "Total: " (get summary :pages)))))
```

The `on-page` callback receives:

```phel
{:url    "https://example.com/about"
 :depth  1
 :status 200
 :title  "About Us"}
```

The `on-done` callback receives:

```phel
{:pages   42     ; pages successfully fetched
 :visited 47}    ; URLs attempted (includes errors)
```

## Extending the crawler

To add custom filtering (e.g. skip PDFs, only crawl `/docs/`):

1. Add a filter step in `crawler.phel`'s `crawlable?` function
2. Or pre-filter links in `enqueue-new-links` using `parser/normalize-url` output

Example — only crawl URLs matching a path prefix:

```phel
(defn- docs-only? [url]
  (php/str_starts_with (php/parse_url url \PHP_URL_PATH) "/docs"))

; Then add to crawlable?:
(and (crawlable? ...) (docs-only? url))
```
