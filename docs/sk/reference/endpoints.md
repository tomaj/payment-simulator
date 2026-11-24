---
title: Endpointy
description: Všetky URL payment-simulator na jednej stránke - metóda, platobná brána, testovací kľúč, parameter podpisu a podpisované polia a čo endpoint vráti.
---

# Endpointy

Všetky URL, na ktoré simulátor odpovedá; podrobnosti sú na stránkach bánk. Nepovinné parametre sú napísané
*kurzívou*. Podpisované polia sa spájajú v tomto poradí bez oddeľovačov, ak nie je uvedené inak; chýbajúce nepovinné
pole nič nepridá.

## Brány s presmerovaním {#redirect-gateways}

Zobrazia HTML stránku: **Sign OK** s odkazmi `continue ok`, `continue fail` a `continue timeout` späť na návratovú
URL, **Wrong sign** alebo **Missing parameters** (HTTP 400). Pozrite [Ako to funguje](/sk/guide/how-it-works).

| Metóda a URL | Platobná brána | Kľúč | Povinné parametre | Podpis: podpisované polia | Odpoveď podpísaná nad |
|---|---|---|---|---|---|
| `GET /payment/tatrapay-hmac` | [TatraPay](/sk/banks/tatrapay#hmac-get-payment-tatrapay-hmac) | HMAC | `MID AMT CURR VS RURL TIMESTAMP`, *`SS CS REM`* | `HMAC`: MID AMT CURR VS SS CS RURL REM TIMESTAMP | AMT CURR VS SS CS RES TID TIMESTAMP |
| `GET /payment/tatrapay-aes256` | [TatraPay](/sk/banks/tatrapay#aes-get-payment-tatrapay-aes256) | AES | `MID AMT CURR VS RURL`, *`SS CS`* | `SIGN`: MID AMT CURR VS SS CS RURL | VS SS RES |
| `GET /payment/tatrapay-des` | [TatraPay](/sk/banks/tatrapay#des-get-payment-tatrapay-des) | DES | ako AES | ako AES | VS SS RES |
| `GET /payment/cardpay-hmac` | [CardPay](/sk/banks/cardpay#hmac-get-payment-cardpay-hmac) | HMAC | `MID AMT CURR VS RURL IPC NAME TIMESTAMP`, *`REM`* | `HMAC`: MID AMT CURR VS RURL IPC NAME REM TIMESTAMP | AMT CURR VS TXN RES AC TRES CID CC TID TIMESTAMP |
| `GET /payment/cardpay-aes256` | [CardPay](/sk/banks/cardpay#aes-get-payment-cardpay-aes256-and-des-get-payment-cardpay-des) | AES | `MID AMT CURR VS RURL IPC NAME`, *`CS`* | `SIGN`: MID AMT CURR VS CS RURL IPC NAME | VS RES AC |
| `GET /payment/cardpay-des` | CardPay | DES | ako AES | ako AES | VS RES AC |
| `GET /payment/cardpay-authorize-hmac` | [CardPay predautorizácia](/sk/banks/cardpay#pre-authorization-get-payment-cardpay-authorize-hmac) | HMAC | `MID AMT CURR VS RURL IPC NAME TIMESTAMP`, *`E2E TXN REM TPAY CID ECID TDS_*`* | `HMAC`: MID AMT CURR VS E2E TXN RURL IPC NAME REM TPAY CID ECID TDS_… TIMESTAMP | AMT CURR VS TXN RES AC TRES CID CC RC TID TIMESTAMP |
| `GET /payment/comfortpay-hmac` | [ComfortPay](/sk/banks/comfortpay#hmac-get-payment-comfortpay-hmac) | HMAC | `MID AMT CURR VS RURL TPAY TIMESTAMP IPC NAME`, *`REM`* | `HMAC`: MID AMT CURR VS RURL IPC NAME REM TPAY TIMESTAMP | AMT CURR VS RES AC TRES CID CC RC TID TIMESTAMP |
| `GET /payment/comfortpay-aes256` | [ComfortPay](/sk/banks/comfortpay#aes-get-payment-comfortpay-aes256) | AES | `MID AMT CURR VS RURL TPAY IPC NAME`, *`CS`* | `SIGN`: MID AMT CURR VS CS RURL IPC NAME TPAY | VS RES AC CID |
| `POST /payment/eplatby-hmac` | [VÚB e-platby](/sk/banks/vub-eplatby) | e-platby | formulár: `MID AMT VS RURL`, *`SS CS`* | `SIGN`: MID AMT VS SS CS RURL (veľké písmená) | VS SS RES |
| `GET /payment/sporopay-des` | [SLSP SporoPay](/sk/banks/slsp-sporopay) | SporoPay | `pu_predcislo pu_cislo pu_kbanky suma mena vs ss url param` | `sign1`: polia spojené znakom `;` | `SIGN2`: účet platiteľa, polia, result, real, spojené znakom `;` |

## Server-to-server {#server-to-server}

| Metóda a URL | Platobná brána | Požiadavka | Odpoveď |
|---|---|---|---|
| `GET /payment/cardpay-cancel-purchase` | [CardPay dokončenie, zrušenie, vrátenie platby](/sk/banks/cardpay#capture-release-and-refund-get-payment-cardpay-cancel-purchase) | `MID AMT TID VS TXN REM TIMESTAMP HMAC`, podpísané v tomto poradí | XML `<res>OK</res>` s `Authorization: HMAC=<HMAC of the body>,…`, alebo `<res>FAIL</res><errorCode>10</errorCode>` bez podpisu |
| `POST /cgi-bin/e-commerce/start/api/cardpay/transaction` | [CardPay Direct API](/sk/banks/cardpay-direct-api) | JSON `amount` (v centoch), `applePayToken` alebo `googlePayToken`, *`tdsTermUrl`*; `Authorization: HMAC=<HMAC of the body>` | JSON `status` `OK`, `TDS_AUTH_REQUIRED` (Google Pay od 1000) alebo `FAIL` (od 10000), podpísané v `Authorization` |
| `GET /cgi-bin/e-commerce/start/api/cardpay/transaction/<processingId>` | CardPay Direct API | hlavičky `X-Merchant-Id`, `X-Timestamp`, `Authorization: HMAC=<HMAC of "merchantId;timestamp;processingId">` | JSON `status: OK`, podpísané |
| `POST /cgi-bin/e-commerce/start/api/form/cardpay/transaction/tds` | stránka 3-D Secure | formulár `tdsTermUrl`, `processingId`, … | HTML stránka s odkazom `continue` na `tdsTermUrl` |

## Ostatné {#other}

| URL | Odpovedá |
|---|---|
| `GET /` | úvodná stránka |
| `/tatrabanka/cardpay-direct-api-v0.4.2.pdf`, `-v0.3.pdf` | dokumentácia CardPay Direct API od Tatra banky |
| čokoľvek iné | stránka 404 |
