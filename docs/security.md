---
title: Security
description: payment-simulator uses public test keys and must never see real merchant keys or be trusted by a production shop; how to report a vulnerability.
---

# Security

## The keys are public

The simulator signs and verifies with [fixed, published test keys](/guide/configuration#test-keys). Anyone can produce
a return URL that the simulator would produce. Therefore:

- **never configure a shop with real merchant keys against the simulator**, and never send it real card data;
- **never let a production shop accept the simulator's answers**: keep `testMode` off in production, so that the shop
  verifies the bank's returns with your real key, which the simulator does not know;
- use the public instance `https://platby.tomaj.sk` only for test data: everybody shares it.

## Where to run it

The simulator is for development and test environments. The Docker image contains Xdebug and enables the OpenSSL
legacy provider for single DES; do not use it as the base of a production image. Requests from localhost run in Tracy's
debug mode and show detailed error pages: do not expose a local instance to the internet.

## The pages echo your parameters

Every page prints the received parameters, escaped by Latte, and the links back to your return URL. Report any way to
inject markup or script.

## Reporting a vulnerability

Do **not** open a public issue. Report it through
[GitHub private vulnerability reporting](https://github.com/tomaj/payment-simulator/security/advisories/new) or by
e-mail, as described in [SECURITY.md](https://github.com/tomaj/payment-simulator/blob/master/SECURITY.md).
