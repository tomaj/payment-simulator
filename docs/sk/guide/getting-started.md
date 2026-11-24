---
title: Začíname
description: Spustite payment-simulator lokálne jedným príkazom, pošlite mu podpísanú požiadavku TatraPay a sledujte jeho podpísané odpovede späť do vášho e-shopu.
---

# Začíname

payment-simulator nahrádza platobné stránky slovenských bánk. Váš e-shop presmeruje zákazníka namiesto banky na
simulátor; ten overí podpis požiadavky verejným testovacím kľúčom a ponúkne zákazníkovi tri cesty späť na vašu
návratovú URL: úspešnú, neúspešnú a časovo vypršanú platbu, každú podpísanú tak, ako ju podpisuje banka.
Nič neopustí váš počítač a nepohnú sa žiadne peniaze.

## Požiadavky {#requirements}

- PHP 8.4, 8.5 alebo 8.6 s `openssl` a Composer, **alebo** Docker s Compose v2
- e-shop alebo knižnica, ktorú chcete testovať, nastavená tak, aby posielala platby na simulátor

## Spustenie {#start-it}

```bash
git clone https://github.com/tomaj/payment-simulator.git
cd payment-simulator
examples/ci/serve.sh            # composer install, then php -S 127.0.0.1:8080
```

[`examples/ci/serve.sh`](https://github.com/tomaj/payment-simulator/blob/master/examples/ci/serve.sh) nainštaluje
závislosti, keď chýba `vendor/`, spustí vstavaný PHP server nad `www/` na adrese `SIMULATOR_URL` (predvolene
`http://127.0.0.1:8080`) s legacy providerom OpenSSL, ktorý potrebujú brány s DES, a počká, kým simulátor odpovie.
Zastavíte ho príkazom `kill "$(cat log/php-server.pid)"`. Docker a podrobnosti opisuje stránka [Spustenie](./running).

Úvodnú stránku uvidíte na `http://127.0.0.1:8080/`.

## Odoslanie platby {#send-a-payment}

Každá brána má vlastnú URL pod `/payment/`. Platba TatraPay podpísaná testovacím kľúčom HMAC (128-krát `1`):

<<< @/../examples/plain-php/run.php#request

Simulátor odpovie HTML stránkou, ktorá zobrazí podpis, ktorý ste poslali, podpis, ktorý vypočítal, parametre a:

- **Sign OK** a tri odkazy, `continue ok`, `continue fail` a `continue timeout`, keď podpis sedí;
- **Wrong sign**, keď nesedí (bez odkazov);
- **Missing parameters** so zoznamom a HTTP 400, keď chýba povinný parameter.

Každý odkaz vedie na vašu `RURL` s parametrami výsledku platby od banky a ich podpisom:

```text
https://shop.example/payment/return?VS=2026001&RES=OK&AMT=24.90&CURR=978&CS=&TIMESTAMP=24112026101500&HMAC=...
```

Handler vašej návratovej URL overí tento podpis presne tak, ako by overil podpis banky. Tu ručne:

<<< @/../examples/plain-php/run.php#verify

Celý skript a jeho výstup nájdete v [príklade v čistom PHP](/sk/examples/plain-php):

<<< @/../examples/plain-php/expected-output.txt{text}

## S bránou Omnipay {#with-an-omnipay-gateway}

Brány [omnipay-tatra](https://tomaj.github.io/omnipay-tatra/sk/), [omnipay-vub](https://tomaj.github.io/omnipay-vub/sk/) a
[omnipay-slsp](https://tomaj.github.io/omnipay-slsp/sk/) v testovacom režime posielajú platby na simulátor;
nastavte `testHost` na vašu inštanciu:

<<< @/../examples/omnipay-tatra/run.php#config

Pozrite [Použitie s Omnipay](./omnipay).

## Ďalšie kroky {#next-steps}

- [Ako to funguje](./how-it-works): stránky, verdikty a podpísané odpovede
- [Banky](/sk/banks/): každý endpoint s parametrami, podpisovanými poľami a návratom, ktorý vráti
- [Konfigurácia a testovacie kľúče](./configuration)
- [Príklady](/sk/examples/): šesť klientov, od čistého PHP po CardPay Direct API, testovaných v CI
