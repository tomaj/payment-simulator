---
title: 'Tatra banka: ComfortPay'
description: Endpointy ComfortPay v payment-simulator - platba CardPay, ktorá zaregistruje kartu (TPAY=Y), s kľúčom HMAC alebo AES, a ID karty, ktoré vráti.
---

# Tatra banka: ComfortPay

ComfortPay je platba CardPay, ktorá zaregistruje kartu (`TPAY=Y`); banka vráti ID karty (`CID`), z ktorej e-shop
neskôr strháva platby cez webovú službu SOAP Tatra banky. Simulátor pokrýva platbu s presmerovaním. **Webová služba
SOAP sa nesimuluje**: v testovacom režime na tieto požiadavky odpovedá samotná omnipay-tatra.

## HMAC: `GET /payment/comfortpay-hmac` {#hmac-get-payment-comfortpay-hmac}

| Parameter | Povinný |
|---|---|
| `MID`, `AMT`, `CURR`, `VS`, `RURL`, `TPAY`, `TIMESTAMP`, `IPC`, `NAME`, `HMAC` | áno |
| *`REM`* | nie |

**Podpis požiadavky:** `MID AMT CURR VS RURL IPC NAME REM TPAY TIMESTAMP`.

**Odpoveď:** `RURL?VS=…&AMT=…&CURR=…&RES=…&TRES=…&AC=111111&CID=111111&CC=1111********1111&RC=123&TIMESTAMP=…&HMAC=…`,
HMAC nad `AMT CURR VS RES AC TRES CID CC RC TID TIMESTAMP` s prázdnym `TID`; `TRES` sa rovná `RES`.

## AES: `GET /payment/comfortpay-aes256` {#aes-get-payment-comfortpay-aes256}

| Parameter | Povinný |
|---|---|
| `MID`, `AMT`, `CURR`, `VS`, `RURL`, `TPAY`, `IPC`, `NAME`, `SIGN` | áno |
| *`CS`* | nie |

**Podpis požiadavky:** `MID AMT CURR VS CS RURL IPC NAME TPAY`.

**Odpoveď:** `RURL?VS=…&RES=…&TRES=…&AC=111111&CID=111111&SIGN=…`, podpísaná nad `VS RES AC CID` (omnipay-tatra
overuje `VS TRES AC CID`; `TRES` sa rovná `RES`).

ComfortPay nemá variant s DES.

## ID karty {#the-card-id}

Každá odpoveď, aj `FAIL` a `TOUT`, nesie `CID=111111`. S omnipay-tatra v testovacom režime `charge()` schváli nepárne
ID karty a zamietne párne, takže platba z karty simulátora prejde.

Príklad: [omnipay-tatra](/sk/examples/omnipay-tatra) a
[predplatné cez ComfortPay](https://tomaj.github.io/omnipay-tatra/examples/comfortpay-recurring) v dokumentácii omnipay-tatra.
