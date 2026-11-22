Payment simulator
=================

[![CI](https://github.com/tomaj/payment-simulator/actions/workflows/ci.yml/badge.svg)](https://github.com/tomaj/payment-simulator/actions/workflows/ci.yml)

[More info here](https://platby.tomaj.sk/)

Requirements
------------

- PHP 8.3 - 8.6 with the `openssl` extension
- the TatraPay / CardPay DES simulators use single DES, which OpenSSL 3 only provides with the legacy provider enabled

Development
-----------

```bash
composer install
composer check   # code style, PHPStan, lint
composer cs-fix  # apply code style
```
