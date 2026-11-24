---
title: Banks
description: The payment gateways payment-simulator simulates - Tatra banka TatraPay, CardPay, ComfortPay and the CardPay Direct API, VÚB e-platby and SLSP SporoPay - and their endpoints.
---

# Banks

| Bank | Gateway | What the customer does | Endpoints |
|---|---|---|---|
| Tatra banka | [TatraPay](./tatrapay) | pays from internet banking | HMAC, AES, DES |
| Tatra banka | [CardPay](./cardpay) | pays by card; pre-authorization, capture, release, refund | HMAC, AES, DES, authorize, capture |
| Tatra banka | [ComfortPay](./comfortpay) | pays by card and registers it for later charges | HMAC, AES |
| Tatra banka | [CardPay Direct API](./cardpay-direct-api) | pays with Apple Pay or Google Pay inside your page | JSON API, 3-D Secure page |
| VÚB | [e-platby](./vub-eplatby) | pays from VÚB internet banking | HMAC (form POST) |
| SLSP | [SporoPay](./slsp-sporopay) | pays from Slovenská sporiteľňa internet banking | 3DES |

Every redirect endpoint works the same way, see [How it works](/guide/how-it-works): it checks the signature, shows
**Sign OK**, **Wrong sign** or **Missing parameters**, and with a correct signature offers `continue ok`,
`continue fail` and `continue timeout` links back to the return URL, signed like the bank signs them.

On the bank pages, parameters in *italics* are optional: they may be absent, and an absent or empty optional field adds
nothing to the signed string. The signed fields are concatenated in the order given, without separators unless noted.
All endpoints on one page: [Endpoints](/reference/endpoints).
