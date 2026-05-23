# Automated tests

These tests catch common PHP 8 warnings and regressions in summary/progress report pages.

## Quick run (no Composer)

```bash
php tests/run_all.php
```

## Full suite (PHPUnit)

```bash
composer install
composer test
```

Or:

```bash
vendor/bin/phpunit
```

## What is covered

| Area | Tests |
|------|--------|
| `includes/report_helpers.php` | Branch ID resolution, date params, lab %, gender codes |
| `bk/includes/progress_report_params.php` | SQL subquery helpers |
| FR summary print pages | No leading whitespace, valid PHP syntax, no `$dr_name` strpos bug |
| BK daily branch report | Batch queries (no per-doctor N+1) |
| FR user summary flow | `http_build_query`, no broken `target="_blank"` |

## CI

GitHub Actions runs the same checks on every push/PR to `main` (see `.github/workflows/php-tests.yml`).
