---
title: Časté otázky
description: Časté otázky o simulátore payment-simulator - kľúče, Wrong sign, timeouty, notifikácie, verejná inštancia a Docker.
---

# Časté otázky

## Ktorý kľúč mám použiť? {#which-key-do-i-use}

Testovací kľúč algoritmu, ktorý vaša požiadavka používa: `1` × 128 pre HMAC Tatra banky, `1` × 64 pre AES, `11111111`
pre DES, `1` × 64 pre VÚB e-platby, `Z3qY08EpvLlAAoMZdnyUdQ==` pre SporoPay. Pozrite
[testovacie kľúče](/sk/guide/configuration#test-keys).

## Prečo stránka hlási „Wrong sign“? {#why-does-the-page-say-wrong-sign}

Váš podpis sa líši od toho, ktorý vypočítal simulátor. Porovnajte obe hodnoty na stránke a skontrolujte kľúč, poradie
polí a veľkosť písmen; úplný zoznam je v [Riešení problémov](/sk/guide/troubleshooting#wrong-sign).

## Kontroluje simulátor sumu, identifikátor obchodníka alebo variabilný symbol? {#does-the-simulator-check-the-amount-merchant-id-or-variable-symbol}

Nie. Prijme akékoľvek hodnoty a kontroluje iba to, že požiadavka je podpísaná a úplná.

## Ukladá platby alebo posiela notifikácie? {#does-it-store-payments-or-send-notifications}

Nie. Každá požiadavka sa vybaví samostatne, nič sa neukladá, neposiela sa e-mail (`REM`) ani SMS a neexistuje ani
server-to-server notifikácia: výsledok sa k e-shopu dostane iba cez návratovú URL, ktorú otvorí zákazník.

## Čo robí „continue timeout“? {#what-does-continue-timeout-do}

Pošle `RES=TOUT` (pri SporoPay `result=TOUT`), podpísaný rovnako ako ostatné odpovede. TatraPay ho definuje ako
„výsledok zatiaľ nie je známy“; omnipay-tatra ho hlási cez `isPending()`. Ostatné brány ho berú ako neúspešnú platbu.

## Dá sa použiť z iného jazyka než PHP? {#can-i-use-it-from-another-language-than-php}

Áno: je to obyčajné HTTP. [Klient v čistom PHP](/sk/examples/plain-php) ukazuje protokol bez akejkoľvek knižnice
a stránky [Banky](/sk/banks/) uvádzajú každé pole.

## Verejná inštancia, alebo vlastná? {#the-public-instance-or-my-own}

Vlastnú na automatické testy a na všetko neverejné: spustíte ju [jedným príkazom](/sk/guide/getting-started#start-it).
Verejná inštancia na https://platby.tomaj.sk je predvolený `testHost` brán Omnipay a hodí sa na rýchle ručné
vyskúšanie.

## Môžem poslať špecifický symbol? {#can-i-send-a-specific-symbol}

Áno, s každým kľúčom TatraPay a VÚB e-platby. `SS` sa v požiadavke podpisuje medzi `VS` a `CS` a vráti sa
v návratovej URL. Návrat TatraPay pri AES a DES je podpísaný cez `VS SS RES` podľa technickej príručky TatraPay
od Tatra banky, návrat pri HMAC cez `AMT CURR VS SS CS RES TID TIMESTAMP`; bez `SS` nič nepridáva. omnipay-tatra 5
overuje rovnaké reťazce ([TatraPay](/sk/banks/tatrapay)).

## Ako sa k simulátoru dostanem z iného kontajnera? {#how-do-i-reach-the-simulator-from-another-container}

Pridajte jeho služby do svojho projektu v Compose; služba `nginx` má sieťový alias `paymentsandbox.test`. Pozrite
[Spustenie](/sk/guide/running#next-to-your-application).
