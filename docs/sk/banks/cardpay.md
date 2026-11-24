---
title: 'Tatra banka: CardPay'
description: Endpointy CardPay v payment-simulator - platby s kľúčom HMAC, AES a DES, endpoint predautorizácie s poliami 3-D Secure a server-to-server dokončenie, zrušenie predautorizácie a vrátenie platby.
---

# Tatra banka: CardPay

Zákazník platí kartou na stránke banky. Okrem platby vie CardPay sumu predautorizovať a e-shop ju neskôr
server-to-server požiadavkou dokončí, zruší alebo vráti.

## HMAC: `GET /payment/cardpay-hmac` {#hmac-get-payment-cardpay-hmac}

| Parameter | Povinný | Význam |
|---|---|---|
| `MID`, `AMT`, `CURR`, `VS`, `RURL` | áno | ako pri [TatraPay](./tatrapay#hmac-get-payment-tatrapay-hmac) |
| `IPC` | áno | IP adresa zákazníka |
| `NAME` | áno | meno držiteľa karty |
| *`REM`* | nie | e-mail pre notifikáciu o výsledku platby |
| `TIMESTAMP` | áno | časová pečiatka `ddmmYYYYHHMMSS` v UTC |
| `HMAC` | áno | podpis |

**Podpis požiadavky:** `MID AMT CURR VS RURL IPC NAME REM TIMESTAMP` (HMAC-SHA256, malé hexadecimálne znaky).

**Odpoveď:** `RURL?VS=…&TXN=&RES=…&TRES=&AC=111111&AMT=…&CURR=…&CID=&CC=1111********1111&AC=111111&TIMESTAMP=…&HMAC=…`,
HMAC nad `AMT CURR VS TXN RES AC TRES CID CC TID TIMESTAMP` s prázdnymi `TXN`, `TRES`, `CID` a `TID`. Je to ten istý
reťazec, ktorý overuje omnipay-tatra (`AMT CURR VS RES AC TRES CC RC TID TIMESTAMP`), pretože každé pole, ktoré
v oboch zoznamoch nie je, je prázdne.

## AES: `GET /payment/cardpay-aes256` a DES: `GET /payment/cardpay-des` {#aes-get-payment-cardpay-aes256-and-des-get-payment-cardpay-des}

| Parameter | Povinný |
|---|---|
| `MID`, `AMT`, `CURR`, `VS`, `RURL`, `IPC`, `NAME`, `SIGN` | áno |
| *`CS`* | nie |

**Podpis požiadavky:** `MID AMT CURR VS CS RURL IPC NAME` (AES s kľúčom `1` × 64, DES s `11111111`; veľké
hexadecimálne znaky).

**Odpoveď:** `RURL?VS=…&AC=111111&RES=…&SIGN=…`, podpísaná nad `VS RES AC`.

## Predautorizácia: `GET /payment/cardpay-authorize-hmac` {#pre-authorization-get-payment-cardpay-authorize-hmac}

Endpoint pre `authorize()` z omnipay-tatra (`TXN=PA`) a pre registráciu karty.

| Parameter | Povinný |
|---|---|
| `MID`, `AMT`, `CURR`, `VS`, `RURL`, `IPC`, `NAME`, `TIMESTAMP`, `HMAC` | áno |
| *`E2E`*, *`TXN`*, *`REM`*, *`TPAY`*, *`CID`*, *`ECID`* | nie |
| *`TDS_CARDHOLDER`*, *`TDS_EMAIL`*, *`TDS_MOBILE_PHONE`*, *`TDS_BILL_CITY`*, *`TDS_BILL_COUNTRY`*, *`TDS_BILL_ADDRESS1`*, *`TDS_BILL_ADDRESS2`*, *`TDS_BILL_ZIP`*, *`TDS_SHIP_CITY`*, *`TDS_SHIP_COUNTRY`*, *`TDS_SHIP_ADDRESS1`*, *`TDS_SHIP_ADDRESS2`*, *`TDS_SHIP_ZIP`*, *`TDS_ADDR_MATCH`* | nie |

**Podpis požiadavky:** `MID AMT CURR VS E2E TXN RURL IPC NAME REM TPAY CID ECID TDS_CARDHOLDER TDS_EMAIL
TDS_MOBILE_PHONE TDS_BILL_CITY TDS_BILL_COUNTRY TDS_BILL_ADDRESS1 TDS_BILL_ADDRESS2 TDS_BILL_ZIP TDS_SHIP_CITY
TDS_SHIP_COUNTRY TDS_SHIP_ADDRESS1 TDS_SHIP_ADDRESS2 TDS_SHIP_ZIP TDS_ADDR_MATCH TIMESTAMP`.

**Odpoveď:** `RURL?AMT=…&CURR=…&VS=…&TXN=…&RES=…&AC=…&TRES=…&CID=…&CC=************1234&RC=…&TID=…&TIMESTAMP=…&HMAC=…`,
HMAC nad `AMT CURR VS TXN RES AC TRES CID CC RC TID TIMESTAMP`. Hodnoty závisia od `TPAY`:

| | `TPAY=Y`, `RES=OK` | `TPAY=Y`, `FAIL`/`TOUT` | bez `TPAY=Y` |
|---|---|---|---|
| `AC` (autorizačný kód) | `31232` | prázdne | prázdne |
| `TRES` | `OK` | `FAIL` / `TOUT` | prázdne |
| `CID` (ID karty) | `123456` | prázdne | prázdne |
| `RC` | prázdne | `01` | `01` |
| `TID` (ID transakcie) | `567898` | prázdne | prázdne |

Pošlite `TPAY=Y`, aby ste dostali ID transakcie pre dokončenie nižšie.

## Dokončenie, zrušenie predautorizácie a vrátenie platby: `GET /payment/cardpay-cancel-purchase` {#capture-release-and-refund-get-payment-cardpay-cancel-purchase}

Server-to-server endpoint pre `cancelAuthorize()` z omnipay-tatra (v banke `txn_process.jsp`).

| Parameter | Povinný | Význam |
|---|---|---|
| `MID`, `AMT`, `TID`, `TXN`, `TIMESTAMP`, `HMAC` | očakávaný | `TXN`: `CPA` dokončenie, `SPA` zrušenie, `CB` vrátenie platby |
| *`VS`*, *`REM`* | nie | |

**Podpis požiadavky:** `MID AMT TID VS TXN REM TIMESTAMP` (HMAC, malé hexadecimálne znaky). Endpoint chýbajúce
parametre nevypisuje: chýbajúci parameter jednoducho spôsobí nesprávny podpis.

**Odpoveď** (`Content-Type: text/xml`), keď podpis sedí:

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

s hlavičkou `Authorization: HMAC=<HMAC of the body>,ECDSA=…, ECDSA_KEY=1` (HMAC tela odpovede). Pri nesprávnom podpise
je `<result>` rovný `<res>FAIL</res><errorCode>10</errorCode>` a hlavička `Authorization` chýba. Hodnoty v elemente
`<request>` sú kópie parametrov s XML escapovaním.

Príklady: [omnipay-tatra](/sk/examples/omnipay-tatra) (platba a predautorizácia),
[CardPay: dokončenie a vrátenie platby](/sk/examples/cardpay-capture).
