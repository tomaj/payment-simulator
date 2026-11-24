---
title: FAQ
description: Frequently asked questions about payment-simulator - keys, Wrong sign, timeouts, notifications, the public instance and Docker.
---

# FAQ

## Which key do I use?

The test key of the algorithm your request uses: `1` × 128 for Tatra banka HMAC, `1` × 64 for AES, `11111111` for DES,
`1` × 64 for VÚB e-platby, `Z3qY08EpvLlAAoMZdnyUdQ==` for SporoPay. See [test keys](/guide/configuration#test-keys).

## Why does the page say "Wrong sign"?

Your signature differs from the one the simulator computed. Compare the two values on the page and check the key, the
field order and the case; [Troubleshooting](/guide/troubleshooting#wrong-sign) has the full list.

## Does the simulator check the amount, merchant id or variable symbol?

No. It accepts any values and only checks that the request is signed and complete.

## Does it store payments or send notifications?

No. Every request is answered on its own, nothing is stored, no e-mail (`REM`) or SMS is sent, and there is no
server-to-server notification: the result reaches the shop only through the return URL the customer opens.

## What does "continue timeout" do?

It sends `RES=TOUT` (SporoPay `result=TOUT`), signed like the other answers. TatraPay defines it as "the result is not
known yet"; omnipay-tatra reports it with `isPending()`. The other gateways treat it as not successful.

## Can I use it from another language than PHP?

Yes: it is plain HTTP. The [plain PHP client](/examples/plain-php) shows the protocol without any library, and the
[Banks](/banks/) pages list every field.

## The public instance or my own?

Your own for automated tests and anything private: it runs with [one command](/guide/getting-started#start-it). The
public instance at https://platby.tomaj.sk is the default `testHost` of the omnipay gateways and is handy for a quick
manual try.

## Can I send a specific symbol?

Yes, with every TatraPay and VÚB e-platby key. `SS` is signed in the request between `VS` and `CS` and comes back in the
return URL. The TatraPay AES and DES return is signed over `VS SS RES` as in Tatra banka's TatraPay manual, the HMAC
return over `AMT CURR VS SS CS RES TID TIMESTAMP`; without `SS` it adds nothing. omnipay-tatra 5 verifies the same
strings ([TatraPay](/banks/tatrapay)).

## How do I reach the simulator from another container?

Add its services to your Compose project; the `nginx` service has the network alias `paymentsandbox.test`. See
[Running it](/guide/running#next-to-your-application).
