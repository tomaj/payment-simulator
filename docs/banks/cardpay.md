---
title: 'Tatra banka: CardPay'
description: The CardPay endpoints of payment-simulator - HMAC, AES and DES payments, the pre-authorization endpoint with 3-D Secure fields, and the server-to-server capture, release and refund.
---

# Tatra banka: CardPay

The customer pays by card on the bank's page. Besides a payment, CardPay pre-authorizes an amount that the shop
captures, releases or refunds later with a server-to-server request.

## HMAC: `GET /payment/cardpay-hmac`

| Parameter | Required | Meaning |
|---|---|---|
| `MID`, `AMT`, `CURR`, `VS`, `RURL` | yes | as [TatraPay](./tatrapay#hmac-get-payment-tatrapay-hmac) |
| `IPC` | yes | the customer's IP address |
| `NAME` | yes | the cardholder's name |
| *`REM`* | no | e-mail for the result notification |
| `TIMESTAMP` | yes | `ddmmYYYYHHMMSS` in UTC |
| `HMAC` | yes | the signature |

**Signed request:** `MID AMT CURR VS RURL IPC NAME REM TIMESTAMP` (HMAC-SHA256, lowercase hex).

**Return:** `RURL?VS=…&TXN=&RES=…&TRES=&AC=111111&AMT=…&CURR=…&CID=&CC=1111********1111&AC=111111&TIMESTAMP=…&HMAC=…`,
the HMAC over `AMT CURR VS TXN RES AC TRES CID CC TID TIMESTAMP` with empty `TXN`, `TRES`, `CID` and `TID`. That is
the same string omnipay-tatra verifies (`AMT CURR VS RES AC TRES CC RC TID TIMESTAMP`), because every field the two
lists do not share is empty.

## AES: `GET /payment/cardpay-aes256` and DES: `GET /payment/cardpay-des`

| Parameter | Required |
|---|---|
| `MID`, `AMT`, `CURR`, `VS`, `RURL`, `IPC`, `NAME`, `SIGN` | yes |
| *`CS`* | no |

**Signed request:** `MID AMT CURR VS CS RURL IPC NAME` (AES with `1` × 64, DES with `11111111`; uppercase hex).

**Return:** `RURL?VS=…&AC=111111&RES=…&SIGN=…`, signed over `VS RES AC`.

## Pre-authorization: `GET /payment/cardpay-authorize-hmac`

The endpoint of omnipay-tatra's `authorize()` (`TXN=PA`) and of card registration.

| Parameter | Required |
|---|---|
| `MID`, `AMT`, `CURR`, `VS`, `RURL`, `IPC`, `NAME`, `TIMESTAMP`, `HMAC` | yes |
| *`E2E`*, *`TXN`*, *`REM`*, *`TPAY`*, *`CID`*, *`ECID`* | no |
| *`TDS_CARDHOLDER`*, *`TDS_EMAIL`*, *`TDS_MOBILE_PHONE`*, *`TDS_BILL_CITY`*, *`TDS_BILL_COUNTRY`*, *`TDS_BILL_ADDRESS1`*, *`TDS_BILL_ADDRESS2`*, *`TDS_BILL_ZIP`*, *`TDS_SHIP_CITY`*, *`TDS_SHIP_COUNTRY`*, *`TDS_SHIP_ADDRESS1`*, *`TDS_SHIP_ADDRESS2`*, *`TDS_SHIP_ZIP`*, *`TDS_ADDR_MATCH`* | no |

**Signed request:** `MID AMT CURR VS E2E TXN RURL IPC NAME REM TPAY CID ECID TDS_CARDHOLDER TDS_EMAIL
TDS_MOBILE_PHONE TDS_BILL_CITY TDS_BILL_COUNTRY TDS_BILL_ADDRESS1 TDS_BILL_ADDRESS2 TDS_BILL_ZIP TDS_SHIP_CITY
TDS_SHIP_COUNTRY TDS_SHIP_ADDRESS1 TDS_SHIP_ADDRESS2 TDS_SHIP_ZIP TDS_ADDR_MATCH TIMESTAMP`.

**Return:** `RURL?AMT=…&CURR=…&VS=…&TXN=…&RES=…&AC=…&TRES=…&CID=…&CC=************1234&RC=…&TID=…&TIMESTAMP=…&HMAC=…`,
the HMAC over `AMT CURR VS TXN RES AC TRES CID CC RC TID TIMESTAMP`. The values depend on `TPAY`:

| | `TPAY=Y`, `RES=OK` | `TPAY=Y`, `FAIL`/`TOUT` | without `TPAY=Y` |
|---|---|---|---|
| `AC` (approval code) | `31232` | empty | empty |
| `TRES` | `OK` | `FAIL` / `TOUT` | empty |
| `CID` (card id) | `123456` | empty | empty |
| `RC` | empty | `01` | `01` |
| `TID` (transaction id) | `567898` | empty | empty |

Send `TPAY=Y` to get a transaction id for the capture below.

## Capture, release and refund: `GET /payment/cardpay-cancel-purchase`

The server-to-server endpoint of omnipay-tatra's `cancelAuthorize()` (the bank's `txn_process.jsp`).

| Parameter | Required | Meaning |
|---|---|---|
| `MID`, `AMT`, `TID`, `TXN`, `TIMESTAMP`, `HMAC` | expected | `TXN`: `CPA` capture, `SPA` release, `CB` refund |
| *`VS`*, *`REM`* | no | |

**Signed request:** `MID AMT TID VS TXN REM TIMESTAMP` (HMAC, lowercase hex). The endpoint does not list missing
parameters: a missing one simply makes the signature wrong.

**Answer** (`Content-Type: text/xml`), when the signature matches:

```xml
<?xml version="1.0" encoding="windows-1250"?>
    <cardpay>
    <request>
        <mid>9999</mid>
        <amt>110.00</amt>
        <tid>567898</tid>
        <txn>CPA</txn>
        <timestamp>24112026101500</timestamp>
    </request>
    <result>
        <res>OK</res>
    </result>
</cardpay>
```

with the header `Authorization: HMAC=<HMAC of the body>,ECDSA=…, ECDSA_KEY=1`. With a wrong signature the
`<result>` is `<res>FAIL</res><errorCode>10</errorCode>` and there is no `Authorization` header. The values of the
`<request>` element are XML-escaped copies of the parameters.

Examples: [omnipay-tatra](/examples/omnipay-tatra) (purchase and authorize), [CardPay capture and refund](/examples/cardpay-capture).
