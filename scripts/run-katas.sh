#!/usr/bin/env bash
set -euo pipefail
find kata -name solution.php -print0 | sort -z | xargs -0 -n1 php >/dev/null
echo "PASS: all kata solutions"
