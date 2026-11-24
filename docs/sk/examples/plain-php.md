---
title: Klient v čistom PHP
description: Požiadavka TatraPay podpísaná ručne proti simulátoru payment-simulator, ručné overenie jeho podpísaných odpovedí a stránky Wrong sign a Missing parameters.
---

# Klient v čistom PHP

Bez knižnice: príklad zostaví HMAC požiadavku TatraPay cez `hash_hmac()`, otvorí stránku simulátora a každú podpísanú
odpoveď overí sám. Použite ho ako referenciu, keď píšete klienta v inom jazyku.

Zdrojový kód: [`examples/plain-php/run.php`](https://github.com/tomaj/payment-simulator/blob/master/examples/plain-php/run.php) ·
spustíte ho príkazom `examples/ci/example.sh plain-php`.

## Požiadavka {#the-request}

<<< @/../examples/plain-php/run.php#request

Kľúč má 128 hexadecimálnych znakov; HMAC používa jeho 64 dekódovaných bajtov. `SS` a `CS` sú vynechané, takže do
podpisovaného reťazca nič nepridávajú. Poradie polí: [TatraPay HMAC](/sk/banks/tatrapay#hmac-get-payment-tatrapay-hmac).

## Overenie odpovedí {#verifying-the-answers}

<<< @/../examples/plain-php/run.php#verify

Simulátor neposiela `TID`; chýbajúce pole sa počíta ako prázdne, presne ako chýbajúce nepovinné pole v požiadavke.
Porovnávajte cez `hash_equals()` a skontrolujte, či `VS` patrí objednávke, ktorú očakávate.

## Nesprávny podpis a chýbajúce parametre {#wrong-signature-and-missing-parameters}

<<< @/../examples/plain-php/run.php#errors

## Výstup {#output}

<<< @/../examples/plain-php/expected-output.txt{text}

## Čo testuje {#what-it-tests}

- algoritmus HMAC a poradie polí v požiadavke TatraPay aj v návrate, nezávisle od akejkoľvek knižnice;
- **Wrong sign** pri nesprávnom podpise, bez odkazov späť;
- **Missing parameters** s HTTP 400 a zoznamom chýbajúcich parametrov.
