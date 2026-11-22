# Changelog

## Unreleased

### Changed

- Requires PHP 8.3 - 8.6 (was PHP >= 7.2); the Docker image is based on `php:8.3-fpm`.
- Nette 3.2+/3.3 (application, bootstrap, forms, http, security, di), nette/utils 4, nette/robot-loader 4,
  Latte 3, Tracy 2.12, Guzzle 7 (`php-http/guzzle7-adapter` instead of `php-http/guzzle6-adapter`).
- Removed the unused `nette/database`, `nette/caching`, `nette/mail` and `nette/finder` packages and the
  `database` section in `app/config/local.neon`.
- `moneyphp/money` (an `omnipay/common` dependency) is held at 3.x because 4.x does not allow PHP 8.6 yet.
- The AES and DES signatures use local copies of the omnipay-tatra 4.x signers (`App\Model\Sign`): the library
  versions call `openssl_random_pseudo_bytes(0)`, which throws on PHP 8.
- DES signing throws a `RuntimeException` when OpenSSL cannot compute it (an empty signature was shown before).

### Fixed

- SporoPay return URL: `SporoPay3DesSign::returnUrlSign()` read the undefined `$rurl` property and failed.
- Template parameter dumps are escaped (`{print_r($params, true)}` instead of a raw `{php print_r()}`).

### Added

- GitHub Actions CI (PHP 8.3 - 8.6, nightly 8.7): PHP and Latte lint, DI container boot, PHP-CS-Fixer (PER-CS),
  PHPStan level 5 with strict rules, `composer audit`, Dependabot.
