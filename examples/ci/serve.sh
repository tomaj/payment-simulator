#!/usr/bin/env bash
# Serves this checkout of payment-simulator with the PHP built-in server at SIMULATOR_URL (default
# http://127.0.0.1:8080) and waits until it answers. Installs the Composer dependencies first when vendor/ is missing.
# The server keeps running after the script; stop it with: kill "$(cat log/php-server.pid)"
# Other projects (the omnipay-* examples) start the simulator the same way from a clone of this repository.
set -euo pipefail
root="$(cd "$(dirname "$0")/../.." && pwd)"
url="${SIMULATOR_URL:-http://127.0.0.1:8080}"
if curl -sf -o /dev/null "$url/"; then
    echo "something already answers at $url" >&2
    exit 1
fi

[[ -f "$root/vendor/autoload.php" ]] || composer install --working-dir="$root" --no-dev --no-progress --no-interaction --quiet
mkdir -p "$root/temp" "$root/log"

# the DES endpoints need the OpenSSL 3 legacy provider
OPENSSL_CONF="$root/docker/php/openssl-legacy.cnf" nohup php -d display_errors=0 -d log_errors=1 -S "${url#http://}" -t "$root/www" "$root/www/index.php" \
    > "$root/log/php-server.log" 2>&1 &
echo $! > "$root/log/php-server.pid"
for _ in $(seq 1 50); do
    if curl -sf -o /dev/null "$url/"; then
        echo "payment-simulator is running at $url"
        exit 0
    fi
    sleep 0.2
done
echo "fail: payment-simulator did not start at $url" >&2
cat "$root/log/php-server.log" >&2
exit 1
