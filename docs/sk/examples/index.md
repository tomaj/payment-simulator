---
title: Príklady
description: Šesť klientov, ktoré v CI bežia proti simulátoru payment-simulator - omnipay-tatra, klient v čistom PHP, VÚB e-platby, SLSP SporoPay, CardPay Direct API a dokončenie a vrátenie platby CardPay.
---

# Príklady

Každý príklad je klient v [`examples/`](https://github.com/tomaj/payment-simulator/tree/master/examples), ktorý
komunikuje so simulátorom tak, ako by to robil e-shop. [Workflow Examples](https://github.com/tomaj/payment-simulator/blob/master/.github/workflows/examples.yml)
spustí simulátor z kódu pull requestu, pustí proti nemu každý príklad a porovná výstup s jeho
`expected-output.txt`. Zmena, ktorá pokazí protokol niektorej banky, tak neprejde cez CI. Kód na týchto stránkach sa
vkladá priamo z týchto súborov.

| Príklad | Klient | Čo pokrýva |
|---|---|---|
| [omnipay-tatra](./omnipay-tatra) | `tomaj/omnipay-tatra` | TatraPay, CardPay, ComfortPay a predautorizáciu CardPay, každú s výsledkom OK, FAIL a TOUT |
| [Klient v čistom PHP](./plain-php) | žiadny | požiadavku TatraPay a odpovede podpísané ručne; **Wrong sign** a **Missing parameters** |
| [VÚB e-platby](./vub-eplatby) | `tomaj/omnipay-vub` | odoslanie formulára metódou POST a odpovede naň |
| [SLSP SporoPay](./slsp-sporopay) | `tomaj/omnipay-slsp` | presmerovanie podpísané 3DES a odpovede naň |
| [CardPay Direct API](./cardpay-direct-api) | žiadny | JSON transakcie Apple Pay a Google Pay, 3-D Secure, kontrolu transakcie |
| [Dokončenie a vrátenie platby CardPay](./cardpay-capture) | žiadny | server-to-server dokončenie, zrušenie predautorizácie a vrátenie platby s podpísaným XML |

## Spustenie {#run-them}

```bash
git clone https://github.com/tomaj/payment-simulator.git
cd payment-simulator
examples/ci/example.sh plain-php    # or omnipay-tatra, vub-eplatby, slsp-sporopay, cardpay-direct-api, cardpay-capture
```

[`examples/ci/example.sh`](https://github.com/tomaj/payment-simulator/blob/master/examples/ci/example.sh) spustí
checkout cez [`examples/ci/serve.sh`](https://github.com/tomaj/payment-simulator/blob/master/examples/ci/serve.sh),
ak na adrese `SIMULATOR_URL` ešte nič neodpovedá, nainštaluje vlastné závislosti príkladu a spustí jeho `e2e.sh`.
Ten pri zhode výstupu vypíše `ok: <example>`. Príklady s Omnipay berú aktuálny `master` knižníc z GitHubu.
Požiadavky: PHP 8.4+ s rozšírením `openssl` (pre omnipay-tatra aj `soap`), Composer, curl.

Príklady s presmerovaním zdieľajú
[`examples/_support/simulator.php`](https://github.com/tomaj/payment-simulator/blob/master/examples/_support/simulator.php),
ktorý hrá úlohu prehliadača zákazníka: otvorí platobnú stránku a prečíta z nej verdikt, chýbajúce parametre a tri
podpísané odkazy späť.

<<< @/../examples/_support/simulator.php#open

## V iných repozitároch {#in-other-repositories}

[omnipay-tatra](https://tomaj.github.io/omnipay-tatra/sk/examples/) spúšťa vlastné príklady proti klonu tohto
repozitára, ktorý štartuje rovnakým spôsobom; pozrite [Spustenie v CI](/sk/guide/running#in-ci).
