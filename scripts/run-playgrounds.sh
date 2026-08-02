#!/usr/bin/env bash
set -euo pipefail
find playground -name index.php -print0 | sort -z | xargs -0 -n1 php >/dev/null
echo "PASS: all playgrounds"
