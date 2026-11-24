---
title: Spustenie
description: Spustite payment-simulator vstavaným PHP serverom, cez Docker Compose (nginx + PHP-FPM), vedľa vašej aplikácie v sieti Compose, v CI, alebo použite verejnú inštanciu.
---

# Spustenie

| Kde | Príkaz | URL |
|---|---|---|
| vstavaný PHP server | `examples/ci/serve.sh` | `http://127.0.0.1:8080` |
| Docker Compose | `docker compose up -d --build` | `http://localhost:8080` (s override súborom) |
| vedľa vašej aplikácie v Compose | služby vo vašom Compose súbore | `http://paymentsandbox.test` |
| verejná inštancia | nič | `https://platby.tomaj.sk` |

## Lokálne PHP {#local-php}

Požiadavky: PHP 8.4, 8.5 alebo 8.6 s `openssl` a Composer.

```bash
git clone https://github.com/tomaj/payment-simulator.git
cd payment-simulator
examples/ci/serve.sh
```

Skript spustí `composer install --no-dev`, keď chýba `vendor/`, vytvorí `temp/` a `log/`, na pozadí spustí

```bash
OPENSSL_CONF=docker/php/openssl-legacy.cnf php -S 127.0.0.1:8080 -t www www/index.php
```

a počká, kým simulátor odpovie. Jeho log ide do `log/php-server.log`, ID procesu do `log/php-server.pid`. Pre iný port
nastavte `SIMULATOR_URL=http://127.0.0.1:9000`. Príkaz `php -S` môžete rovnako dobre spustiť aj sami.

`OPENSSL_CONF` načíta legacy provider OpenSSL 3, ktorý obsahuje jednoduchý DES. Potrebujú ho len endpointy TatraPay
a CardPay s DES; bez neho zlyhajú s chybou „Unable to compute the DES signature“. Do `temp/` (cache Latte a DI)
a `log/` musí byť možné zapisovať.

## Docker {#docker}

Požiadavky: Docker s Compose v2.

```bash
git clone https://github.com/tomaj/payment-simulator.git
cd payment-simulator
cp docker-compose.override.yml.example docker-compose.override.yml   # publishes nginx on port 8080
docker compose up -d --build
docker compose exec payment_simulator composer install
```

Otvorte `http://localhost:8080/`. Stack:

| Služba | Image | Čo robí |
|---|---|---|
| `nginx` | `nginx:stable` | servíruje `www/`, PHP posiela na `payment_simulator:9000`; sieťový alias `paymentsandbox.test` |
| `payment_simulator` | zostavený z `docker/php/Dockerfile` (`php:8.4-fpm`) | PHP-FPM so `soap`, OPcache, Xdebug 3 a legacy providerom OpenSSL |

Repozitár je pripojený do oboch kontajnerov, takže zmena v kóde sa prejaví bez nového zostavenia. Na Linuxe
beží PHP-FPM ako `www-data` a musí môcť zapisovať do pripojených `temp/` a `log/`: `chmod -R a+w temp log`.

Override súbor zverejní port a nastaví Xdebug: beží v režime trigger (reláciu spustíte cez `XDEBUG_TRIGGER`), na porte
9003 a pripája sa na `client_host` z `XDEBUG_CONFIG` (v príklade Docker bridge `172.17.0.1`). `PHP_IDE_CONFIG`
pomenúva server pre mapovanie ciest.

## Vedľa vašej aplikácie {#next-to-your-application}

Pridajte obe služby do Compose projektu vašej aplikácie (alebo dajte oba projekty do jednej siete). Vaša aplikácia
dosiahne simulátor ako `http://paymentsandbox.test`, sieťový alias služby `nginx`:

```php
$gateway->setTestHost('http://paymentsandbox.test');
```

Prehliadač zákazníka sa presmeruje na ten istý host, preto zverejnite aj port a ak prehliadač beží mimo Dockeru,
pridajte `127.0.0.1 paymentsandbox.test` do súboru hosts alebo ako test host použite `http://localhost:8080`.

## V CI {#in-ci}

[Workflow Examples](https://github.com/tomaj/payment-simulator/blob/master/.github/workflows/examples.yml) tohto
repozitára servíruje checkout cez `examples/ci/serve.sh` a spúšťa proti nemu klientov. V inom repozitári ho najprv
naklonujte:

```yaml
- name: Start payment-simulator
  run: |
    git clone --depth 1 https://github.com/tomaj/payment-simulator.git "$RUNNER_TEMP/payment-simulator"
    "$RUNNER_TEMP/payment-simulator/examples/ci/serve.sh"
```

Takto bežia proti simulátoru [príklady omnipay-tatra](https://tomaj.github.io/omnipay-tatra/sk/examples/).

## Verejná inštancia {#the-public-instance}

https://platby.tomaj.sk beží v aktuálnej verzii a je predvoleným `testHost` brán omnipay-*. Netreba nič nastavovať,
vaša návratová URL však musí byť dostupná z vášho prehliadača (je: simulátor na ňu iba odkazuje) a inštanciu
zdieľate so všetkými ostatnými. Pre automatizované testy si spustite vlastnú.
