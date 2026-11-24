---
title: Troubleshooting
description: Fix "Wrong sign", "Missing parameters", DES errors, rejected returns and connection problems with payment-simulator.
---

# Troubleshooting

## "Wrong sign"

The page shows the **Input Sign** you sent and the **Computed Sign**. Check, in this order:

1. **The key.** Use the [test key](./configuration#test-keys) of the gateway's algorithm, not your production key.
2. **The URL.** `/payment/tatrapay-hmac` expects an HMAC signature, `/payment/tatrapay-aes256` an AES one; the key length
   decides which one your library computes.
3. **The field order.** The signed fields of every endpoint are on the [Banks](/banks/) pages. Fields are concatenated
   without separators (SporoPay: with `;`), and an empty optional field adds nothing.
4. **The specific symbol.** `SS` is signed between `VS` and `CS` for TatraPay and VÚB.
5. **The case.** Hexadecimal signatures are compared exactly: HMAC is lowercase for Tatra banka, AES/DES and VÚB are
   uppercase.
6. **The timestamp.** HMAC requests sign `TIMESTAMP`; send the same value you signed.
7. **Encoding.** The signature covers the decoded values: sign `https://shop.example/return`, then URL-encode it in the
   query string, not the other way round.

## HTTP 400 "Missing parameters"

A required parameter is absent; the page lists all missing ones, the signature parameter included. Parameters that
must be present but may be empty are sent empty (`SS=`), not left out.

## DES pages fail with "Unable to compute the DES signature"

OpenSSL 3 offers single DES only through its legacy provider. Use the Docker image, `examples/ci/serve.sh`, or start
the server with `OPENSSL_CONF=docker/php/openssl-legacy.cnf`.

## My library rejects the simulator's answer

- **A specific symbol on the return**: TatraPay and VÚB e-platby sign `SS` in the return (`VS SS RES` for TatraPay AES
  and DES and for e-platby). Pass the same `ss` to `completePurchase()` as to `purchase()`; omnipay-tatra 4.x verifies
  the AES/DES return over `VS RES` and rejects it when `ss` is set, use omnipay-tatra 5.
- **Another amount or currency**: the HMAC return is signed over the amount and currency of the request; verify with
  the same values.
- **A changed variable symbol**: the libraries reject a return for another order than the one you pass.

## Nothing answers, or the browser cannot reach the return URL

- `curl -I http://127.0.0.1:8080/` must answer 200; `log/php-server.log` (built-in server) or
  `docker compose logs` (Docker) show why not.
- The return URL is opened by the customer's browser, not by the simulator: it must be reachable from the browser,
  so `http://localhost/...` works for local testing.
- In Docker, other containers reach the simulator as `http://paymentsandbox.test`, but the browser on your machine
  does not know that name; see [Running it](./running#next-to-your-application).

## Errors on Linux with Docker

PHP-FPM runs as `www-data` and must write the mounted `temp/` and `log/`: `chmod -R a+w temp log`.
