#!/usr/bin/env bash
set -euo pipefail
find examples -name after.php -print0 | sort -z | xargs -0 -n1 php >/dev/null
echo "PASS: all examples"
