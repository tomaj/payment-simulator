---
title: Dokončenie a vrátenie platby CardPay
description: Endpoint simulátora payment-simulator na dokončenie, zrušenie predautorizácie a vrátenie platby CardPay v čistom PHP - podpísaný GET a XML odpoveď podpísaná v hlavičke Authorization.
---

# Dokončenie a vrátenie platby CardPay

Po predautorizácii server e-shopu podpísaným `GET` požiadavkou predautorizáciu dokončí (`CPA`), zruší (`SPA`) alebo
platbu vráti (`CB`); banka odpovie XML podpísaným v hlavičke `Authorization`. V omnipay-tatra to robí
`cancelAuthorize()`; príklad ukazuje protokol v čistom PHP.

Zdrojový kód: [`examples/cardpay-capture/run.php`](https://github.com/tomaj/payment-simulator/blob/master/examples/cardpay-capture/run.php) ·
spustíte ho príkazom `examples/ci/example.sh cardpay-capture`.

## Požiadavka {#the-request}

<<< @/../examples/cardpay-capture/run.php#request

## Overenie odpovede {#verifying-the-answer}

<<< @/../examples/cardpay-capture/run.php#verify

HMAC pokrýva telo presne tak, ako prišlo, vrátane XML deklarácie a bielych znakov; pred overením ho neparsujte
a neserializujte nanovo.

## Dokončenie, zrušenie, vrátenie {#capture-release-refund}

<<< @/../examples/cardpay-capture/run.php#operations

## Nesprávny podpis {#a-wrong-signature}

<<< @/../examples/cardpay-capture/run.php#wrong

Odpoveď `FAIL` nie je podpísaná. Klient nesmie nepodpísaný `FAIL` brať ako dôkaz čohokoľvek; znamená iba, že operácia
sa nevykonala.

## Výstup {#output}

<<< @/../examples/cardpay-capture/expected-output.txt{text}

## Čo testuje {#what-it-tests}

- podpis požiadavky cez `MID AMT TID VS TXN REM TIMESTAMP`;
- podpísanú XML odpoveď pre `CPA`, `SPA` a `CB`;
- nepodpísaný `FAIL` s chybovým kódom `10`.

Podrobnosti o endpointe: [CardPay](/sk/banks/cardpay#capture-release-and-refund-get-payment-cardpay-cancel-purchase).
