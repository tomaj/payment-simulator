---
title: Examples
description: Six clients that run against payment-simulator in CI - omnipay-tatra, a plain PHP client, VÚB e-platby, SLSP SporoPay, the CardPay Direct API and CardPay capture and refund.
---

# Examples

Every example is a client in [`examples/`](https://github.com/tomaj/payment-simulator/tree/master/examples) that talks
to the simulator like a shop would. The [Examples workflow](https://github.com/tomaj/payment-simulator/blob/master/.github/workflows/examples.yml)
serves the simulator from the pull request's code and runs every example against it, comparing the output with its
`expected-output.txt`, so a change that breaks a bank's protocol fails CI. The code on these pages is included from
those files.

| Example | Client | Covers |
|---|---|---|
| [omnipay-tatra](./omnipay-tatra) | `tomaj/omnipay-tatra` | TatraPay, CardPay, ComfortPay and the CardPay pre-authorization, each with OK, FAIL and TOUT |
| [Plain PHP client](./plain-php) | none | a TatraPay request and its answers signed by hand; **Wrong sign** and **Missing parameters** |
| [VÚB e-platby](./vub-eplatby) | `tomaj/omnipay-vub` | the form POST and its answers |
| [SLSP SporoPay](./slsp-sporopay) | `tomaj/omnipay-slsp` | the 3DES redirect and its answers |
| [CardPay Direct API](./cardpay-direct-api) | none | Apple Pay and Google Pay JSON transactions, 3-D Secure, the transaction check |
| [CardPay capture and refund](./cardpay-capture) | none | the server-to-server capture, release and refund with its signed XML |

## Run them

```bash
git clone https://github.com/tomaj/payment-simulator.git
cd payment-simulator
examples/ci/example.sh plain-php    # or omnipay-tatra, vub-eplatby, slsp-sporopay, cardpay-direct-api, cardpay-capture
```

[`examples/ci/example.sh`](https://github.com/tomaj/payment-simulator/blob/master/examples/ci/example.sh) serves the
checkout with [`examples/ci/serve.sh`](https://github.com/tomaj/payment-simulator/blob/master/examples/ci/serve.sh)
unless something already answers at `SIMULATOR_URL`, installs the example's own dependencies and runs its `e2e.sh`,
which prints `ok: <example>` when the output matches. The omnipay examples take the libraries' current `master` from
GitHub. Requirements: PHP 8.4+ with `openssl` (and `soap` for omnipay-tatra), Composer, curl.

The redirect examples share
[`examples/_support/simulator.php`](https://github.com/tomaj/payment-simulator/blob/master/examples/_support/simulator.php),
which plays the customer's browser: it opens the payment page and reads the verdict, the missing parameters and the
three signed links back.

<<< @/../examples/_support/simulator.php#open

## In other repositories

[omnipay-tatra](https://tomaj.github.io/omnipay-tatra/examples/) runs its own examples against a clone of this
repository started the same way; see [Running it in CI](/guide/running#in-ci).
