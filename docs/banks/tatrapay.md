---
title: 'Tatra banka: TatraPay'
description: The TatraPay endpoints of payment-simulator - HMAC, AES and DES - with the parameters they require, the signed fields and the signed return they send to the shop.
---

# Tatra banka: TatraPay

The customer pays from Tatra banka internet banking. The simulator has one endpoint per key type; the bank's
production URLs are `https://moja.tatrabanka.sk/cgi-bin/e-commerce/start/tatrapay` (HMAC) and
`.../e-commerce.jsp` (AES, DES).

## HMAC: `GET /payment/tatrapay-hmac`

Key: `1` × 128 ([test keys](/guide/configuration#test-keys)).

| Parameter | Required | Meaning |
|---|---|---|
| `MID` | yes | merchant id (any value) |
| `AMT` | yes | amount, `24.90` |
| `CURR` | yes | ISO 4217 numeric currency code, `978` for EUR |
| `VS` | yes | variable symbol |
| *`SS`* | no | specific symbol |
| *`CS`* | no | constant symbol |
| `RURL` | yes | return URL |
| *`REM`* | no | e-mail for the result notification |
| `TIMESTAMP` | yes | `ddmmYYYYHHMMSS` in UTC |
| `HMAC` | yes | the signature |

Other TatraPay parameters (`PT`, `DESC`, `RSMS`, `AREDIR`, `LANG`) are accepted and ignored.

**Signed request:** `MID AMT CURR VS SS CS RURL REM TIMESTAMP`, HMAC-SHA256 with the hex-decoded key, lowercase hex.

**Return:** `RURL?VS=…[&SS=…]&RES=OK|FAIL|TOUT&AMT=…&CURR=…&CS=…&TIMESTAMP=…&HMAC=…`, the HMAC over
`AMT CURR VS SS CS RES TID TIMESTAMP` with an empty `TID` (the simulator sends none). `TIMESTAMP` is the request's.

<<< @/../examples/plain-php/run.php#request

## AES: `GET /payment/tatrapay-aes256`

Key: `1` × 64.

| Parameter | Required |
|---|---|
| `MID`, `AMT`, `CURR`, `VS`, `RURL`, `SIGN` | yes |
| *`SS`*, *`CS`* | no |

**Signed request:** `MID AMT CURR VS SS CS RURL`: the first 16 bytes of SHA-1, AES-128-ECB with the first 16 bytes of
the key, the first 32 hex characters, uppercase.

**Return:** `RURL?VS=…[&SS=…]&RES=OK|FAIL|TOUT&SIGN=…`, signed over `VS RES`.

::: warning SS on AES and DES returns
The return signature does not include `SS`, while omnipay-tatra 5 verifies `VS SS RES`. With a specific symbol the
library rejects the simulator's AES/DES return; leave `SS` empty with these keys.
:::

## DES: `GET /payment/tatrapay-des`

Key: `11111111`. Parameters and signed fields as AES; the signature is SHA-1 encrypted with DES-ECB, the first 16 hex
characters, uppercase. Needs the OpenSSL legacy provider ([Running it](/guide/running#local-php)).

**Return:** as AES, signed with DES over `VS RES`.

## Results

| Link | `RES` | omnipay-tatra |
|---|---|---|
| `continue ok` | `OK` | `isSuccessful()` |
| `continue fail` | `FAIL` | neither |
| `continue timeout` | `TOUT` | `isPending()` |

Examples: [omnipay-tatra](/examples/omnipay-tatra), [plain PHP](/examples/plain-php).
