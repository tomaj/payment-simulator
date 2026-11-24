---
title: VÚB e-platby
description: The VÚB e-platby endpoint of payment-simulator - a form POST signed with HMAC-SHA256, the fields it signs and the signed return.
---

# VÚB e-platby

The customer pays from VÚB internet banking. Unlike the Tatra banka gateways, the shop **posts a form** to the bank.

## `POST /payment/eplatby-hmac`

Key: `1` × 64, used as text ([test keys](/guide/configuration#test-keys)).

| Field | Required | Meaning |
|---|---|---|
| `MID` | yes | merchant id |
| `AMT` | yes | amount |
| `VS` | yes | variable symbol |
| *`SS`* | no | specific symbol |
| *`CS`* | no | constant symbol |
| `RURL` | yes | return URL |
| `SIGN` | yes | the signature |

The fields are read from the POST body; query parameters are ignored.

**Signed request:** `MID AMT VS SS CS RURL`, HMAC-SHA256 with the key as text, **uppercase** hex.

**Return:** `RURL?VS=…[&SS=…]&RES=OK|FAIL|TOUT&SIGN=…`, signed over `VS SS RES`.

omnipay-vub's `purchase()` answers with a form (`getRedirectMethod()` is `POST`); `redirect()` renders it so that the
browser submits it. omnipay-vub 4 verifies the return over `VS SS RES` and checks the variable symbol.

Example: [VÚB e-platby](/examples/vub-eplatby).
