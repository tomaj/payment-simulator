# Contributing

Contributions are welcome and will be credited.

This project follows the [Contributor Covenant Code of Conduct](CODE_OF_CONDUCT.md). By participating, you agree to uphold it.

Security vulnerabilities are reported in private. See [SECURITY.md](SECURITY.md). Do not open a public issue for a security report.

## Pull requests

- Follow [PER Coding Style](https://www.php-fig.org/per/coding-style/). `composer phpcs` checks `app`, `www` and `tests`.
- Add tests for every behaviour change. A signature change needs expected values computed independently with the `openssl` CLI (see `tests/Model/`) and, where a `tomaj/omnipay-*` library builds the request, a test that the library's request is accepted.
- Signatures follow the bank's specification. Link it in the pull request.
- Document user-visible changes in `README.md` (endpoints, parameters) and `CHANGELOG.md`.
- Open one pull request per change, from a feature branch.

## Development

The simulator runs on PHP 8.4, 8.5 and 8.6. CI runs the tests on each of them (8.6 also with coverage, the OpenSSL legacy provider and a 90% coverage threshold), builds the Docker image, and runs a nightly PHP 8.7 build that may fail.

```bash
git clone https://github.com/tomaj/payment-simulator.git
cd payment-simulator
composer install
composer test
composer phpstan
composer phpcs
composer rector
```

`composer check` runs the code style check, PHPStan, Rector and the tests in one go. The DES tests need single DES, which OpenSSL 3 offers only with the legacy provider; without it they are skipped. Run them with:

```bash
OPENSSL_CONF=docker/php/openssl-legacy.cnf composer test
```

See [README.md](README.md) for running the simulator locally and with Docker.

Releases are tagged by the maintainer; see [RELEASING.md](RELEASING.md).

By contributing, you agree that your contributions are licensed under the MIT License.
