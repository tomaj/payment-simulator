---
layout: home
title: payment-simulator – simulátor platobných brán slovenských bánk
titleTemplate: false
description: 'Lokálna náhrada platobných stránok Tatra banky (TatraPay, CardPay, ComfortPay, CardPay Direct API), VÚB e-platieb a SLSP SporoPay: overí podpis vašej požiadavky a odpovie výsledkom OK, FAIL alebo TOUT podpísaným tak, ako ho podpisuje banka.'

hero:
  name: payment-simulator
  text: Testujte platby bez banky
  tagline: 'Presmerujte e-shop namiesto Tatra banky, VÚB alebo SLSP na simulátor platobnej brány. Overí podpis vašej požiadavky verejnými testovacími kľúčmi a vráti zákazníka s úspešným, neúspešným alebo časovo vypršaným výsledkom, podpísaným presne tak, ako ho podpisuje banka.'
  image:
    light: /illustrations/hero-sk-light.svg
    dark: /illustrations/hero-sk-dark.svg
    alt: Podpísaná požiadavka otvorí platobnú stránku simulátora, ktorá zobrazí Sign OK a ponúkne tri podpísané odpovede späť do e-shopu
  actions:
    - theme: brand
      text: Začíname
      link: /sk/guide/getting-started
    - theme: alt
      text: Endpointy
      link: /sk/reference/endpoints
    - theme: alt
      text: GitHub
      link: https://github.com/tomaj/payment-simulator

features:
  - icon: '<svg class="ps-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="40" height="40" aria-hidden="true" focusable="false"><rect class="ps-soft" x="2" y="2" width="44" height="44" rx="10"/><path class="ps-ink" d="M9 19L24 10l15 9M12 21v14M20 21v14M28 21v14M36 21v14M8 38h32"/><circle class="ps-acc" cx="38" cy="12" r="6"/></svg>'
    title: Šesť platobných brán troch bánk
    details: TatraPay, CardPay (s predautorizáciou a jej dokončením), ComfortPay, CardPay Direct API pre Apple Pay a Google Pay, VÚB e-platby a SLSP SporoPay.
    link: /sk/banks/
  - icon: '<svg class="ps-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="40" height="40" aria-hidden="true" focusable="false"><rect class="ps-soft" x="3" y="6" width="30" height="36" rx="6"/><path class="ps-ink" d="M10 16h16M10 23h12M10 30h9"/><circle class="ps-acc" cx="35" cy="33" r="10"/><path class="ps-mark" d="M30.5 33.5l3.2 3 6-6.5"/></svg>'
    title: Overí váš podpis
    details: Každá stránka prepočíta podpis algoritmom banky a zobrazí obe hodnoty, takže zlé poradie polí alebo zlý kľúč odhalíte hneď.
    link: /sk/guide/how-it-works
  - icon: '<svg class="ps-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="40" height="40" aria-hidden="true" focusable="false"><rect class="ps-soft" x="3" y="8" width="42" height="12" rx="6"/><rect class="ps-acc" x="3" y="24" width="18" height="16" rx="5"/><path class="ps-ink" d="M27 28h14M27 35h10"/><path class="ps-mark" d="M8 32l3 3 6-6"/></svg>'
    title: OK, FAIL a TOUT
    details: Tri podpísané odkazy na vašu návratovú URL otestujú každú vetvu vášho kódu vrátane vypršania času na strane banky.
    link: /sk/guide/how-it-works#_3-the-three-answers
  - icon: '<svg class="ps-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="40" height="40" aria-hidden="true" focusable="false"><rect class="ps-acc" x="5" y="11" width="38" height="26" rx="5"/><path class="ps-ink" d="M5 18h38"/><path class="ps-mark" d="M11 30h10"/><path class="ps-ink" d="M31 29l3 3 6-7"/></svg>'
    title: Stavaný pre Omnipay
    details: Brány omnipay-tatra, omnipay-vub a omnipay-slsp sem v testovacom režime posielajú platby; funguje aj každý klient, ktorý dodržiava špecifikáciu banky.
    link: /sk/guide/omnipay
  - icon: '<svg class="ps-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="40" height="40" aria-hidden="true" focusable="false"><rect class="ps-soft" x="3" y="5" width="42" height="30" rx="6"/><path class="ps-ink" d="M10 14l6 5-6 5M20 25h9M17 41h14M24 35v6"/><circle class="ps-acc" cx="37" cy="15" r="4"/></svg>'
    title: Beží kdekoľvek
    details: Jeden príkaz so vstavaným PHP serverom, Docker Compose s úplným stackom nginx + PHP-FPM, alebo verejná inštancia na platby.tomaj.sk.
    link: /sk/guide/running
  - icon: '<svg class="ps-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="40" height="40" aria-hidden="true" focusable="false"><path class="ps-soft" d="M24 3l17 6v13c0 11-7 19-17 23C14 41 7 33 7 22V9z"/><circle class="ps-acc" cx="20" cy="22" r="6"/><path class="ps-ink" d="M25 24h12M33 24v5M37 24v4"/></svg>'
    title: Verejné testovacie kľúče
    details: Pevné, zverejnené kľúče pre HMAC, AES, DES, e-platby a SporoPay. Nič na nastavovanie, nič tajné, žiadne skutočné peniaze.
    link: /sk/guide/configuration
---

## Ako to vyzerá {#what-it-looks-like}

Klient bez akejkoľvek knižnice: ručne podpíše požiadavku TatraPay, otvorí stránku simulátora a overí jeho podpísané
odpovede. Toto je [príklad v čistom PHP](/sk/examples/plain-php), ktorý v CI beží proti simulátoru zostavenému
z toho istého commitu:

<<< @/../examples/plain-php/run.php#request

<<< @/../examples/plain-php/run.php#verify

Jeho výstup:

<<< @/../examples/plain-php/expected-output.txt{text}

## Kam ďalej {#where-to-go-next}

<div class="ps-cards">
  <a href="./guide/getting-started"><strong>Začíname</strong><span>Spustite simulátor a pošlite mu prvú podpísanú platbu.</span></a>
  <a href="./guide/how-it-works"><strong>Ako to funguje</strong><span>Stránka, verdikt a tri podpísané odpovede.</span></a>
  <a href="./banks/"><strong>Banky</strong><span>Každý endpoint s parametrami, podpisovanými poľami a návratom.</span></a>
  <a href="./guide/omnipay"><strong>Použitie s Omnipay</strong><span>Nasmerujte naň brány omnipay-tatra, omnipay-vub a omnipay-slsp.</span></a>
  <a href="./examples/"><strong>Príklady</strong><span>Šesť klientov, ktoré v CI bežia proti simulátoru.</span></a>
  <a href="./reference/endpoints"><strong>Endpointy</strong><span>Všetky URL na jednej stránke.</span></a>
</div>
