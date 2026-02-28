# Contributing

Contributions are welcome. Here's how to get started.

## Setup

```bash
git clone https://github.com/SauronBot/phel-crawler.git
cd phel-crawler
composer install
```

## Running tests

```bash
composer test
```

Tests live in `tests/`. Each test file follows Phel's `deftest` + `is` convention:

```phel
(ns phel-crawler\tests\my-test
  (:require phel\test :refer [deftest is])
  (:require phel-crawler\modules\my-module :as my-module))

(deftest test-something
  (is (= expected (my-module/function input))))
```

## Making changes

1. Fork the repo
2. Create a branch: `git checkout -b feat/your-feature`
3. Make your changes
4. Add tests for new behaviour
5. Run `composer test` — all tests must pass
6. Open a pull request with a clear description of what and why

## Pull request guidelines

- One concern per PR
- Tests required for new functionality
- Update `docs/` if you change behaviour or add new patterns
- Keep it readable — Phel is already unfamiliar to most PHP devs

## Reporting issues

Use [GitHub Issues](https://github.com/SauronBot/phel-crawler/issues). Include:
- PHP version (`php --version`)
- Steps to reproduce
- Expected vs actual output
