# Security Policy

## Supported Versions

payment-simulator is a development tool deployed from `master` (https://platby.tomaj.sk/). Security fixes go to `master` and the next release; older releases are not patched.

| Version | Supported |
| ------- | --------- |
| `master` and the latest release | Yes |
| Older releases | No |

## Reporting a Vulnerability

Do not open a public GitHub issue for a security vulnerability.

The preferred way to report a vulnerability is [GitHub private vulnerability reporting](https://github.com/tomaj/payment-simulator/security/advisories/new).

If you cannot use private vulnerability reporting, email the details to **tomasmajer@gmail.com**.

Please include a description, steps to reproduce, the affected page or endpoint, the potential impact, and a suggested fix if you have one.

### Response

You will receive an acknowledgement within 14 days.

### Coordinated disclosure

We follow coordinated disclosure. Please give us time to prepare a fix before discussing the issue in public. When a fix is ready, we publish a [GitHub Security Advisory](https://github.com/tomaj/payment-simulator/security/advisories) and record the fix in the [CHANGELOG](CHANGELOG.md).

## Security considerations

- The simulator signs and verifies with **fixed, publicly known test keys** (see README.md). It is not a payment gateway: never configure a shop with real merchant keys against it, and never let a production shop trust its return URLs.
- It is meant for development and test environments. The Docker image contains Xdebug and enables the OpenSSL legacy provider (single DES); do not use it as a base for production images.
- Pages echo the request parameters back (escaped by Latte). Report any way to inject markup or script.
