---
title: VÚB e-platby
description: omnipay-vub proti simulátoru payment-simulator - odoslanie formulára e-platby metódou POST a odpovede OK, FAIL a TOUT.
---

# VÚB e-platby

VÚB e-platby je jediná simulovaná brána, ktorá prijíma formulár odoslaný metódou POST. Príklad používa
[omnipay-vub](https://github.com/tomaj/omnipay-vub) v testovacom režime.

Zdrojový kód: [`examples/vub-eplatby/run.php`](https://github.com/tomaj/payment-simulator/blob/master/examples/vub-eplatby/run.php) ·
spustíte ho príkazom `examples/ci/example.sh vub-eplatby`.

## Platba {#the-payment}

<<< @/../examples/vub-eplatby/run.php#purchase

`getRedirectMethod()` je `POST` a `getRedirectData()` sú polia formulára; vo webovej aplikácii `redirect()`
vykreslí formulár, ktorý prehliadač odošle. Príklad polia odošle sám.

## Odpovede {#the-answers}

<<< @/../examples/vub-eplatby/run.php#complete

omnipay-vub overí podpis cez `VS SS RES` a to, že `VS` je očakávaný. Sumu banka nepodpisuje: berte ju zo svojej
objednávky.

## Výstup {#output}

<<< @/../examples/vub-eplatby/expected-output.txt{text}

`TOUT` nie je výsledok, ktorý VÚB definuje; simulátor ho ponúka pri každej bráne a omnipay-vub ho berie ako
nezaplatenú platbu.

## Čo testuje {#what-it-tests}

- odoslanie formulára metódou POST s podpisom z omnipay-vub dostane **Sign OK**;
- podpísané odpovede prejdú overením v omnipay-vub.

Podrobnosti o endpointe: [VÚB e-platby](/sk/banks/vub-eplatby).
