---
title: Running it
description: Run payment-simulator with the PHP built-in server, with Docker Compose (nginx + PHP-FPM), next to your application on the Compose network, in CI, or use the public instance.
---

# Running it

| Where | Command | URL |
|---|---|---|
| PHP built-in server | `examples/ci/serve.sh` | `http://127.0.0.1:8080` |
| Docker Compose | `docker compose up -d --build` | `http://localhost:8080` (with the override file) |
| next to your app in Compose | the services in your Compose file | `http://paymentsandbox.test` |
| public instance | nothing | `https://platby.tomaj.sk` |

## Local PHP

Requirements: PHP 8.4, 8.5 or 8.6 with `openssl`, and Composer.

```bash
git clone https://github.com/tomaj/payment-simulator.git
cd payment-simulator
examples/ci/serve.sh
```

The script runs `composer install --no-dev` when `vendor/` is missing, creates `temp/` and `log/`, starts

```bash
OPENSSL_CONF=docker/php/openssl-legacy.cnf php -S 127.0.0.1:8080 -t www www/index.php
```

in the background and waits until the simulator answers. Its log goes to `log/php-server.log`, its process id to
`log/php-server.pid`. Set `SIMULATOR_URL=http://127.0.0.1:9000` for another port. You can run the `php -S` command
yourself just as well.

`OPENSSL_CONF` loads the OpenSSL 3 legacy provider, which has single DES. Only the TatraPay and CardPay DES endpoints
need it; without it they fail with "Unable to compute the DES signature". `temp/` (Latte and DI cache) and `log/` must
be writable.

## Docker

Requirements: Docker with Compose v2.

```bash
git clone https://github.com/tomaj/payment-simulator.git
cd payment-simulator
cp docker-compose.override.yml.example docker-compose.override.yml   # publishes nginx on port 8080
docker compose up -d --build
docker compose exec payment_simulator composer install
```

Open `http://localhost:8080/`. The stack:

| Service | Image | Does |
|---|---|---|
| `nginx` | `nginx:stable` | serves `www/`, passes PHP to `payment_simulator:9000`; network alias `paymentsandbox.test` |
| `payment_simulator` | built from `docker/php/Dockerfile` (`php:8.4-fpm`) | PHP-FPM with `soap`, OPcache, Xdebug 3 and the OpenSSL legacy provider |

The repository is mounted into both containers, so a change in the code shows up without a rebuild. On Linux,
PHP-FPM runs as `www-data` and must be able to write the mounted `temp/` and `log/`: `chmod -R a+w temp log`.

The override file publishes the port and configures Xdebug: it runs in trigger mode (start a session with
`XDEBUG_TRIGGER`), on port 9003, and connects to `client_host` from `XDEBUG_CONFIG` (the Docker bridge
`172.17.0.1` in the example). `PHP_IDE_CONFIG` names the server for path mappings.

## Next to your application

Add the two services to your application's Compose project (or put both on one network). Your application reaches the
simulator as `http://paymentsandbox.test`, the network alias of the `nginx` service:

```php
$gateway->setTestHost('http://paymentsandbox.test');
```

The customer's browser is redirected to the same host, so publish the port too and, if the browser runs outside
Docker, add `127.0.0.1 paymentsandbox.test` to your hosts file or use `http://localhost:8080` as the test host.

## In CI

The [Examples workflow](https://github.com/tomaj/payment-simulator/blob/master/.github/workflows/examples.yml) of this
repository serves the checkout with `examples/ci/serve.sh` and runs clients against it. In another repository, clone
it first:

```yaml
- name: Start payment-simulator
  run: |
    git clone --depth 1 https://github.com/tomaj/payment-simulator.git "$RUNNER_TEMP/payment-simulator"
    "$RUNNER_TEMP/payment-simulator/examples/ci/serve.sh"
```

[omnipay-tatra's examples](https://tomaj.github.io/omnipay-tatra/examples/) run against the simulator this way.

## The public instance

https://platby.tomaj.sk runs the current version and is the default `testHost` of the omnipay-* gateways. It needs
no setup, but your return URL must be reachable from your browser (it is: the simulator only links to it), and you
share the instance with everybody else. For automated tests run your own.
