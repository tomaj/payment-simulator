---
title: omnipay-tatra
description: omnipay-tatra in test mode against payment-simulator - TatraPay, CardPay, ComfortPay and the CardPay pre-authorization, each answered with OK, FAIL and TOUT.
---

# omnipay-tatra

[omnipay-tatra](https://tomaj.github.io/omnipay-tatra/) is the client the Tatra banka endpoints were built for. The
example runs four flows against the simulator and completes each with all three answers.

Source: [`examples/omnipay-tatra/run.php`](https://github.com/tomaj/payment-simulator/blob/master/examples/omnipay-tatra/run.php) ·
run it with `examples/ci/example.sh omnipay-tatra`.

## Configuration

<<< @/../examples/omnipay-tatra/run.php#config

## The round trip

<<< @/../examples/omnipay-tatra/run.php#round-trip

| Flow | Starts with | Simulator URL | Completes with |
|---|---|---|---|
| TatraPay | `purchase()` | `/payment/tatrapay-hmac` | `completePurchase()` |
| CardPay | `purchase()` | `/payment/cardpay-hmac` | `completePurchase()` |
| ComfortPay | `purchase()` (`TPAY=Y`) | `/payment/comfortpay-hmac` | `completePurchase()` |
| CardPay authorize | `authorize()` with `tpay` `Y` | `/payment/cardpay-authorize-hmac` | `completeAuthorize()` |

## Output

<<< @/../examples/omnipay-tatra/expected-output.txt{text}

`timeout` is pending only for TatraPay: CardPay and ComfortPay have no timeout result, so `RES=TOUT` is simply not
successful.

## What it tests

- every request omnipay-tatra builds in test mode gets **Sign OK**, so the signed fields agree;
- every signed answer of the simulator passes omnipay-tatra's verification, so the return signatures agree;
- `isSuccessful()` and `isPending()` match the result.

The simulator's own PHPUnit suite (`tests/Functional/OmnipayLibrariesTest.php`) does the same with the released
omnipay libraries.
