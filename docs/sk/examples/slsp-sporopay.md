---
title: SLSP SporoPay
description: omnipay-slsp proti simulátoru payment-simulator - presmerovanie SporoPay podpísané 3DES a odpovede OK, FAIL a TOUT.
---

# SLSP SporoPay

SporoPay podpisuje cez 3DES polia spojené bodkočiarkami. Príklad používa
[omnipay-slsp](https://github.com/tomaj/omnipay-slsp) v testovacom režime.

Zdrojový kód: [`examples/slsp-sporopay/run.php`](https://github.com/tomaj/payment-simulator/blob/master/examples/slsp-sporopay/run.php) ·
spustíte ho príkazom `examples/ci/example.sh slsp-sporopay`.

## Platba {#the-payment}

<<< @/../examples/slsp-sporopay/run.php#purchase

`accountNumberPrefix` a `accountNumber` je váš účet (`pu_predcislo`, `pu_cislo`); `param` sa vráti nezmenený.

## Odpovede {#the-answers}

<<< @/../examples/slsp-sporopay/run.php#complete

## Výstup {#output}

<<< @/../examples/slsp-sporopay/expected-output.txt{text}

## Čo testuje {#what-it-tests}

- 3DES `sign1` z omnipay-slsp dostane **Sign OK**;
- `SIGN2` každej odpovede prejde overením v omnipay-slsp, vrátane účtu platiteľa a poľa `real`, ktoré pridáva simulátor.

Podrobnosti o endpointe: [SLSP SporoPay](/sk/banks/slsp-sporopay).
