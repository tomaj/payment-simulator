---
title: 'Tatra banka: TatraPay'
description: Endpointy TatraPay v payment-simulator - HMAC, AES a DES - s parametrami, ktoré vyžadujú, podpisovanými poľami a podpísanou odpoveďou, ktorú posielajú späť do e-shopu.
---

# Tatra banka: TatraPay

Zákazník platí z internet bankingu Tatra banky. Simulátor má jeden endpoint pre každý typ kľúča; produkčné URL banky
sú `https://moja.tatrabanka.sk/cgi-bin/e-commerce/start/tatrapay` (HMAC) a `.../e-commerce.jsp` (AES, DES).

## HMAC: `GET /payment/tatrapay-hmac` {#hmac-get-payment-tatrapay-hmac}

Kľúč: `1` × 128 ([testovacie kľúče](/sk/guide/configuration#test-keys)).

| Parameter | Povinný | Význam |
|---|---|---|
| `MID` | áno | identifikátor obchodníka (ľubovoľná hodnota) |
| `AMT` | áno | suma, `24.90` |
| `CURR` | áno | číselný kód meny podľa ISO 4217, `978` pre EUR |
| `VS` | áno | variabilný symbol |
| *`SS`* | nie | špecifický symbol |
| *`CS`* | nie | konštantný symbol |
| `RURL` | áno | návratová URL adresa |
| *`REM`* | nie | e-mail pre notifikáciu o výsledku platby |
| `TIMESTAMP` | áno | časová pečiatka `ddmmYYYYHHMMSS` v UTC |
| `HMAC` | áno | podpis |

Ostatné parametre TatraPay (`PT`, `DESC`, `RSMS`, `AREDIR`, `LANG`) simulátor prijme a ignoruje.

**Podpis požiadavky:** `MID AMT CURR VS SS CS RURL REM TIMESTAMP`, HMAC-SHA256 s kľúčom dekódovaným z hex, malé
hexadecimálne znaky.

**Odpoveď:** `RURL?VS=…[&SS=…]&RES=OK|FAIL|TOUT&AMT=…&CURR=…&CS=…&TIMESTAMP=…&HMAC=…`, HMAC nad
`AMT CURR VS SS CS RES TID TIMESTAMP` s prázdnym `TID` (simulátor ho neposiela). `TIMESTAMP` je z požiadavky.

<<< @/../examples/plain-php/run.php#request

## AES: `GET /payment/tatrapay-aes256` {#aes-get-payment-tatrapay-aes256}

Kľúč: `1` × 64.

| Parameter | Povinný |
|---|---|
| `MID`, `AMT`, `CURR`, `VS`, `RURL`, `SIGN` | áno |
| *`SS`*, *`CS`* | nie |

**Podpis požiadavky:** `MID AMT CURR VS SS CS RURL`: prvých 16 bajtov SHA-1, AES-128-ECB s prvými 16 bajtmi kľúča,
prvých 32 hexadecimálnych znakov, veľkými písmenami.

**Odpoveď:** `RURL?VS=…[&SS=…]&RES=OK|FAIL|TOUT&SIGN=…`, podpísaná nad `VS SS RES` (prázdny `SS` nič nepridá), ako
to určuje *Technická príručka k službe TatraPay* Tatra banky (verzia 3.3). omnipay-tatra 5 overuje ten istý reťazec;
omnipay-tatra 4.x overuje `VS RES` a odpoveď so `SS` odmietne.

## DES: `GET /payment/tatrapay-des` {#des-get-payment-tatrapay-des}

Kľúč: `11111111`. Parametre a podpisované polia sú rovnaké ako pri AES; podpisom je SHA-1 zašifrovaná cez DES-ECB,
prvých 16 hexadecimálnych znakov, veľkými písmenami. Vyžaduje legacy provider OpenSSL
([Spustenie](/sk/guide/running#local-php)).

**Odpoveď:** ako pri AES, podpísaná cez DES nad `VS SS RES`.

## Výsledky {#results}

| Odkaz | `RES` | omnipay-tatra |
|---|---|---|
| `continue ok` | `OK` | `isSuccessful()` |
| `continue fail` | `FAIL` | ani jedno |
| `continue timeout` | `TOUT` | `isPending()` |

Príklady: [omnipay-tatra](/sk/examples/omnipay-tatra), [čisté PHP](/sk/examples/plain-php).
