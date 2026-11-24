---
title: Plain PHP client
description: A TatraPay request signed by hand against payment-simulator, its signed answers verified by hand, and the Wrong sign and Missing parameters pages.
---

# Plain PHP client

No library: the example builds a TatraPay HMAC request with `hash_hmac()`, opens the simulator's page and verifies
each signed answer itself. Use it as the reference when you write a client in another language.

Source: [`examples/plain-php/run.php`](https://github.com/tomaj/payment-simulator/blob/master/examples/plain-php/run.php) ·
run it with `examples/ci/example.sh plain-php`.

## The request

<<< @/../examples/plain-php/run.php#request

The key is 128 hexadecimal characters; HMAC uses its 64 decoded bytes. `SS` and `CS` are left out, so they add nothing
to the signed string. Field order: [TatraPay HMAC](/banks/tatrapay#hmac-get-payment-tatrapay-hmac).

## Verifying the answers

<<< @/../examples/plain-php/run.php#verify

The simulator sends no `TID`; an absent field counts as empty, exactly like an absent optional field in the request.
Compare with `hash_equals()`, and check that `VS` is the order you expect.

## Wrong signature and missing parameters

<<< @/../examples/plain-php/run.php#errors

## Output

<<< @/../examples/plain-php/expected-output.txt{text}

## What it tests

- the HMAC algorithm and the field order of the TatraPay request and return, independently of any library;
- **Wrong sign** for a wrong signature, without links back;
- **Missing parameters** with HTTP 400 and the list of the absent parameters.
