---
title: CardPay Direct API
description: The CardPay Direct API of payment-simulator - Apple Pay and Google Pay transactions as signed JSON, the 3-D Secure page and the transaction check, with the statuses the amount triggers.
---

# CardPay Direct API

Tatra banka's CardPay Direct API takes Apple Pay and Google Pay tokens from your page and answers with JSON. The
simulator implements its transaction and check endpoints and the 3-D Secure page, with the bank's paths, so a client
only swaps the host. Tatra banka's documentation of the API is in
[`www/tatrabanka/`](https://github.com/tomaj/payment-simulator/tree/master/www/tatrabanka) (v0.4.2 and v0.3, PDF).

Requests and answers are signed with HMAC-SHA256 and the HMAC test key (`1` × 128), in the `Authorization` header:
`Authorization: HMAC=<lowercase hex>`.

## `POST /cgi-bin/e-commerce/start/api/cardpay/transaction`

**Request:** a JSON body, signed over the exact body.

| Field | Meaning |
|---|---|
| `amount` | amount in cents, required and numeric |
| `applePayToken` or `googlePayToken` | the wallet token (not checked) |
| *`tdsTermUrl`* | where the 3-D Secure page sends the customer back |

**Status by amount:**

| Wallet | `amount` | `status` |
|---|---|---|
| Apple Pay | below 10000 | `OK` |
| Google Pay | below 1000 | `OK` |
| Google Pay | 1000 to 9999 | `TDS_AUTH_REQUIRED`, with `tdsRedirectionFormHtml` |
| any | 10000 and more | `FAIL` |

**Answer:**

```json
{
  "processingId": 12345,
  "status": "OK",
  "transactionId": 145678,
  "transactionData": { "authorizationCode": "MH1234", "responseCode": "00" }
}
```

`processingId` (10000 to 19999), `transactionId` (100000 to 199999) and `authorizationCode` (two letters, four digits)
are random; `responseCode` is always `00`. The answer carries
`Authorization: HMAC=<HMAC of the JSON body>, ECDSA=ecdsa, ECDSA_KEY=1`.

**Errors** (HTTP 400, unsigned): `{"error":"Invalid authorization header"}` for a missing or wrong signature,
`{"error":"Missing or invalid amount"}`. A body that is not JSON answers HTTP 200 with
`{"error":"Cannot process Google Pay token"}`.

## 3-D Secure: `POST /cgi-bin/e-commerce/start/api/form/cardpay/transaction/tds`

For `TDS_AUTH_REQUIRED`, `tdsRedirectionFormHtml` is an auto-submitting HTML form that posts `tdsTermUrl`,
`processingId`, `timestamp` and `signature` to this page, on the scheme, host and port the transaction request came to. The page shows
the parameters and a `continue` link to `tdsTermUrl`. It sends no `X-Frame-Options` header, so it can be shown in an
iframe.

## `GET /cgi-bin/e-commerce/start/api/cardpay/transaction/<processingId>`

| Header | Value |
|---|---|
| `X-Merchant-Id` | merchant id (any) |
| `X-Timestamp` | timestamp |
| `Authorization` | `HMAC=<HMAC of "merchantId;timestamp;processingId">` |

**Answer:** `{"processingId": "<processingId>", "status": "OK", "transactionId": …, "transactionData": {…}}`, signed like
the transaction answer. The check always answers `OK`.

Example: [CardPay Direct API](/examples/cardpay-direct-api).
