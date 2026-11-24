---
title: Configuration and test keys
description: The fixed public test keys of every simulated gateway, the Nette configuration files, debug mode and logs of payment-simulator.
---

# Configuration and test keys

There is nothing to configure: the keys are fixed and public, and the simulator accepts any merchant id, amount and
variable symbol.

## Test keys

Sign your requests with these keys. They are constants of `App\Presenters\PaymentPresenter`.

| Gateway / algorithm | Key | Constant |
|---|---|---|
| Tatra banka HMAC (TatraPay, CardPay, ComfortPay, authorize, capture, Direct API) | `1` × 128 (hexadecimal, 64 bytes) | `HMAC_KEY` |
| Tatra banka AES (TatraPay, CardPay, ComfortPay) | `1` × 64 (hexadecimal; AES-128 uses the first 16 bytes) | `AES_KEY` |
| Tatra banka DES (TatraPay, CardPay) | `11111111` | `DES_KEY` |
| VÚB e-platby HMAC | `1` × 64 (used as text) | `EPLATBY_KEY` |
| SLSP SporoPay 3DES | `Z3qY08EpvLlAAoMZdnyUdQ==` (base64) | `SPOROPAY_KEY` |

In PHP: `str_repeat('1', 128)`, `str_repeat('1', 64)`, `'11111111'`.

The algorithms are the banks': HMAC-SHA256 with the hex-decoded key, lowercase hex, for Tatra banka; AES-ECB over the
first 16 bytes of SHA-1, uppercase hex, for the 64-character key; DES-ECB over SHA-1, uppercase hex, for the 8-character
key; HMAC-SHA256 with the key as text, uppercase hex, for VÚB; 3DES for SporoPay. The AES and DES signers are copies of
the ones in omnipay-tatra 4.x (`App\Model\Sign`), the HMAC, e-platby and SporoPay signers come from the omnipay
libraries (`App\Model\Sign\LibrarySign`).

## Nette configuration

| File | Contains |
|---|---|
| `app/config/common.neon` | application mapping, error presenter, session |
| `app/config/services.neon` | the router |
| `app/config/local.neon` | loaded last, empty: your local overrides |

The time zone is `Europe/Prague`; `temp/` holds the Latte and DI cache, `log/` the logs.

## Debug mode and logs

Tracy decides by the client address: requests from localhost run in debug mode and show the Tracy bar and error
pages; other requests get the production error page and the error is logged to `log/`. With the PHP built-in server
the server's own log is `log/php-server.log`.

## Ports and URL

The PHP built-in server listens where `SIMULATOR_URL` says (default `http://127.0.0.1:8080`). With Docker, the port is
in `docker-compose.override.yml`; inside the Compose network the simulator is `http://paymentsandbox.test`. The
simulator builds its links from your `RURL`, never from its own host, so it works behind any host name; only the
CardPay Direct API's 3-D Secure form posts to the host the request came to.
