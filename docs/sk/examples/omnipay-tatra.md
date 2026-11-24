---
title: omnipay-tatra
description: omnipay-tatra v testovacom režime proti simulátoru payment-simulator - TatraPay (aj so špecifickým symbolom pri HMAC a AES kľúči), CardPay, ComfortPay a predautorizácia CardPay, každá s odpoveďou OK, FAIL a TOUT.
---

# omnipay-tatra

[omnipay-tatra](https://tomaj.github.io/omnipay-tatra/sk/) je klient, pre ktorý vznikli endpointy Tatra banky.
Príklad spustí proti simulátoru šesť tokov a každý dokončí všetkými tromi odpoveďami, medzi nimi aj TatraPay so
špecifickým symbolom (`ss`) pri HMAC a AES kľúči.

Zdrojový kód: [`examples/omnipay-tatra/run.php`](https://github.com/tomaj/payment-simulator/blob/master/examples/omnipay-tatra/run.php) ·
spustíte ho príkazom `examples/ci/example.sh omnipay-tatra`.

## Konfigurácia {#configuration}

<<< @/../examples/omnipay-tatra/run.php#config

## Cesta tam a späť {#the-round-trip}

<<< @/../examples/omnipay-tatra/run.php#round-trip

| Tok | Začína | URL simulátora | Dokončuje sa |
|---|---|---|---|
| TatraPay | `purchase()` | `/payment/tatrapay-hmac` | `completePurchase()` |
| TatraPay so SS | `purchase()` s `ss` | `/payment/tatrapay-hmac` | `completePurchase()` s rovnakým `ss` |
| TatraPay AES so SS | `purchase()` s `ss`, AES kľúč (64 znakov) | `/payment/tatrapay-aes256` | `completePurchase()`, návrat podpísaný cez `VS SS RES` |
| CardPay | `purchase()` | `/payment/cardpay-hmac` | `completePurchase()` |
| ComfortPay | `purchase()` (`TPAY=Y`) | `/payment/comfortpay-hmac` | `completePurchase()` |
| CardPay predautorizácia | `authorize()` s `tpay` `Y` | `/payment/cardpay-authorize-hmac` | `completeAuthorize()` |

## Výstup {#output}

<<< @/../examples/omnipay-tatra/expected-output.txt{text}

`timeout` je čakajúci (pending) iba pri TatraPay: CardPay a ComfortPay výsledok timeout nepoznajú, takže `RES=TOUT`
je pri nich jednoducho neúspešná platba.

## Čo testuje {#what-it-tests}

- každá požiadavka, ktorú omnipay-tatra v testovacom režime zostaví, dostane **Sign OK**, teda podpisované polia sedia;
- každá podpísaná odpoveď simulátora prejde overením v omnipay-tatra, teda sedia aj podpisy návratu;
- `isSuccessful()` a `isPending()` zodpovedajú výsledku.

Vlastná PHPUnit sada simulátora (`tests/Functional/OmnipayLibrariesTest.php`) robí to isté priamo v procese
s omnipay-tatra (HMAC, AES a DES kľúče, so špecifickým symbolom aj bez neho) a s vydanými verziami omnipay-vub
a omnipay-slsp.
