---
title: CardPay capture and refund
description: The CardPay capture, release and refund endpoint of payment-simulator in plain PHP - a signed GET and an XML answer signed in the Authorization header.
---

# CardPay capture and refund

After a pre-authorization, the shop's server captures (`CPA`), releases (`SPA`) or refunds (`CB`) it with a signed
`GET`; the bank answers with XML, signed in the `Authorization` header. omnipay-tatra's `cancelAuthorize()` does this;
the example shows the protocol in plain PHP.

Source: [`examples/cardpay-capture/run.php`](https://github.com/tomaj/payment-simulator/blob/master/examples/cardpay-capture/run.php) ·
run it with `examples/ci/example.sh cardpay-capture`.

## The request

<<< @/../examples/cardpay-capture/run.php#request

## Verifying the answer

<<< @/../examples/cardpay-capture/run.php#verify

The HMAC covers the body exactly as received, including the XML declaration and the whitespace; do not parse and
re-serialize it before you check.

## Capture, release, refund

<<< @/../examples/cardpay-capture/run.php#operations

## A wrong signature

<<< @/../examples/cardpay-capture/run.php#wrong

The `FAIL` answer is not signed. A client must not take an unsigned `FAIL` as proof of anything; it only means the
operation was not done.

## Output

<<< @/../examples/cardpay-capture/expected-output.txt{text}

## What it tests

- the request signature over `MID AMT TID VS TXN REM TIMESTAMP`;
- the signed XML answer for `CPA`, `SPA` and `CB`;
- the unsigned `FAIL` with error code `10`.

Endpoint details: [CardPay](/banks/cardpay#capture-release-and-refund-get-payment-cardpay-cancel-purchase).
