#!/usr/bin/env bash
# Runs one example against this checkout of payment-simulator: examples/ci/example.sh omnipay-tatra
# Serves the checkout (examples/ci/serve.sh) unless SIMULATOR_URL already answers, installs the example's own
# dependencies (the clients) and runs its e2e.sh, which compares the output with expected-output.txt.
set -euo pipefail
examples="$(cd "$(dirname "$0")/.." && pwd)"
name="${1:?usage: $0 <example>}"
[[ -x "$examples/$name/e2e.sh" ]] || { echo "fail: $examples/$name/e2e.sh not found" >&2; exit 1; }

url="${SIMULATOR_URL:-http://127.0.0.1:8080}"
if ! curl -sf -o /dev/null "$url/"; then
    "$examples/ci/serve.sh"
    trap 'kill "$(cat "$examples/../log/php-server.pid")" 2>/dev/null || true' EXIT
fi
composer install --working-dir="$examples/$name" --no-progress --no-interaction
"$examples/$name/e2e.sh"
