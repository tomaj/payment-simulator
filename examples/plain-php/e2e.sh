#!/usr/bin/env bash
# Runs run.php against the simulator at SIMULATOR_URL (default http://127.0.0.1:8080, see ../ci/serve.sh) and
# compares its output with expected-output.txt.
set -euo pipefail
cd "$(dirname "$0")"
diff -u expected-output.txt <(php run.php)
echo "ok: plain-php"
