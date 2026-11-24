---
title: How it works
description: What payment-simulator does with a request - the signature check, the Sign OK, Wrong sign and Missing parameters pages, and the three signed answers it sends back to the shop.
---

# How it works

The banks' online payments are redirect protocols: the shop sends the customer to the bank with a signed request, the
bank sends the customer back with a signed result. The simulator implements the bank's side of that protocol with
public test keys.

<figure class="ps-diagram">
  <img class="light-only" src="/diagrams/flow-light.svg" alt="Sequence: the shop in test mode redirects the customer to the simulator with a signed request; the simulator recomputes the signature and shows Sign OK, Wrong sign or Missing parameters; the customer picks continue ok, fail or timeout and is sent back to the shop's return URL with a signed result, which the shop verifies with completePurchase()" width="760" height="454">
  <img class="dark-only" src="/diagrams/flow-dark.svg" alt="Sequence: the shop in test mode redirects the customer to the simulator with a signed request; the simulator recomputes the signature and shows Sign OK, Wrong sign or Missing parameters; the customer picks continue ok, fail or timeout and is sent back to the shop's return URL with a signed result, which the shop verifies with completePurchase()" width="760" height="454">
</figure>

## 1. The request

The shop sends the bank's parameters to the simulator's URL for the gateway and key type, for example
`GET /payment/tatrapay-hmac` instead of `https://moja.tatrabanka.sk/cgi-bin/e-commerce/start/tatrapay`. VÚB e-platby
is a form `POST`, every other redirect gateway a `GET`. The [Banks](/banks/) pages list every URL.

## 2. The check

The simulator reads the parameters, recomputes the signature with the gateway's algorithm and test key, and compares
it with the one you sent using `hash_equals()`: the comparison is exact, so the case of a hexadecimal signature
matters. The page shows:

| Page | When | HTTP status |
|---|---|---|
| **Sign OK** | the signatures are equal | 200 |
| **Wrong sign** | they differ | 200 |
| **Missing parameters** | a required parameter, or the signature itself, is absent; the page lists all of them | 400 |

Every page also prints the **Input Sign** (yours), the **Computed Sign** and all received parameters, which is what you
compare when a signature does not match.

The simulator does not check the values themselves: any `MID`, amount or variable symbol is accepted. It also does not
keep state: every request is answered on its own, and nothing is stored.

## 3. The three answers

With a correct signature the page offers three links back to the shop's return URL (`RURL`, for SporoPay `url`):

| Link | Result | Use it to test |
|---|---|---|
| `continue ok` | `RES=OK` (SporoPay `result=OK`) | a paid order |
| `continue fail` | `RES=FAIL` | a declined or cancelled payment |
| `continue timeout` | `RES=TOUT` | the bank's timeout: TatraPay reports it as pending |

Each link carries the bank's return parameters, signed with the same test key over the fields the bank signs. Where
the bank returns values the shop cannot know (approval code, masked card number, card id), the simulator returns fixed
ones; the [Banks](/banks/) pages list them. When the `RURL` already has a query string, the parameters are appended
with `&`.

Your return URL handler verifies the answer exactly as it would verify the bank's. Then edit the URL by hand
(`RES=FAIL` to `RES=OK`) and check that your handler rejects it.

## Server-to-server endpoints

Three endpoints answer the shop's server directly instead of showing a page:

- `GET /payment/cardpay-cancel-purchase`: CardPay capture, release and refund; an XML answer signed in the
  `Authorization` header ([CardPay](/banks/cardpay#capture-release-and-refund-get-payment-cardpay-cancel-purchase));
- `POST /cgi-bin/e-commerce/start/api/cardpay/transaction` and `GET .../transaction/<processingId>`: the CardPay
  Direct API with JSON bodies signed in the `Authorization` header ([CardPay Direct API](/banks/cardpay-direct-api)).

## What the simulator is not

- not a bank: it accepts any merchant, card and amount, and never moves money;
- not a specification: it implements what the banks' documentation and the omnipay libraries agree on, and is checked
  against those libraries in its tests. Before you go live, make one real payment with the bank's test or production
  environment;
- not for real keys: its keys are public, see [Security](/security).
