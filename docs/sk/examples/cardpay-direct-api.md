---
title: CardPay Direct API
description: Transakcie Apple Pay a Google Pay proti CardPay Direct API simulátora payment-simulator v čistom PHP - podpísaný JSON, stránka 3-D Secure a kontrola transakcie.
---

# CardPay Direct API

Direct API je JSON cez HTTPS, podpísaný v hlavičke `Authorization` v oboch smeroch. Príklad je úplný klient v čistom
PHP.

Zdrojový kód: [`examples/cardpay-direct-api/run.php`](https://github.com/tomaj/payment-simulator/blob/master/examples/cardpay-direct-api/run.php) ·
spustíte ho príkazom `examples/ci/example.sh cardpay-direct-api`.

## Klient {#the-client}

<<< @/../examples/cardpay-direct-api/run.php#client

Každá odpoveď sa overí skôr, než sa dekóduje: HMAC pokrýva presné bajty tela.

## Transakcie {#transactions}

<<< @/../examples/cardpay-direct-api/run.php#transaction

O stave rozhoduje suma: Google Pay od 10.00 (1000 centov) vyžaduje 3-D Secure, čokoľvek od 100.00 zlyhá.

## 3-D Secure {#_3-d-secure}

<<< @/../examples/cardpay-direct-api/run.php#tds

V e-shope vložte `tdsRedirectionFormHtml` do stránky; formulár sa odošle sám.

## Kontrola transakcie {#checking-a-transaction}

<<< @/../examples/cardpay-direct-api/run.php#check

## Nesprávny podpis {#a-wrong-signature}

<<< @/../examples/cardpay-direct-api/run.php#unsigned

## Výstup {#output}

<<< @/../examples/cardpay-direct-api/expected-output.txt{text}

Identifikátory a autorizačný kód sú náhodné, preto príklad vypisuje iba stabilné polia.

## Čo testuje {#what-it-tests}

- podpisy požiadaviek cez JSON telo a cez `merchantId;timestamp;processingId`;
- podpis každej odpovede;
- stav pre každú peňaženku a sumu, formulár a stránku 3-D Secure;
- HTTP 400 pri nesprávnom podpise.

Podrobnosti o endpointe: [CardPay Direct API](/sk/banks/cardpay-direct-api).
