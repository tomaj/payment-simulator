---
title: Getting started
description: Start payment-simulator locally with one command, send it a signed TatraPay request and follow its signed answers back to your shop.
---

# Getting started

payment-simulator stands in for the payment pages of Slovak banks. Your shop redirects the customer to the simulator
instead of the bank; the simulator checks the request's signature with a public test key and offers the customer
three ways back to your return URL: a successful, a failed and a timed-out payment, each signed like the bank signs it.
Nothing leaves your machine and no money moves.

## Requirements

- PHP 8.4, 8.5 or 8.6 with `openssl` and Composer, **or** Docker with Compose v2
- the shop or library you want to test, configured to send payments to the simulator

## Start it

```bash
git clone https://github.com/tomaj/payment-simulator.git
cd payment-simulator
examples/ci/serve.sh            # composer install, then php -S 127.0.0.1:8080
```

[`examples/ci/serve.sh`](https://github.com/tomaj/payment-simulator/blob/master/examples/ci/serve.sh) installs the
dependencies when `vendor/` is missing, serves `www/` with the PHP built-in server at `SIMULATOR_URL` (default
`http://127.0.0.1:8080`) with the OpenSSL legacy provider the DES gateways need, and waits until the simulator answers.
Stop it with `kill "$(cat log/php-server.pid)"`. [Running it](./running) covers Docker and the details.

Open `http://127.0.0.1:8080/` to see the home page.

## Send a payment

Every gateway has its own URL under `/payment/`. A TatraPay payment signed with the HMAC test key (128 times `1`):

<<< @/../examples/plain-php/run.php#request

The simulator answers with an HTML page that shows the signature you sent, the one it computed, the parameters, and:

- **Sign OK** and three links, `continue ok`, `continue fail` and `continue timeout`, when the signature matches;
- **Wrong sign** when it does not (no links);
- **Missing parameters** with the list and HTTP 400 when a required parameter is absent.

Each link leads to your `RURL` with the bank's result parameters and their signature:

```text
https://shop.example/payment/return?VS=2026001&RES=OK&AMT=24.90&CURR=978&CS=&TIMESTAMP=24112026101500&HMAC=...
```

Your return URL handler verifies that signature, exactly as it would verify the bank's. Here by hand:

<<< @/../examples/plain-php/run.php#verify

The whole script and its output are the [plain PHP example](/examples/plain-php):

<<< @/../examples/plain-php/expected-output.txt{text}

## With an Omnipay gateway

The [omnipay-tatra](https://tomaj.github.io/omnipay-tatra/), [omnipay-vub](https://github.com/tomaj/omnipay-vub) and
[omnipay-slsp](https://github.com/tomaj/omnipay-slsp) gateways send their payments to the simulator in test mode;
set `testHost` to your instance:

<<< @/../examples/omnipay-tatra/run.php#config

See [Using it with Omnipay](./omnipay).

## Next steps

- [How it works](./how-it-works): the pages, the verdicts and the signed answers
- [Banks](/banks/): every endpoint with its parameters, signed fields and the return it gives
- [Configuration and test keys](./configuration)
- [Examples](/examples/): six clients, from plain PHP to the CardPay Direct API, tested in CI
