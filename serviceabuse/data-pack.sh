#!/bin/sh
set -eu
exec /usr/bin/tar czf /var/app/backups/data-$(date +%s).tgz /var/app/data