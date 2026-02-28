# PHP Interoperability in Phel

This document covers the PHP interop patterns used in phel-crawler, with real examples from the codebase. Useful as a reference for anyone building Phel projects that use PHP libraries.

## Calling PHP functions

Use the `php/` prefix:

```phel
(php/strlen "hello")           ; => 5
(php/str_starts_with url "https://")
(php/parse_url base \PHP_URL_HOST)
(php/microtime true)
(php/count my-php-array)
```

## Instantiating PHP classes

```phel
(:use GuzzleHttp\Client)

(php/new Client
  (php/array
    "timeout" 10
    "headers" (php/array "User-Agent" "MyCrawler/1.0")))
```

## Calling instance methods

Use `php/->` with the method as a list:

```phel
(let [response (php/-> client (get url))
      status   (php/-> response (getStatusCode))
      body     (php/-> (php/-> response (getBody)) (getContents))]
  ...)
```

Chain calls by nesting, or (better) split with `let`:

```phel
; Avoid deep nesting — use let instead:
(let [body-stream (php/-> response (getBody))
      body        (php/-> body-stream (getContents))]
  body)
```

## Calling static methods

Use `php/::`:

```phel
(:use GuzzleHttp\HandlerStack)

(php/:: HandlerStack (create (php/new StreamHandler)))
```

## Reading from PHP arrays

PHP arrays returned by PHP functions are not Phel maps. Use `php/aget`:

```phel
(let [parsed (php/parse_url url)
      host   (php/aget parsed "host")
      scheme (php/aget parsed "scheme")]
  ...)
```

## Writing to PHP arrays

Use `php/aset`:

```phel
(php/aset my-array "key" "value")
```

## Creating PHP arrays

```phel
(php/array "key1" "val1" "key2" "val2")
; Indexed:
(php/array "foo" "bar" "baz")
```

## Accessing PHP superglobals

```phel
php/$_SERVER   ; => $_SERVER
php/$argv      ; => $argv (may not be set in all contexts)
(php/aget php/$_SERVER "argv")  ; safe way to get argv
```

## PHP constants

Prefix with `\`:

```phel
(php/parse_url url \PHP_URL_HOST)
(php/parse_url url \PHP_URL_SCHEME)
```

## try/catch

```phel
(try
  (let [res (php/-> client (get url))]
    {:ok true :body (php/-> (php/-> res (getBody)) (getContents))})
  (catch \Throwable e
    {:ok false :error (php/-> e (getMessage))}))
```

Use `\Throwable` (not `\Exception`) to catch all PHP errors and exceptions.

## What doesn't work

**Phel functions as PHP closures.**
Symfony's `DomCrawler::each()` expects a `\Closure`. Passing a Phel `fn` doesn't satisfy this type check. Workaround: use `extract(['attr'])` instead of `each()`.

```phel
; Don't do this — Phel fn is not a \Closure:
(php/-> anchors (each (fn [node i] ...)))

; Do this instead — no closure needed:
(php/-> anchors (extract (php/array "href")))
```

**Guzzle async pools.**
Same issue — the `fulfilled`/`rejected` handlers expect `\Closure`. Use synchronous batches instead.

**`php/array_push` with `deref`.**
PHP arrays are pass-by-value. `(deref my-var)` returns a copy, so `php/array_push` on it doesn't persist. Use Phel's `var`/`swap!` with Phel collections (`conj`, `assoc`) instead.

```phel
; Wrong — push doesn't persist:
(php/array_push (deref my-array) item)

; Correct — use Phel persistent vector + swap!:
(def my-vec (var []))
(swap! my-vec conj item)
```

**`(drop n coll)` returns a lazy sequence.**
If you store the result in state and later try to `conj` onto it, it fails. Convert with `(into [] ...)`:

```phel
(let [remaining (into [] (drop n queue))] ...)
```
