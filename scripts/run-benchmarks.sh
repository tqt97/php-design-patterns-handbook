#!/usr/bin/env bash
set -euo pipefail
find benchmarks -name benchmark.php -print0 | sort -z | xargs -0 -n1 php
