---
title: Using it with Omnipay
description: Point the omnipay-tatra, omnipay-vub and omnipay-slsp gateways at payment-simulator with testMode and testHost, and run every branch of your return URL handler.
---

# Using it with Omnipay

The simulator was built for the `tomaj/omnipay-*` gateways. In test mode they send the customer to `testHost`
(default `https://platby.tomaj.sk`) instead of the bank, at the URL the simulator expects:

| Package | Gateway | Test key | Simulator URL |
|---|---|---|---|
| [omnipay-tatra](https://tomaj.github.io/omnipay-tatra/) | `TatraPay` | HMAC, AES or DES | `/payment/tatrapay-hmac`, `-aes256`, `-des` |
| | `CardPay` | HMAC, AES or DES | `/payment/cardpay-hmac`, `-aes256`, `-des`; `authorize()`: `/payment/cardpay-authorize-hmac`; `cancelAuthorize()`: `/payment/cardpay-cancel-purchase` |
| | `ComfortPay` | HMAC or AES | `/payment/comfortpay-hmac`, `-aes256`; the SOAP web service is answered by the library itself in test mode |
| [omnipay-vub](https://github.com/tomaj/omnipay-vub) | `Eplatby` | e-platby | `/payment/eplatby-hmac` (form POST) |
| [omnipay-slsp](https://github.com/tomaj/omnipay-slsp) | `SporoPay` | SporoPay | `/payment/sporopay-des` |

## Configure the gateway

<<< @/../examples/omnipay-tatra/run.php#config

`testMode` must be on: without it the gateway sends the customer to the real bank. `testHost` is the simulator's base
URL without a trailing slash. The [test keys](./configuration#test-keys) replace your real `sharedSecret`; any `mid`
works.

## Round trip

The [omnipay-tatra example](/examples/omnipay-tatra) does this for TatraPay, CardPay, ComfortPay and a CardPay
pre-authorization: start the payment, open the simulator's page like the customer's browser would, then complete the
payment with each of the three signed answers.

<<< @/../examples/omnipay-tatra/run.php#round-trip

`Omnipay::create($name, null, $request)` gives the gateway the customer's request to the return URL; in a web
application `Omnipay::create($name)` reads it from the PHP globals.

<<< @/../examples/omnipay-tatra/expected-output.txt{text}

The [VÚB e-platby](/examples/vub-eplatby) and [SLSP SporoPay](/examples/slsp-sporopay) examples do the same with
omnipay-vub and omnipay-slsp.

## What differs from the bank

| Gateway | Difference | Effect on your tests |
|---|---|---|
| TatraPay HMAC | no `TID` in the return | the transaction id is empty |
| TatraPay AES / DES | the return is signed over `VS RES`, omnipay-tatra 5 verifies `VS SS RES` | with a specific symbol (`ss`) the AES/DES return fails verification; leave `ss` empty with those keys |
| CardPay | fixed `AC=111111`, `CC=1111********1111`, no `TID` and `RC` | test only that you store them |
| CardPay authorize | `TID=567898`, `CID=123456`, `AC=31232` only with `TPAY=Y` and `RES=OK` | pass `tpay` `Y` to get a transaction id for `cancelAuthorize()` |
| ComfortPay | fixed card id `CID=111111`, `RC=123` | |
| ComfortPay web service | not simulated | omnipay-tatra answers it locally in test mode (by the parity of the id) |
| all | `RES=TOUT` is offered for every gateway | only TatraPay reports it as pending; for the others it is "not successful" |

Release a real integration only after one real payment in the bank's environment.
