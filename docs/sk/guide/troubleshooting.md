---
title: Riešenie problémov
description: Ako vyriešiť „Wrong sign“, „Missing parameters“, chyby DES, odmietnuté návraty a problémy s pripojením k payment-simulatoru.
---

# Riešenie problémov

## „Wrong sign“ {#wrong-sign}

Stránka zobrazí **Input Sign**, ktorý ste poslali, a **Computed Sign**. Skontrolujte v tomto poradí:

1. **Kľúč.** Použite [testovací kľúč](./configuration#test-keys) pre algoritmus brány, nie váš produkčný kľúč.
2. **URL.** `/payment/tatrapay-hmac` očakáva podpis HMAC, `/payment/tatrapay-aes256` podpis AES; ktorý z nich vaša
   knižnica vypočíta, rozhoduje dĺžka kľúča.
3. **Poradie polí.** Podpisované polia každého endpointu sú na stránkach [Banky](/sk/banks/). Polia sa spájajú bez
   oddeľovačov (SporoPay: s `;`) a prázdne nepovinné pole nepridá nič.
4. **Špecifický symbol.** Pri TatraPay a VÚB sa `SS` podpisuje medzi `VS` a `CS`.
5. **Veľkosť písmen.** Hexadecimálne podpisy sa porovnávajú presne: HMAC Tatra banky je malými písmenami, AES/DES
   a VÚB veľkými.
6. **Časová pečiatka.** Požiadavky s HMAC podpisujú `TIMESTAMP`; pošlite tú istú hodnotu, ktorú ste podpísali.
7. **Kódovanie.** Podpis sa počíta z dekódovaných hodnôt: podpíšte `https://shop.example/return` a až potom ju
   v query stringu URL-zakódujte, nie naopak.

## HTTP 400 „Missing parameters“ {#http-400-missing-parameters}

Chýba povinný parameter; stránka vypíše všetky chýbajúce vrátane parametra s podpisom. Parametre, ktoré musia byť
prítomné, no môžu byť prázdne, sa posielajú prázdne (`SS=`), nevynechávajú sa.

## Stránky s DES zlyhajú s „Unable to compute the DES signature“ {#des-pages-fail-with-unable-to-compute-the-des-signature}

OpenSSL 3 ponúka jednoduchý DES len cez svoj legacy provider. Použite Docker image, `examples/ci/serve.sh`, alebo
spustite server s `OPENSSL_CONF=docker/php/openssl-legacy.cnf`.

## Moja knižnica odmietne odpoveď simulátora {#my-library-rejects-the-simulators-answer}

- **Špecifický symbol v návrate**: TatraPay a VÚB e-platby podpisujú `SS` v návrate (`VS SS RES` pri TatraPay s AES
  a DES a pri e-platbách). Do `completePurchase()` odovzdajte ten istý `ss` ako do `purchase()`; omnipay-tatra 4.x
  overuje návrat s AES/DES nad `VS RES` a pri nastavenom `ss` ho odmietne, použite omnipay-tatra 5.
- **Iná suma alebo mena**: návrat s HMAC je podpísaný nad sumou a menou z požiadavky; overujte s tými istými
  hodnotami.
- **Zmenený variabilný symbol**: knižnice odmietnu návrat pre inú objednávku, než akú im odovzdáte.

## Nič neodpovedá alebo prehliadač sa nedostane na návratovú URL {#nothing-answers-or-the-browser-cannot-reach-the-return-url}

- `curl -I http://127.0.0.1:8080/` musí odpovedať 200; prečo nie, ukáže `log/php-server.log` (vstavaný server) alebo
  `docker compose logs` (Docker).
- Návratovú URL otvára prehliadač zákazníka, nie simulátor: musí byť dostupná z prehliadača, takže na lokálne
  testovanie funguje `http://localhost/...`.
- V Dockeri ostatné kontajnery dosiahnu simulátor ako `http://paymentsandbox.test`, prehliadač na vašom počítači však
  tento názov nepozná; pozrite [Spustenie](./running#next-to-your-application).

## Chyby na Linuxe s Dockerom {#errors-on-linux-with-docker}

PHP-FPM beží ako `www-data` a musí zapisovať do pripojených `temp/` a `log/`: `chmod -R a+w temp log`.
