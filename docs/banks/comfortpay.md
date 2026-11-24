---
title: 'Tatra banka: ComfortPay'
description: The ComfortPay endpoints of payment-simulator - a CardPay payment that registers the card (TPAY=Y) with an HMAC or AES key, and the card id it returns.
---

# Tatra banka: ComfortPay

ComfortPay is a CardPay payment that registers the card (`TPAY=Y`); the bank returns a card id (`CID`) that the shop
charges later through Tatra banka's SOAP web service. The simulator covers the redirect payment. **The SOAP web service
is not simulated**: omnipay-tatra answers those requests itself in test mode.

## HMAC: `GET /payment/comfortpay-hmac`

| Parameter | Required |
|---|---|
| `MID`, `AMT`, `CURR`, `VS`, `RURL`, `TPAY`, `TIMESTAMP`, `IPC`, `NAME`, `HMAC` | yes |
| *`REM`* | no |

**Signed request:** `MID AMT CURR VS RURL IPC NAME REM TPAY TIMESTAMP`.

**Return:** `RURL?VS=…&AMT=…&CURR=…&RES=…&TRES=…&AC=111111&CID=111111&CC=1111********1111&RC=123&TIMESTAMP=…&HMAC=…`,
the HMAC over `AMT CURR VS RES AC TRES CID CC RC TID TIMESTAMP` with an empty `TID`; `TRES` equals `RES`.

## AES: `GET /payment/comfortpay-aes256`

| Parameter | Required |
|---|---|
| `MID`, `AMT`, `CURR`, `VS`, `RURL`, `TPAY`, `IPC`, `NAME`, `SIGN` | yes |
| *`CS`* | no |

**Signed request:** `MID AMT CURR VS CS RURL IPC NAME TPAY`.

**Return:** `RURL?VS=…&RES=…&TRES=…&AC=111111&CID=111111&SIGN=…`, signed over `VS RES AC CID` (omnipay-tatra
verifies `VS TRES AC CID`; `TRES` equals `RES`).

ComfortPay has no DES variant.

## The card id

Every answer, also `FAIL` and `TOUT`, carries `CID=111111`. With omnipay-tatra in test mode, `charge()` approves an odd
card id and declines an even one, so the simulator's card id is charged successfully.

Example: [omnipay-tatra](/examples/omnipay-tatra), and the
[ComfortPay subscription](https://tomaj.github.io/omnipay-tatra/examples/comfortpay-recurring) of omnipay-tatra.
