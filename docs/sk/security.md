---
title: Bezpečnosť
description: payment-simulator používa verejné testovacie kľúče, nikdy nesmie vidieť skutočné kľúče obchodníka a produkčný e-shop mu nesmie dôverovať; ako nahlásiť zraniteľnosť.
---

# Bezpečnosť

## Kľúče sú verejné {#the-keys-are-public}

Simulátor podpisuje a overuje [pevnými, zverejnenými testovacími kľúčmi](/sk/guide/configuration#test-keys). Ktokoľvek
dokáže vytvoriť návratovú URL, akú by vytvoril simulátor. Preto:

- **nikdy nenastavujte e-shop so skutočnými kľúčmi obchodníka proti simulátoru** a nikdy mu neposielajte skutočné
  údaje platobných kariet;
- **nikdy nedovoľte produkčnému e-shopu prijať odpovede simulátora**: v produkcii nechajte `testMode` vypnutý, aby
  e-shop overoval návraty z banky vaším skutočným kľúčom, ktorý simulátor nepozná;
- verejnú inštanciu `https://platby.tomaj.sk` používajte iba s testovacími údajmi: zdieľajú ju všetci.

## Kde ho spúšťať {#where-to-run-it}

Simulátor je určený pre vývojové a testovacie prostredie. Docker image obsahuje Xdebug a zapína legacy provider
OpenSSL kvôli jednoduchému DES; nepoužívajte ho ako základ produkčného image. Požiadavky z localhostu bežia v debug
režime Tracy a zobrazujú podrobné chybové stránky: lokálnu inštanciu nevystavujte do internetu.

## Stránky vypisujú vaše parametre {#the-pages-echo-your-parameters}

Každá stránka vypíše prijaté parametre, escapované cez Latte, a odkazy späť na vašu návratovú URL. Nahláste
akýkoľvek spôsob, ako do nich vložiť značkovanie alebo skript.

## Nahlásenie zraniteľnosti {#reporting-a-vulnerability}

**Neotvárajte** verejné issue. Nahláste ju cez
[súkromné hlásenie zraniteľností na GitHube](https://github.com/tomaj/payment-simulator/security/advisories/new) alebo
e-mailom, ako popisuje [SECURITY.md](https://github.com/tomaj/payment-simulator/blob/master/SECURITY.md).
