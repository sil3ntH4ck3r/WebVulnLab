#!/bin/bash
set -euo pipefail

if [ "$#" -gt 0 ]; then
  sudo cron
  exec "$@"
fi

if [ -t 0 ]; then
  sudo cron
  exec /bin/bash
fi

exec sudo cron -f -L 15