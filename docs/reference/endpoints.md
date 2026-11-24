---
title: Endpoints
description: Every URL of payment-simulator on one page - method, gateway, test key, signature parameter and signed fields, and what it answers.
---

# Endpoints

Every URL the simulator answers, with the details on the bank pages. Parameters in *italics* are optional. Signed
fields are concatenated in this order without separators unless noted; an absent optional field adds nothing.

## Redirect gateways

These show an HTML page: **Sign OK** with `continue ok`, `continue fail` and `continue timeout` links back to the return
URL, **Wrong sign**, or **Missing parameters** (HTTP 400). See [How it works](/guide/how-it-works).

| Method and URL | Gateway | Key | Required parameters | Signature: signed fields | Return signed over |
|---|---|---|---|---|---|
| `GET /payment/tatrapay-hmac` | [TatraPay](/banks/tatrapay#hmac-get-payment-tatrapay-hmac) | HMAC | `MID AMT CURR VS RURL TIMESTAMP`, *`SS CS REM`* | `HMAC`: MID AMT CURR VS SS CS RURL REM TIMESTAMP | AMT CURR VS SS CS RES TID TIMESTAMP |
| `GET /payment/tatrapay-aes256` | [TatraPay](/banks/tatrapay#aes-get-payment-tatrapay-aes256) | AES | `MID AMT CURR VS RURL`, *`SS CS`* | `SIGN`: MID AMT CURR VS SS CS RURL | VS RES |
| `GET /payment/tatrapay-des` | [TatraPay](/banks/tatrapay#des-get-payment-tatrapay-des) | DES | as AES | as AES | VS RES |
| `GET /payment/cardpay-hmac` | [CardPay](/banks/cardpay#hmac-get-payment-cardpay-hmac) | HMAC | `MID AMT CURR VS RURL IPC NAME TIMESTAMP`, *`REM`* | `HMAC`: MID AMT CURR VS RURL IPC NAME REM TIMESTAMP | AMT CURR VS TXN RES AC TRES CID CC TID TIMESTAMP |
| `GET /payment/cardpay-aes256` | [CardPay](/banks/cardpay#aes-get-payment-cardpay-aes256-and-des-get-payment-cardpay-des) | AES | `MID AMT CURR VS RURL IPC NAME`, *`CS`* | `SIGN`: MID AMT CURR VS CS RURL IPC NAME | VS RES AC |
| `GET /payment/cardpay-des` | CardPay | DES | as AES | as AES | VS RES AC |
| `GET /payment/cardpay-authorize-hmac` | [CardPay pre-authorization](/banks/cardpay#pre-authorization-get-payment-cardpay-authorize-hmac) | HMAC | `MID AMT CURR VS RURL IPC NAME TIMESTAMP`, *`E2E TXN REM TPAY CID ECID TDS_*`* | `HMAC`: MID AMT CURR VS E2E TXN RURL IPC NAME REM TPAY CID ECID TDS_… TIMESTAMP | AMT CURR VS TXN RES AC TRES CID CC RC TID TIMESTAMP |
| `GET /payment/comfortpay-hmac` | [ComfortPay](/banks/comfortpay#hmac-get-payment-comfortpay-hmac) | HMAC | `MID AMT CURR VS RURL TPAY TIMESTAMP IPC NAME`, *`REM`* | `HMAC`: MID AMT CURR VS RURL IPC NAME REM TPAY TIMESTAMP | AMT CURR VS RES AC TRES CID CC RC TID TIMESTAMP |
| `GET /payment/comfortpay-aes256` | [ComfortPay](/banks/comfortpay#aes-get-payment-comfortpay-aes256) | AES | `MID AMT CURR VS RURL TPAY IPC NAME`, *`CS`* | `SIGN`: MID AMT CURR VS CS RURL IPC NAME TPAY | VS RES AC CID |
| `POST /payment/eplatby-hmac` | [VÚB e-platby](/banks/vub-eplatby) | e-platby | form: `MID AMT VS RURL`, *`SS CS`* | `SIGN`: MID AMT VS SS CS RURL (uppercase) | VS SS RES |
| `GET /payment/sporopay-des` | [SLSP SporoPay](/banks/slsp-sporopay) | SporoPay | `pu_predcislo pu_cislo pu_kbanky suma mena vs ss url param` | `sign1`: the fields joined with `;` | `SIGN2`: payer account, the fields, result, real, joined with `;` |

## Server-to-server

| Method and URL | Gateway | Request | Answer |
|---|---|---|---|
| `GET /payment/cardpay-cancel-purchase` | [CardPay capture, release, refund](/banks/cardpay#capture-release-and-refund-get-payment-cardpay-cancel-purchase) | `MID AMT TID VS TXN REM TIMESTAMP HMAC`, signed in that order | XML `<res>OK</res>` with `Authorization: HMAC=<HMAC of the body>,…`, or `<res>FAIL</res><errorCode>10</errorCode>` unsigned |
| `POST /cgi-bin/e-commerce/start/api/cardpay/transaction` | [CardPay Direct API](/banks/cardpay-direct-api) | JSON `amount` (cents), `applePayToken` or `googlePayToken`, *`tdsTermUrl`*; `Authorization: HMAC=<HMAC of the body>` | JSON `status` `OK`, `TDS_AUTH_REQUIRED` (Google Pay from 1000) or `FAIL` (from 10000), signed in `Authorization` |
| `GET /cgi-bin/e-commerce/start/api/cardpay/transaction/<processingId>` | CardPay Direct API | headers `X-Merchant-Id`, `X-Timestamp`, `Authorization: HMAC=<HMAC of "merchantId;timestamp;processingId">` | JSON `status: OK`, signed |
| `POST /cgi-bin/e-commerce/start/api/form/cardpay/transaction/tds` | 3-D Secure page | form `tdsTermUrl`, `processingId`, … | HTML page with a `continue` link to `tdsTermUrl` |

## Other

| URL | Answers |
|---|---|
| `GET /` | the home page |
| `/tatrabanka/cardpay-direct-api-v0.4.2.pdf`, `-v0.3.pdf` | Tatra banka's CardPay Direct API documentation |
| anything else | 404 page |
