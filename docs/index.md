---
layout: home
title: payment-simulator - test Slovak bank payment gateways
titleTemplate: false
description: 'A local stand-in for the payment pages of Tatra banka (TatraPay, CardPay, ComfortPay, CardPay Direct API), VÚB e-platby and SLSP SporoPay: it checks your signed request and answers with OK, FAIL and TOUT returns signed like the bank signs them.'

hero:
  name: payment-simulator
  text: Test bank payments without the bank
  tagline: 'Point your shop at the simulator instead of Tatra banka, VÚB or SLSP. It checks the signature of your request with public test keys and sends the customer back with a successful, failed or timed-out result, signed exactly like the bank signs it.'
  image:
    light: /illustrations/hero-light.svg
    dark: /illustrations/hero-dark.svg
    alt: A signed request opens the simulator's payment page, which shows Sign OK and offers three signed answers back to the shop
  actions:
    - theme: brand
      text: Get started
      link: /guide/getting-started
    - theme: alt
      text: Endpoints
      link: /reference/endpoints
    - theme: alt
      text: GitHub
      link: https://github.com/tomaj/payment-simulator

features:
  - icon: '<svg class="ps-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="40" height="40" aria-hidden="true" focusable="false"><rect class="ps-soft" x="2" y="2" width="44" height="44" rx="10"/><path class="ps-ink" d="M9 19L24 10l15 9M12 21v14M20 21v14M28 21v14M36 21v14M8 38h32"/><circle class="ps-acc" cx="38" cy="12" r="6"/></svg>'
    title: Six gateways of three banks
    details: TatraPay, CardPay (with pre-authorization and capture), ComfortPay, the CardPay Direct API for Apple Pay and Google Pay, VÚB e-platby and SLSP SporoPay.
    link: /banks/
  - icon: '<svg class="ps-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="40" height="40" aria-hidden="true" focusable="false"><rect class="ps-soft" x="3" y="6" width="30" height="36" rx="6"/><path class="ps-ink" d="M10 16h16M10 23h12M10 30h9"/><circle class="ps-acc" cx="35" cy="33" r="10"/><path class="ps-mark" d="M30.5 33.5l3.2 3 6-6.5"/></svg>'
    title: Checks your signature
    details: Every page recomputes the signature with the bank's algorithm and shows both values, so a wrong field order or key shows up at once.
    link: /guide/how-it-works
  - icon: '<svg class="ps-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="40" height="40" aria-hidden="true" focusable="false"><rect class="ps-soft" x="3" y="8" width="42" height="12" rx="6"/><rect class="ps-acc" x="3" y="24" width="18" height="16" rx="5"/><path class="ps-ink" d="M27 28h14M27 35h10"/><path class="ps-mark" d="M8 32l3 3 6-6"/></svg>'
    title: OK, FAIL and TOUT
    details: Three signed links back to your return URL test every branch of your code, including the bank's timeout.
    link: /guide/how-it-works#_3-the-three-answers
  - icon: '<svg class="ps-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="40" height="40" aria-hidden="true" focusable="false"><rect class="ps-acc" x="5" y="11" width="38" height="26" rx="5"/><path class="ps-ink" d="M5 18h38"/><path class="ps-mark" d="M11 30h10"/><path class="ps-ink" d="M31 29l3 3 6-7"/></svg>'
    title: Built for Omnipay
    details: The omnipay-tatra, omnipay-vub and omnipay-slsp gateways send test-mode payments here; any client that follows the bank's specification works too.
    link: /guide/omnipay
  - icon: '<svg class="ps-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="40" height="40" aria-hidden="true" focusable="false"><rect class="ps-soft" x="3" y="5" width="42" height="30" rx="6"/><path class="ps-ink" d="M10 14l6 5-6 5M20 25h9M17 41h14M24 35v6"/><circle class="ps-acc" cx="37" cy="15" r="4"/></svg>'
    title: Runs anywhere
    details: One command with the PHP built-in server, Docker Compose for a full nginx + PHP-FPM stack, or the public instance at platby.tomaj.sk.
    link: /guide/running
  - icon: '<svg class="ps-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="40" height="40" aria-hidden="true" focusable="false"><path class="ps-soft" d="M24 3l17 6v13c0 11-7 19-17 23C14 41 7 33 7 22V9z"/><circle class="ps-acc" cx="20" cy="22" r="6"/><path class="ps-ink" d="M25 24h12M33 24v5M37 24v4"/></svg>'
    title: Public test keys
    details: Fixed, published keys for HMAC, AES, DES, e-platby and SporoPay. Nothing to configure, nothing secret, no real money.
    link: /guide/configuration
---

## What it looks like

A client without any library: sign a TatraPay request by hand, open the simulator's page and verify its signed
answers. This is the [plain PHP example](/examples/plain-php), which runs in CI against the simulator built from the
same commit:

<<< @/../examples/plain-php/run.php#request

<<< @/../examples/plain-php/run.php#verify

Its output:

<<< @/../examples/plain-php/expected-output.txt{text}

## Where to go next

<div class="ps-cards">
  <a href="./guide/getting-started"><strong>Getting started</strong><span>Start the simulator and send it your first signed payment.</span></a>
  <a href="./guide/how-it-works"><strong>How it works</strong><span>The page, the verdict and the three signed answers.</span></a>
  <a href="./banks/"><strong>Banks</strong><span>Every endpoint with its parameters, signed fields and return.</span></a>
  <a href="./guide/omnipay"><strong>Using it with Omnipay</strong><span>Point the omnipay-tatra, omnipay-vub and omnipay-slsp gateways at it.</span></a>
  <a href="./examples/"><strong>Examples</strong><span>Six clients that run against the simulator in CI.</span></a>
  <a href="./reference/endpoints"><strong>Endpoints</strong><span>All URLs on one page.</span></a>
</div>
