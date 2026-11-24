---
title: Ako to funguje
description: Čo payment-simulator robí s požiadavkou - overenie podpisu, stránky Sign OK, Wrong sign a Missing parameters a tri podpísané odpovede, ktoré pošle späť do e-shopu.
---

# Ako to funguje

Online platby bánk sú protokoly založené na presmerovaní: e-shop pošle zákazníka do banky s podpísanou požiadavkou,
banka ho vráti späť s podpísaným výsledkom. Simulátor implementuje stranu banky v tomto protokole s verejnými
testovacími kľúčmi.

<figure class="ps-diagram">
  <img class="light-only" src="/diagrams/flow-sk-light.svg" alt="Sekvencia: e-shop v testovacom režime presmeruje zákazníka na simulátor s podpísanou požiadavkou; simulátor prepočíta podpis a zobrazí Sign OK, Wrong sign alebo Missing parameters; zákazník zvolí continue ok, fail alebo timeout a vráti sa na návratovú URL e-shopu s podpísaným výsledkom, ktorý e-shop overí metódou completePurchase()" width="760" height="454">
  <img class="dark-only" src="/diagrams/flow-sk-dark.svg" alt="Sekvencia: e-shop v testovacom režime presmeruje zákazníka na simulátor s podpísanou požiadavkou; simulátor prepočíta podpis a zobrazí Sign OK, Wrong sign alebo Missing parameters; zákazník zvolí continue ok, fail alebo timeout a vráti sa na návratovú URL e-shopu s podpísaným výsledkom, ktorý e-shop overí metódou completePurchase()" width="760" height="454">
</figure>

## 1. Požiadavka {#_1-the-request}

E-shop pošle parametre banky na URL simulátora pre danú bránu a typ kľúča, napríklad
`GET /payment/tatrapay-hmac` namiesto `https://moja.tatrabanka.sk/cgi-bin/e-commerce/start/tatrapay`. VÚB e-platby
používajú odoslanie formulára (`POST`), všetky ostatné brány s presmerovaním `GET`. Všetky URL nájdete na stránkach
[Banky](/sk/banks/).

## 2. Overenie {#_2-the-check}

Simulátor prečíta parametre, prepočíta podpis algoritmom a testovacím kľúčom danej brány a porovná ho s tým, ktorý ste
poslali, pomocou `hash_equals()`: porovnanie je presné, takže pri hexadecimálnom podpise záleží na veľkosti písmen.
Stránka zobrazí:

| Stránka | Kedy | HTTP status |
|---|---|---|
| **Sign OK** | podpisy sa zhodujú | 200 |
| **Wrong sign** | podpisy sa líšia | 200 |
| **Missing parameters** | chýba povinný parameter alebo samotný podpis; stránka ich vypíše všetky | 400 |

Každá stránka vypíše aj **Input Sign** (váš podpis), **Computed Sign** a všetky prijaté parametre; práve tie
porovnávate, keď podpis nesedí.

Samotné hodnoty simulátor nekontroluje: prijme ľubovoľný `MID`, sumu aj variabilný symbol. Neuchováva ani stav: každú
požiadavku vybaví samostatne a nič neukladá.

## 3. Tri odpovede {#_3-the-three-answers}

Pri správnom podpise stránka ponúkne tri odkazy späť na návratovú URL e-shopu (`RURL`, pri SporoPay `url`):

| Odkaz | Výsledok | Na otestovanie |
|---|---|---|
| `continue ok` | `RES=OK` (SporoPay `result=OK`) | zaplatenej objednávky |
| `continue fail` | `RES=FAIL` | zamietnutej alebo zrušenej platby |
| `continue timeout` | `RES=TOUT` | vypršania času na strane banky: TatraPay ho hlási ako platbu v spracovaní |

Každý odkaz nesie návratové parametre banky, podpísané tým istým testovacím kľúčom nad poľami, ktoré podpisuje banka.
Tam, kde banka vracia hodnoty, ktoré e-shop nemôže poznať (autorizačný kód, maskované číslo karty, ID karty), vracia
simulátor pevné hodnoty; uvádzajú ich stránky [Banky](/sk/banks/). Keď `RURL` už obsahuje query string, parametre sa
pripoja cez `&`.

Handler vašej návratovej URL overí odpoveď presne tak, ako by overil odpoveď banky. Potom URL ručne upravte
(`RES=FAIL` na `RES=OK`) a skontrolujte, že ju handler odmietne.

## Server-to-server endpointy {#server-to-server-endpoints}

Tri endpointy odpovedajú priamo serveru e-shopu namiesto toho, aby zobrazili stránku:

- `GET /payment/cardpay-cancel-purchase`: dokončenie predautorizácie, zrušenie predautorizácie a vrátenie platby
  pre CardPay; XML odpoveď podpísaná v hlavičke `Authorization`
  ([CardPay](/sk/banks/cardpay#capture-release-and-refund-get-payment-cardpay-cancel-purchase));
- `POST /cgi-bin/e-commerce/start/api/cardpay/transaction` a `GET .../transaction/<processingId>`: CardPay
  Direct API s JSON telami podpísanými v hlavičke `Authorization` ([CardPay Direct API](/sk/banks/cardpay-direct-api)).

## Čím simulátor nie je {#what-the-simulator-is-not}

- nie je banka: prijme akéhokoľvek obchodníka, kartu aj sumu a nikdy nepresúva peniaze;
- nie je špecifikácia: implementuje to, na čom sa zhoduje dokumentácia bánk a knižnice omnipay, a jeho testy ho
  overujú proti týmto knižniciam. Pred spustením do produkcie urobte jednu skutočnú platbu v testovacom alebo
  produkčnom prostredí banky;
- nie je pre skutočné kľúče: jeho kľúče sú verejné, pozrite [Bezpečnosť](/sk/security).
