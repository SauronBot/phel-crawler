# Architecture

## Overview

phel-crawler is a BFS (breadth-first search) web crawler. It starts at a given URL, fetches the page, extracts links, and enqueues them for the next depth level. It stops when it hits the page limit, depth limit, or runs out of URLs.

```
main.phel
  └─ parses args
  └─ calls crawler/crawl
        └─ http/fetch-url     (Guzzle)
        └─ parser/extract-links  (Symfony DomCrawler)
        └─ parser/normalize-url
        └─ on-page callback (user-supplied)
        └─ on-done callback (user-supplied)
```

## Modules

### `modules/http.phel`

Thin wrapper around Guzzle. Creates a client with sensible defaults (timeout, redirects, user-agent) and exposes `fetch-url`, which returns a Phel map:

```phel
{:ok true :url "..." :status 200 :body "..." :content-type "text/html"}
; or on error:
{:ok false :url "..." :error "Connection refused"}
```

All errors are caught — the crawler never crashes on a bad URL.

### `modules/parser.phel`

Uses Symfony DomCrawler for HTML parsing. Key functions:

- `extract-links` — finds all `<a href>` attributes via `filter("a[href]")` + `extract(["href"])`. Returns a Phel vector of raw href strings.
- `normalize-url` — resolves relative, absolute-path, and protocol-relative URLs against a base. Returns `nil` for anchors, `mailto:`, `javascript:`.
- `extract-title` — extracts `<title>` text.
- `is-html?` — checks `Content-Type` header.
- `same-domain?` — compares hosts via `php/parse_url`.

### `modules/crawler.phel`

The BFS engine. State is managed with Phel's `var`/`swap!` (mutable references):

```phel
state = {:visited #{}      ; Phel set of visited URLs
         :queue   [...]    ; Phel vector of [url depth] pairs
         :results [...]}   ; Phel vector of result maps
```

Each iteration:
1. Takes a batch of up to `concurrency` items from the queue
2. For each item, checks `crawlable?` (not visited, within limits, same domain)
3. Fetches and parses via `process-url`
4. Enqueues new links via `enqueue-new-links`
5. Calls the `on-page` callback for each successful fetch
6. Calls `on-done` when finished

`swap!` is used throughout to update state immutably — `process-url` takes the current state and returns a new state, which `swap!` stores.

## Key design decisions

**Why `var`/`swap!` instead of pure recursion?**
The BFS queue grows and shrinks dynamically. Managing it with pure immutable recursion would require threading state through every call. `var`/`swap!` gives us a clean shared mutable reference without PHP's pass-by-value array pitfalls.

**Why not async?**
Guzzle's async pool requires closures as PHP callables, which Phel functions don't satisfy as native `\Closure` instances. The current design uses synchronous batches — grouping N requests before moving to the next batch achieves practical concurrency without async complexity.

**Why Symfony DomCrawler instead of regex?**
Reliable HTML parsing. `extract(['href'])` on a filtered node set is both correct and fast — no closure required, avoiding the PHP-callable compatibility issue.
