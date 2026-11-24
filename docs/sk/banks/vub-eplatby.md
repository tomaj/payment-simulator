---
title: VÚB e-platby
description: Endpoint e-platieb VÚB v payment-simulator - formulár POST podpísaný cez HMAC-SHA256, polia, ktoré podpisuje, a podpísaná odpoveď.
---

# VÚB e-platby

Zákazník platí z internet bankingu VÚB. Na rozdiel od brán Tatra banky e-shop banke **odošle formulár** (POST).

## `POST /payment/eplatby-hmac` {#post-payment-eplatby-hmac}

Kľúč: `1` × 64, použitý ako text ([testovacie kľúče](/sk/guide/configuration#test-keys)).

| Pole | Povinné | Význam |
|---|---|---|
| `MID` | áno | identifikátor obchodníka |
| `AMT` | áno | suma |
| `VS` | áno | variabilný symbol |
| *`SS`* | nie | špecifický symbol |
| *`CS`* | nie | konštantný symbol |
| `RURL` | áno | návratová URL adresa |
| `SIGN` | áno | podpis |

Polia sa čítajú z tela POST požiadavky; parametre v query stringu sa ignorujú.

**Podpis požiadavky:** `MID AMT VS SS CS RURL`, HMAC-SHA256 s kľúčom ako textom, **veľké** hexadecimálne znaky.

**Odpoveď:** `RURL?VS=…[&SS=…]&RES=OK|FAIL|TOUT&SIGN=…`, podpísaná nad `VS SS RES`.

`purchase()` z omnipay-vub odpovedá formulárom (`getRedirectMethod()` je `POST`); `redirect()` ho vykreslí tak, aby
ho prehliadač odoslal. omnipay-vub 4 overuje odpoveď nad `VS SS RES` a kontroluje variabilný symbol.

Príklad: [VÚB e-platby](/sk/examples/vub-eplatby).
