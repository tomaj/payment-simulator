---
title: CardPay Direct API
description: Apple Pay and Google Pay transactions against the CardPay Direct API of payment-simulator in plain PHP - signed JSON, the 3-D Secure page and the transaction check.
---

# CardPay Direct API

The Direct API is JSON over HTTPS, signed in the `Authorization` header in both directions. The example is a complete
client in plain PHP.

Source: [`examples/cardpay-direct-api/run.php`](https://github.com/tomaj/payment-simulator/blob/master/examples/cardpay-direct-api/run.php) ·
run it with `examples/ci/example.sh cardpay-direct-api`.

## The client

<<< @/../examples/cardpay-direct-api/run.php#client

Every answer is checked before it is decoded: the HMAC covers the exact body bytes.

## Transactions

<<< @/../examples/cardpay-direct-api/run.php#transaction

The amount decides the status: Google Pay from 10.00 (1000 cents) needs 3-D Secure, anything from 100.00 fails.

## 3-D Secure

<<< @/../examples/cardpay-direct-api/run.php#tds

In a shop, put `tdsRedirectionFormHtml` into the page; it submits itself.

## Checking a transaction

<<< @/../examples/cardpay-direct-api/run.php#check

## A wrong signature

<<< @/../examples/cardpay-direct-api/run.php#unsigned

## Output

<<< @/../examples/cardpay-direct-api/expected-output.txt{text}

The ids and the authorization code are random, so the example prints only the stable fields.

## What it tests

- request signatures over the JSON body and over `merchantId;timestamp;processingId`;
- the signature of every answer;
- the status for each wallet and amount, and the 3-D Secure form and page;
- HTTP 400 for a wrong signature.

Endpoint details: [CardPay Direct API](/banks/cardpay-direct-api).
