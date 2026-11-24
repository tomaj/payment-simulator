---
title: SLSP SporoPay
description: omnipay-slsp against payment-simulator - the 3DES-signed SporoPay redirect and its OK, FAIL and TOUT answers.
---

# SLSP SporoPay

SporoPay signs with 3DES over fields joined by semicolons. The example uses
[omnipay-slsp](https://github.com/tomaj/omnipay-slsp) in test mode.

Source: [`examples/slsp-sporopay/run.php`](https://github.com/tomaj/payment-simulator/blob/master/examples/slsp-sporopay/run.php) ·
run it with `examples/ci/example.sh slsp-sporopay`.

## The payment

<<< @/../examples/slsp-sporopay/run.php#purchase

`accountNumberPrefix` and `accountNumber` are your account (`pu_predcislo`, `pu_cislo`); `param` comes back
unchanged.

## The answers

<<< @/../examples/slsp-sporopay/run.php#complete

## Output

<<< @/../examples/slsp-sporopay/expected-output.txt{text}

## What it tests

- the 3DES `sign1` of omnipay-slsp gets **Sign OK**;
- the `SIGN2` of each answer passes omnipay-slsp's verification, with the payer account and `real` the simulator adds.

Endpoint details: [SLSP SporoPay](/banks/slsp-sporopay).
