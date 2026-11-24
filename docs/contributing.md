---
title: Contributing
description: How to contribute to payment-simulator - checks, tests, examples and this documentation.
---

# Contributing

Pull requests are welcome on [tomaj/payment-simulator](https://github.com/tomaj/payment-simulator). The project
policy is [CONTRIBUTING.md](https://github.com/tomaj/payment-simulator/blob/master/CONTRIBUTING.md); participation
follows the [code of conduct](https://github.com/tomaj/payment-simulator/blob/master/CODE_OF_CONDUCT.md). Report
vulnerabilities in private, see [SECURITY.md](https://github.com/tomaj/payment-simulator/blob/master/SECURITY.md).

## Checks

```bash
composer install
composer check                                             # code style, PHPStan, Rector, tests
OPENSSL_CONF=docker/php/openssl-legacy.cnf composer test   # include the DES tests
```

CI runs the tests on PHP 8.4, 8.5 and 8.6 (8.7 nightly may fail), requires 90 % statement coverage and builds the
Docker image.

## A change to a gateway

A signature or a return URL is a protocol: a change breaks every client that follows the bank. Compare the pages
before and after on the same requests, check the values against an independent implementation (the `openssl` CLI,
`tests/Model/GatewaySignTest.php`), and keep `tests/Functional/OmnipayLibrariesTest.php` green.

## Examples

The [examples](/examples/) are clients in `examples/<name>/` (`run.php`, `e2e.sh`, `expected-output.txt`). The
Examples workflow serves the pull request's code and runs every one of them. Run one locally with
`examples/ci/example.sh <name>`; after a deliberate change of the output, run the example and commit the new
`expected-output.txt`.

## This documentation

The site is VitePress in `docs/`; the code on its pages is included from `examples/`, and `tests/DocsSnippetsTest.php`
runs the PHP blocks of README.md and `docs/`.

```bash
cd docs
npm ci
npm run docs:dev     # local preview
npm run docs:links   # build and check every link, as CI does
npm run images       # after a change to scripts/illustrations.py: SVGs, og.png, icons (needs Chrome)
```

The logo, the hero illustration, the diagram and the social card are generated from `docs/scripts/illustrations.py`;
edit the script, not the SVGs.
