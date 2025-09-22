#!/usr/bin/env bash
set -euo pipefail

# Asegura el runtime dir de sshd (por si no existiera en tiempo de ejecución)
if [ ! -d /run/sshd ]; then
  sudo /bin/mkdir -p /run/sshd
fi

# Lanza sshd en primer plano, con logs a stderr del contenedor
exec sudo /usr/sbin/sshd -D -e