# Change Log
All notable changes to this project will be documented in this file.
Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [Unreleased][unreleased]

### Changed

- **BREAKING CHANGE**: Requires PHP 8.4 (`config.platform.php` 8.4.1, which PHPUnit 13 needs); the Docker image is based on `php:8.4-fpm`. Rebuild the image (`docker compose build`) ([#20](https://github.com/tomaj/payment-simulator/issues/20)).
- Dependencies updated within their constraints (`composer update`), among them `nette/neon` 4 and Symfony 8 components.
- Docker: Xdebug 3 settings (`xdebug.mode=debug`, trigger mode, port 9003; set `client_host` through `XDEBUG_CONFIG`), the removed `opcache.fast_shutdown` dropped, the obsolete `version:` key dropped from the compose files ([#27](https://github.com/tomaj/payment-simulator/issues/27)).
- The sign classes are `final readonly` with typed, promoted constructors and a common `GatewaySign` interface; every class is `final`. Signatures and return URLs are unchanged (old and new pages compared on fixed requests) ([#21](https://github.com/tomaj/payment-simulator/issues/21)).
- PHPStan level max with the deprecation and PHPUnit rules, Rector, and the baseline composer scripts (`test`, `coverage`, `rector`, …) with descriptions ([#19](https://github.com/tomaj/payment-simulator/issues/19)).
- Requires PHP 8.3 - 8.6 (was PHP >= 7.2); the Docker image is based on `php:8.3-fpm` ([#15](https://github.com/tomaj/payment-simulator/pull/15)).
- Nette 3.2+/3.3 (application, bootstrap, forms, http, security, di), nette/utils 4, nette/robot-loader 4,
  Latte 3, Tracy 2.12, Guzzle 7 (`php-http/guzzle7-adapter` instead of `php-http/guzzle6-adapter`) ([#15](https://github.com/tomaj/payment-simulator/pull/15)).
- Removed the unused `nette/database`, `nette/caching`, `nette/mail` and `nette/finder` packages and the
  `database` section in `app/config/local.neon` ([#15](https://github.com/tomaj/payment-simulator/pull/15)).
- `moneyphp/money` (an `omnipay/common` dependency) is held at 3.x because 4.x does not allow PHP 8.6 yet ([#15](https://github.com/tomaj/payment-simulator/pull/15)).
- The AES and DES signatures use the signers of `tomaj/omnipay-tatra` 5 (`Omnipay\Core\Sign\Aes256Sign`, `DesSign`) through `App\Model\Sign\LibrarySign` instead of local copies of the 4.x signers, which called `openssl_random_pseudo_bytes(0)` and threw on PHP 8. Every signature and return URL is unchanged (old and new output compared on the test vectors). `tomaj/omnipay-tatra` is required as `dev-master` until 5.0.0 is tagged, then as `^5.0`. The library round-trip tests now cover TatraPay, CardPay and ComfortPay with AES and DES keys and TatraPay with a specific symbol on every key type ([#25](https://github.com/tomaj/payment-simulator/issues/25), [#15](https://github.com/tomaj/payment-simulator/pull/15)).
- `tomaj/omnipay-vub` and `tomaj/omnipay-slsp` are required as `dev-master` (4.0 in development, was `^3.0`) until 4.0.0 is tagged, then as `^4.0`; `omnipay-vub` comes from its GitHub repository (`repositories` in `composer.json`) because Packagist still serves a 2022 `dev-master`. The VÚB and SporoPay signers used by `LibrarySign` give the same signatures with the simulator's keys (the `openssl` CLI vectors pass unchanged). `OmnipayLibrariesTest` round-trips with VS, SS and amount verification: `completePurchase()` gets the order's `vs`, `ss` and `amount`, VÚB payments with a constant symbol and with a specific symbol (both with a 10-digit VS, which omnipay-vub 4 requires then), and returns that must be rejected (another order's VS or SS, digits moved between VS and SS, a tampered result for VÚB; another amount, a tampered `suma`, another VS or SS for SporoPay) ([#41](https://github.com/tomaj/payment-simulator/issues/41)).
- DES signing throws a `RuntimeException` when OpenSSL cannot compute it (an empty signature was shown before) ([#15](https://github.com/tomaj/payment-simulator/pull/15)).
- The Docker image enables the OpenSSL legacy provider (`OPENSSL_CONF`), which single DES needs on OpenSSL 3 ([#15](https://github.com/tomaj/payment-simulator/pull/15)).

### Fixed

- TatraPay AES and DES: the return URL is signed over `VS SS RES` instead of `VS RES` when the request carries a specific symbol, as Tatra banka's *Technická príručka k službe TatraPay* (version 3.3) specifies and `tomaj/omnipay-tatra` 5 verifies. Without `SS` the signature is unchanged. The docs no longer advise leaving `ss` empty, and the omnipay-tatra example pays with a specific symbol ([#34](https://github.com/tomaj/payment-simulator/issues/34)).
- CardPay Direct API: the 3-D Secure form posts to the port of the request too (it dropped `:8080` from `http://127.0.0.1:8080`) ([#30](https://github.com/tomaj/payment-simulator/issues/30)).
- Specific symbol (`SS`) ([#26](https://github.com/tomaj/payment-simulator/issues/26)):
  - TatraPay (AES, DES, HMAC) reads the optional `SS` and signs `MID AMT CURR VS SS CS RURL` (HMAC: `… REM TIMESTAMP`), the order `tomaj/omnipay-tatra` has used against Tatra banka in production (the TatraPay specification is not public); the HMAC return URL is signed over `AMT CURR VS SS CS RES TID TIMESTAMP` and carries `SS`. A request with `SS` showed "Wrong sign" before.
  - VÚB ePlatby signs `MID AMT VS SS CS RURL` (SS between VS and CS, the order VÚB confirmed, see tomaj/omnipay-vub#2; it was `… CS SS …`) and the return URL over `VS SS RES`, with `SS` in the return URL.
  - Without `SS` every signature is unchanged.

- The signature check on every payment page uses `hash_equals()` in the presenter instead of `==` in the templates, which treated two different numeric-looking signatures (`0E…` digits) as equal ([#21](https://github.com/tomaj/payment-simulator/issues/21)).
- A request without a required parameter shows "Missing parameters" with the list of names (HTTP 400) instead of an undefined-index error ([#21](https://github.com/tomaj/payment-simulator/issues/21)).
- CardPay Direct API: a body without a numeric `amount` returns 400; `tdsTermUrl` is HTML-escaped in the 3-D Secure form; the CardPay cancel XML escapes the echoed parameters ([#21](https://github.com/tomaj/payment-simulator/issues/21)).

- SporoPay return URL: `SporoPay3DesSign::returnUrlSign()` read the undefined `$rurl` property and failed ([#15](https://github.com/tomaj/payment-simulator/pull/15)).
- Missing optional `CS` / `REM` parameters no longer raise warnings (HTTP 500 in debug mode) ([#15](https://github.com/tomaj/payment-simulator/pull/15)).
- CardPay Direct API: a missing `Authorization` header returns 400 instead of a 500 `TypeError`; the HMAC signatures
  (Direct API, CardPay cancel) are compared with `hash_equals()` ([#15](https://github.com/tomaj/payment-simulator/pull/15)).
- Template parameter dumps are escaped (`{print_r($params, true)}` instead of a raw `{php print_r()}`) ([#15](https://github.com/tomaj/payment-simulator/pull/15)).

### Added

- Documentation site at https://tomaj.github.io/payment-simulator/: getting started, running it, configuration and test keys, every endpoint of every bank with its parameters, signed fields and return, using it with Omnipay, troubleshooting, FAQ and security; own logo, illustrations and diagram. Six examples in `examples/` (omnipay-tatra, a plain PHP client, VÚB e-platby, SLSP SporoPay, the CardPay Direct API, CardPay capture and refund) run against the simulator built from each pull request in the Examples workflow; `examples/ci/serve.sh` starts the simulator for them and for other repositories ([#30](https://github.com/tomaj/payment-simulator/issues/30)).
- README: running with Docker and with local PHP, configuration, the test keys, how to point the omnipay gateways at the simulator, every endpoint with its parameters, signed fields and return URL, and troubleshooting ([#23](https://github.com/tomaj/payment-simulator/issues/23)).
- PHPUnit test suite: every gateway signature and return URL against vectors computed with the `openssl` CLI; the whole app run in-process for every page (correct, wrong, wrong-case signature, missing parameters), CardPay cancel and the Direct API; round trips with the released `tomaj/omnipay-tatra`, `omnipay-vub` and `omnipay-slsp` gateways in test mode ([#22](https://github.com/tomaj/payment-simulator/issues/22)).
- `LICENSE.md` (MIT); `composer.json` declares `MIT` instead of the four licences of the Nette sandbox skeleton the project started from. `CONTRIBUTING.md`, `CODE_OF_CONDUCT.md`, `SECURITY.md`, `RELEASING.md`, issue forms and a pull request template ([#17](https://github.com/tomaj/payment-simulator/issues/17)).
- GitHub Actions CI (PHP 8.3 - 8.6, nightly 8.7): PHP and Latte lint, DI container boot, PHP-CS-Fixer (PER-CS),
  PHPStan level 5 with strict rules, `composer audit`, Dependabot ([#15](https://github.com/tomaj/payment-simulator/pull/15)).
- CI at the shared baseline: tests on PHP 8.4, 8.5, 8.6 (coverage with a 90% threshold on 8.6) and nightly 8.7, all with the OpenSSL legacy provider so the DES tests cannot skip; Rector; a Docker job that builds the image, checks its extensions and single DES, starts the stack with `docker compose` and verifies a DES page; CodeQL, OpenSSF Scorecard and a tag-driven release workflow ([#18](https://github.com/tomaj/payment-simulator/issues/18), [#27](https://github.com/tomaj/payment-simulator/issues/27)).
- Slovak translation of the documentation site ([#37](https://github.com/tomaj/payment-simulator/issues/37)).

[unreleased]: https://github.com/tomaj/payment-simulator/commits/master
