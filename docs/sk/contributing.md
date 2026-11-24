---
title: Ako prispieť
description: Ako prispieť do simulátora payment-simulator - kontroly, testy, príklady a táto dokumentácia.
---

# Ako prispieť

Pull requesty sú vítané v [tomaj/payment-simulator](https://github.com/tomaj/payment-simulator). Pravidlá projektu
sú v [CONTRIBUTING.md](https://github.com/tomaj/payment-simulator/blob/master/CONTRIBUTING.md); účasť sa riadi
[kódexom správania](https://github.com/tomaj/payment-simulator/blob/master/CODE_OF_CONDUCT.md). Zraniteľnosti
hláste súkromne, pozrite [SECURITY.md](https://github.com/tomaj/payment-simulator/blob/master/SECURITY.md).

## Kontroly {#checks}

```bash
composer install
composer check                                             # code style, PHPStan, Rector, tests
OPENSSL_CONF=docker/php/openssl-legacy.cnf composer test   # include the DES tests
```

CI spúšťa testy na PHP 8.4, 8.5 a 8.6 (8.7 nightly môže zlyhať), vyžaduje 90 % pokrytia príkazov a zostaví Docker
image.

## Zmena v bráne {#a-change-to-a-gateway}

Podpis aj návratová URL sú protokol: zmena pokazí každého klienta, ktorý sa riadi bankou. Porovnajte stránky pred
zmenou a po nej na rovnakých požiadavkách, overte hodnoty voči nezávislej implementácii (`openssl` CLI,
`tests/Model/GatewaySignTest.php`) a udržte `tests/Functional/OmnipayLibrariesTest.php` zelený.

## Príklady {#examples}

[Príklady](/sk/examples/) sú klienti v `examples/<name>/` (`run.php`, `e2e.sh`, `expected-output.txt`). Workflow
Examples spustí kód pull requestu a pustí proti nemu každý z nich. Lokálne jeden spustíte príkazom
`examples/ci/example.sh <name>`; po zámernej zmene výstupu príklad spustite a commitnite nový `expected-output.txt`.

## Táto dokumentácia {#this-documentation}

Web je VitePress v `docs/`; kód na jeho stránkach sa vkladá z `examples/` a `tests/DocsSnippetsTest.php` spúšťa PHP
bloky z README.md a `docs/`. Anglické stránky sú v koreni `docs/`, slovenské v `docs/sk/` s rovnakými cestami;
kontrola v CI (`npm run docs:locales`) zlyhá, keď stránke chýba náprotivok v druhom jazyku.

```bash
cd docs
npm ci
npm run docs:dev     # local preview
npm run docs:links   # build and check every link, as CI does
npm run images       # after a change to scripts/illustrations.py: SVGs, og.png, icons (needs Chrome)
```

Logo, ilustrácia na úvodnej stránke, diagram a obrázok pre sociálne siete sa generujú skriptom
`docs/scripts/illustrations.py`, ilustrácia a diagram v angličtine aj v slovenčine (súbory `-sk`). Upravujte skript,
nie SVG súbory.
