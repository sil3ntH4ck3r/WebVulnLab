#!/usr/bin/env bash
set -euo pipefail
SRC="/opt/bof-lab/src/adminsvc.c"
BIN="/opt/bof-lab/bin/adminsvc"
mkdir -p /opt/bof-lab/bin

# Compilar 64-bit x86_64, non-PIE, NX stack off, sin canarios
gcc -O2 -m64 -g -fno-stack-protector -fno-pie -no-pie -z noexecstack -D_FORTIFY_SOURCE=0 -Wl,-z,relro -o "$BIN" "$SRC"

# SUID root
chown root:root "$BIN"
chmod 4755 "$BIN"

# PATH
ln -sf "$BIN" /usr/local/bin/adminsvc