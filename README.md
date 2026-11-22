# Payment Simulator

**Simulator of Slovak bank online payment gateways (TatraPay, CardPay, ComfortPay, VÚB ePlatby, SLSP SporoPay) for testing shop integrations**

Live instance: **https://platby.tomaj.sk/**

[![CI](https://github.com/tomaj/payment-simulator/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/tomaj/payment-simulator/actions/workflows/ci.yml?query=branch%3Amaster)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%20max-brightgreen.svg)](https://phpstan.org/)
[![OpenSSF Scorecard](https://api.securityscorecards.dev/projects/github.com/tomaj/payment-simulator/badge)](https://scorecard.dev/viewer/?uri=github.com/tomaj/payment-simulator)
[![License](https://img.shields.io/github/license/tomaj/payment-simulator.svg)](LICENSE.md)
[![PHP Version](https://img.shields.io/badge/php-%5E8.4-777bb4.svg)](composer.json)

## What is the Payment Simulator?

Slovak banks give merchants a payment page: the shop redirects the customer there with a signed request, and the bank sends the customer back to the shop with a signed result. Testing that round trip against the real banks needs merchant accounts and real money. The simulator accepts the same signed requests with publicly known test keys, shows whether the signature is correct, and offers links back to the shop for a successful, failed and timed-out payment, signed exactly like the bank signs them.

It is built for the [tomaj/omnipay-tatra](https://github.com/tomaj/omnipay-tatra), [tomaj/omnipay-vub](https://github.com/tomaj/omnipay-vub) and [tomaj/omnipay-slsp](https://github.com/tomaj/omnipay-slsp) gateways, whose test mode points at it, but works with any client that follows the bank specifications.

## Running it

### Docker

Requirements: Docker with Compose v2.

```bash
git clone https://github.com/tomaj/payment-simulator.git
cd payment-simulator
cp docker-compose.override.yml.example docker-compose.override.yml   # publishes nginx on port 8080
docker compose up -d --build
docker compose exec payment_simulator composer install
```

Open http://localhost:8080/. The image (`docker/php/Dockerfile`, `php:8.4-fpm`) has `soap`, OPcache and Xdebug 3 (trigger mode: start a session with `XDEBUG_TRIGGER`; set `client_host` in `XDEBUG_CONFIG` in the override file) and enables the OpenSSL legacy provider (`docker/php/openssl-legacy.cnf`) that the DES gateways need. Other containers on the Compose network reach the simulator as `http://paymentsandbox.test`.

### Local PHP

Requirements: PHP 8.4, 8.5 or 8.6 with `openssl`, Composer.

```bash
composer install
OPENSSL_CONF=docker/php/openssl-legacy.cnf php -S 127.0.0.1:8080 -t www www/index.php
```

`OPENSSL_CONF` is only needed for the DES gateways (single DES is in the OpenSSL 3 legacy provider). `log/` and `temp/` must be writable.

### Configuration

There is nothing to configure: the keys below are fixed. `app/config/local.neon` is loaded last and is the place for local overrides of the Nette configuration. Tracy shows errors when the request comes from localhost (debug mode) and logs them to `log/` otherwise.

## Pointing a shop at the simulator

The `tomaj/omnipay-*` gateways send their requests to the simulator in test mode. `testHost` defaults to `https://platby.tomaj.sk`; set it to your own instance:

```php
$gateway = Omnipay\Omnipay::create('TatraPay');
$gateway->initialize([
    'mid' => '9999',
    'sharedSecret' => str_repeat('1', 128), // the HMAC test key
    'testMode' => true,
    'testHost' => 'http://localhost:8080',
]);
```

Other clients send the bank's parameters to the URLs below instead of the bank's URL.

## Test keys

| Gateway / algorithm | Key |
|---|---|
| Tatra banka HMAC (TatraPay, CardPay, ComfortPay, CardPay authorize/cancel, Direct API) | `1` × 128 (hex, 64 bytes) |
| Tatra banka AES (TatraPay, CardPay, ComfortPay) | `1` × 64 (hex; AES-128 uses the first 16 bytes) |
| Tatra banka DES (TatraPay, CardPay) | `11111111` |
| VÚB e-platby HMAC | `1` × 64 (used as text) |
| SLSP SporoPay 3DES | `Z3qY08EpvLlAAoMZdnyUdQ==` (base64) |

## Endpoints

Every payment page shows the received and the computed signature and the parameters. With a correct signature it shows **Sign OK** and three links back to the shop: a successful (`RES=OK`), failed (`FAIL`) and timed-out (`TOUT`) payment, signed like the bank signs them. A wrong signature shows **Wrong sign**; a missing required parameter shows **Missing parameters** with the list (HTTP 400). Signatures are compared exactly (`hash_equals()`), so the case of a hex signature matters. Parameters in *italics* are optional.

| URL | Gateway | Parameters | Signature parameter: signed fields | Return URL |
|---|---|---|---|---|
| `GET /payment/tatrapay-hmac` | TatraPay, HMAC | `MID`, `AMT`, `CURR`, `VS`, *`SS`*, *`CS`*, `RURL`, *`REM`*, `TIMESTAMP` | `HMAC`: MID AMT CURR VS SS CS RURL REM TIMESTAMP | `VS`, *`SS`*, `RES`, `AMT`, `CURR`, `CS`, `TIMESTAMP`, `HMAC` over AMT CURR VS SS CS RES TIMESTAMP |
| `GET /payment/tatrapay-aes256` | TatraPay, AES | `MID`, `AMT`, `CURR`, `VS`, *`SS`*, *`CS`*, `RURL` | `SIGN`: MID AMT CURR VS SS CS RURL | `VS`, *`SS`*, `RES`, `SIGN` over VS RES |
| `GET /payment/tatrapay-des` | TatraPay, DES | as AES | as AES | as AES |
| `GET /payment/cardpay-hmac` | CardPay, HMAC | `MID`, `AMT`, `CURR`, `VS`, `RURL`, `IPC`, `NAME`, *`REM`*, `TIMESTAMP` | `HMAC`: MID AMT CURR VS RURL IPC NAME REM TIMESTAMP | `VS`, `TXN`, `RES`, `TRES`, `AC`, `AMT`, `CURR`, `CID`, `CC`, `TIMESTAMP`, `HMAC` over AMT CURR VS TXN RES AC TRES CID CC TID TIMESTAMP |
| `GET /payment/cardpay-aes256` | CardPay, AES | `MID`, `AMT`, `CURR`, `VS`, *`CS`*, `RURL`, `IPC`, `NAME` | `SIGN`: MID AMT CURR VS CS RURL IPC NAME | `VS`, `AC`, `RES`, `SIGN` over VS RES AC |
| `GET /payment/cardpay-des` | CardPay, DES | as AES | as AES | as AES |
| `GET /payment/cardpay-authorize-hmac` | CardPay authorization (pre-auth, card registration) | `MID`, `AMT`, `CURR`, `VS`, `RURL`, `IPC`, `NAME`, `TIMESTAMP`, *`E2E`, `TXN`, `REM`, `TPAY`, `CID`, `ECID`, `TDS_*`* | `HMAC`: MID AMT CURR VS E2E TXN RURL IPC NAME REM TPAY CID ECID TDS_CARDHOLDER TDS_EMAIL TDS_MOBILE_PHONE TDS_BILL_CITY TDS_BILL_COUNTRY TDS_BILL_ADDRESS1 TDS_BILL_ADDRESS2 TDS_BILL_ZIP TDS_SHIP_CITY TDS_SHIP_COUNTRY TDS_SHIP_ADDRESS1 TDS_SHIP_ADDRESS2 TDS_SHIP_ZIP TDS_ADDR_MATCH TIMESTAMP | `AMT`, `CURR`, `VS`, `TXN`, `RES`, `AC`, `TRES`, `CID`, `CC`, `RC`, `TID`, `TIMESTAMP`, `HMAC` over AMT CURR VS TXN RES AC TRES CID CC RC TID TIMESTAMP (with `TPAY=Y` an OK payment returns a card id) |
| `GET /payment/cardpay-cancel-purchase` | CardPay cancel (void) | `MID`, `AMT`, `TID`, *`VS`*, `TXN`, *`REM`*, `TIMESTAMP`, `HMAC` | `HMAC`: MID AMT TID VS TXN REM TIMESTAMP | XML `<res>OK</res>` with an `Authorization: HMAC=…` header over the body, or `<res>FAIL</res>` |
| `GET /payment/comfortpay-hmac` | ComfortPay, HMAC | `MID`, `AMT`, `CURR`, `VS`, `RURL`, *`REM`*, `TPAY`, `TIMESTAMP`, `IPC`, `NAME` | `HMAC`: MID AMT CURR VS RURL IPC NAME REM TPAY TIMESTAMP | `VS`, `AMT`, `CURR`, `RES`, `TRES`, `AC`, `CID`, `CC`, `RC`, `TIMESTAMP`, `HMAC` over AMT CURR VS RES AC TRES CID CC RC TID TIMESTAMP |
| `GET /payment/comfortpay-aes256` | ComfortPay, AES | `MID`, `AMT`, `CURR`, `VS`, *`CS`*, `RURL`, `TPAY`, `IPC`, `NAME` | `SIGN`: MID AMT CURR VS CS RURL IPC NAME TPAY | `VS`, `RES`, `TRES`, `AC`, `CID`, `SIGN` over VS RES AC CID |
| `POST /payment/eplatby-hmac` | VÚB e-platby (form post) | `MID`, `AMT`, `VS`, *`SS`*, *`CS`*, `RURL` | `SIGN`: MID AMT VS SS CS RURL (uppercase hex) | `VS`, *`SS`*, `RES`, `SIGN` over VS SS RES |
| `GET /payment/sporopay-des` | SLSP SporoPay | `pu_predcislo`, `pu_cislo`, `pu_kbanky`, `suma`, `mena`, `vs`, `ss`, `url`, `param` | `sign1`: the fields joined with `;` | `u_predcislo`, `u_cislo`, `u_kbanky`, the request fields, `result`, `real`, `SIGN2` over all of them joined with `;` |
| `POST /cgi-bin/e-commerce/start/api/cardpay/transaction` | CardPay Direct API (Apple Pay, Google Pay) | JSON body with `amount` (cents), `googlePayToken` or `applePayToken`, *`tdsTermUrl`* | `Authorization: HMAC=<HMAC of the body>` | JSON `status`: `OK`; `TDS_AUTH_REQUIRED` with a 3-D Secure form for Google Pay from 1000; `FAIL` from 10000. Response signed in the `Authorization` header |
| `GET /cgi-bin/e-commerce/start/api/cardpay/transaction/<processingId>` | CardPay Direct API, check | headers `X-Merchant-Id`, `X-Timestamp` | `Authorization: HMAC=<HMAC of "merchantId;timestamp;processingId">` | JSON `status: OK` |
| `POST /cgi-bin/e-commerce/start/api/form/cardpay/transaction/tds` | 3-D Secure page | form `tdsTermUrl`, … | — | a link back to `tdsTermUrl` |

HMAC values are lowercase hex unless noted; AES/DES values uppercase hex. The CardPay Direct API documentation from Tatra banka is in [`www/tatrabanka/`](www/tatrabanka/).

## Troubleshooting

- **"Wrong sign"**: compare the computed signature with yours and check the field order in the table above, the key, empty optional fields (they add nothing to the signed string) and the hex case. The specific symbol `SS` is signed between `VS` and `CS` for TatraPay and VÚB.
- **DES pages fail with "Unable to compute the DES signature"**: OpenSSL 3 without the legacy provider. Use the Docker image or set `OPENSSL_CONF=docker/php/openssl-legacy.cnf`.
- **HTTP 400 "Missing parameters"**: a required parameter is absent; the page lists which.

## Development

```bash
composer check                                       # code style, PHPStan, Rector, tests
OPENSSL_CONF=docker/php/openssl-legacy.cnf composer test   # include the DES tests
```

See [CONTRIBUTING.md](CONTRIBUTING.md).

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) and the [Code of Conduct](CODE_OF_CONDUCT.md).

## Security

The simulator uses fixed test keys and must never be used with real merchant keys. Do not report security vulnerabilities in public issues; see [SECURITY.md](SECURITY.md).

## License

The MIT License (MIT). See [LICENSE.md](LICENSE.md).
