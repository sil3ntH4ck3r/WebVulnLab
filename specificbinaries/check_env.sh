#!/usr/bin/env bash
set -euo pipefail
echo "=== Comprobaciones del entorno ==="
printf "ASLR (randomize_va_space): "
cat /proc/sys/kernel/randomize_va_space || true
echo
echo "GNU_STACK (NX):"
readelf -l /opt/bof-lab/bin/adminsvc | awk '/GNU_STACK/ {print}'
echo
echo "Arquitectura del binario:"
file /opt/bof-lab/bin/adminsvc || true
echo
echo "SUID y propietario:"
ls -l /opt/bof-lab/bin/adminsvc
