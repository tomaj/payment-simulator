# Payment Simulator

**Simulator of Slovak bank online payment gateways (TatraPay, CardPay, ComfortPay, VÚB ePlatby, SLSP SporoPay) for testing shop integrations**

Live instance: **https://platby.tomaj.sk/**

[![CI](https://github.com/tomaj/payment-simulator/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/tomaj/payment-simulator/actions/workflows/ci.yml?query=branch%3Amaster)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%20max-brightgreen.svg)](https://phpstan.org/)
[![OpenSSF Scorecard](https://api.securityscorecards.dev/projects/github.com/tomaj/payment-simulator/badge)](https://scorecard.dev/viewer/?uri=github.com/tomaj/payment-simulator)
[![License](https://img.shields.io/github/license/tomaj/payment-simulator.svg)](LICENSE.md)
[![PHP Version](https://img.shields.io/badge/php-%5E8.4-777bb4.svg)](composer.json)

## What is the Payment Simulator?

Slovak banks give merchants a payment page: the shop redirects the customer there with a signed request, and the bank sends the customer back to the shop with a signed result. Testing that round trip against the real banks needs merchant accounts and real money. The simulator accepts the same signed requests with publicly known test keys, shows whether the signature is correct, and offers links back to the shop for a successful, failed and timed-out payment, signed exactly like the bank signs them.

It is built for the [tomaj/omnipay-tatra](https://github.com/tomaj/omnipay-tatra), [tomaj/omnipay-vub](https://github.com/tomaj/omnipay-vub) and [tomaj/omnipay-slsp](https://github.com/tomaj/omnipay-slsp) gateways, whose test mode points at it, but works with any client that follows the bank specifications.

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) and the [Code of Conduct](CODE_OF_CONDUCT.md).

## Security

The simulator uses fixed test keys and must never be used with real merchant keys. Do not report security vulnerabilities in public issues; see [SECURITY.md](SECURITY.md).

## License

The MIT License (MIT). See [LICENSE.md](LICENSE.md).
