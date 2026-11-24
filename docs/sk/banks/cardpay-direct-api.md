---
title: CardPay Direct API
description: CardPay Direct API v payment-simulator - transakcie Apple Pay a Google Pay ako podpísaný JSON, stránka 3-D Secure a kontrola transakcie so stavmi, ktoré určuje suma.
---

# CardPay Direct API

CardPay Direct API Tatra banky prijíma tokeny Apple Pay a Google Pay z vašej stránky a odpovedá v JSON. Simulátor
implementuje jeho endpointy pre transakciu a kontrolu transakcie aj stránku 3-D Secure, s cestami ako v banke, takže
klient zmení iba host. Dokumentácia API od Tatra banky je v
[`www/tatrabanka/`](https://github.com/tomaj/payment-simulator/tree/master/www/tatrabanka) (v0.4.2 a v0.3, PDF).

Požiadavky aj odpovede sú podpísané cez HMAC-SHA256 testovacím kľúčom HMAC (`1` × 128) v hlavičke `Authorization`:
`Authorization: HMAC=<lowercase hex>` (malé hexadecimálne znaky).

## `POST /cgi-bin/e-commerce/start/api/cardpay/transaction` {#post-cgi-bin-e-commerce-start-api-cardpay-transaction}

**Požiadavka:** telo v JSON, podpísané presne tak, ako bolo odoslané.

| Pole | Význam |
|---|---|
| `amount` | suma v centoch, povinná a číselná |
| `applePayToken` alebo `googlePayToken` | token peňaženky (nekontroluje sa) |
| *`tdsTermUrl`* | kam stránka 3-D Secure vráti zákazníka |

**Stav podľa sumy:**

| Peňaženka | `amount` | `status` |
|---|---|---|
| Apple Pay | menej ako 10000 | `OK` |
| Google Pay | menej ako 1000 | `OK` |
| Google Pay | 1000 až 9999 | `TDS_AUTH_REQUIRED` s `tdsRedirectionFormHtml` |
| ľubovoľná | 10000 a viac | `FAIL` |

**Odpoveď:**

```json
{
  "processingId": 12345,
  "status": "OK",
  "transactionId": 145678,
  "transactionData": { "authorizationCode": "MH1234", "responseCode": "00" }
}
```

`processingId` (10000 až 19999), `transactionId` (100000 až 199999) a `authorizationCode` (dve písmená, štyri číslice)
sú náhodné; `responseCode` je vždy `00`. Odpoveď nesie
`Authorization: HMAC=<HMAC of the JSON body>, ECDSA=ecdsa, ECDSA_KEY=1` (HMAC JSON tela).

**Chyby** (HTTP 400, bez podpisu): `{"error":"Invalid authorization header"}` pri chýbajúcom alebo nesprávnom podpise,
`{"error":"Missing or invalid amount"}`. Telo, ktoré nie je JSON, dostane HTTP 200 s
`{"error":"Cannot process Google Pay token"}`.

## 3-D Secure: `POST /cgi-bin/e-commerce/start/api/form/cardpay/transaction/tds` {#_3-d-secure-post-cgi-bin-e-commerce-start-api-form-cardpay-transaction-tds}

Pri `TDS_AUTH_REQUIRED` je `tdsRedirectionFormHtml` HTML formulár, ktorý sa sám odošle a pošle `tdsTermUrl`,
`processingId`, `timestamp` a `signature` na túto stránku, na tú istú schému, host a port, na ktoré prišla požiadavka
na transakciu. Stránka zobrazí parametre a odkaz `continue` na `tdsTermUrl`. Neposiela hlavičku `X-Frame-Options`,
takže sa dá zobraziť v iframe.

## `GET /cgi-bin/e-commerce/start/api/cardpay/transaction/<processingId>` {#get-cgi-bin-e-commerce-start-api-cardpay-transaction-processingid}

| Hlavička | Hodnota |
|---|---|
| `X-Merchant-Id` | identifikátor obchodníka (ľubovoľný) |
| `X-Timestamp` | časová pečiatka |
| `Authorization` | `HMAC=<HMAC of "merchantId;timestamp;processingId">` |

**Odpoveď:** `{"processingId": "<processingId>", "status": "OK", "transactionId": …, "transactionData": {…}}`, podpísaná
rovnako ako odpoveď na transakciu. Kontrola vždy odpovie `OK`.

Príklad: [CardPay Direct API](/sk/examples/cardpay-direct-api).
