---
title: VÚB e-platby
description: omnipay-vub against payment-simulator - the e-platby form POST and its OK, FAIL and TOUT answers.
---

# VÚB e-platby

VÚB e-platby is the only simulated gateway that receives a form POST. The example uses
[omnipay-vub](https://github.com/tomaj/omnipay-vub) in test mode.

Source: [`examples/vub-eplatby/run.php`](https://github.com/tomaj/payment-simulator/blob/master/examples/vub-eplatby/run.php) ·
run it with `examples/ci/example.sh vub-eplatby`.

## The payment

<<< @/../examples/vub-eplatby/run.php#purchase

`getRedirectMethod()` is `POST` and `getRedirectData()` the form fields; in a web application `redirect()` renders a
form that the browser submits. The example posts the fields itself.

## The answers

<<< @/../examples/vub-eplatby/run.php#complete

omnipay-vub verifies the signature over `VS SS RES` and that `VS` is the expected one. The amount is not signed by
the bank: take it from your order.

## Output

<<< @/../examples/vub-eplatby/expected-output.txt{text}

`TOUT` is not a result VÚB defines; the simulator offers it for every gateway, and omnipay-vub treats it as not paid.

## What it tests

- the form POST with omnipay-vub's signature gets **Sign OK**;
- the signed answers pass omnipay-vub's verification.

Endpoint details: [VÚB e-platby](/banks/vub-eplatby).
