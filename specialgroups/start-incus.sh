#!/usr/bin/env bash
set -Eeuo pipefail

LOG=/var/log/incus/incusd.log

die() {
  echo "[start-incus] ERROR: $*" >&2
  echo "[start-incus] Últimas líneas del log de incusd:"
  tail -n 200 "$LOG" 2>/dev/null || true
  exit 1
}

# Vuelca diagnóstico si salimos por error en cualquier punto
trap 'status=$?; if [ $status -ne 0 ]; then
        echo "[start-incus] Script terminó con código $status"
        echo "[start-incus] Estado del socket y procesos:"
        ls -l /var/lib/incus 2>/dev/null || true
        ss -xl 2>/dev/null | grep -E "incus|lxd|unix.socket" || true
        pgrep -ax incusd 2>/dev/null || true
        tail -n 200 "$LOG" 2>/dev/null || true
      fi' EXIT

if [[ "$(id -u)" -ne 0 ]]; then
  echo "[start-incus] Debe ejecutarse como root." >&2
  exit 1
fi

# --- Normaliza entorno del cliente root para no ensuciar /var/www ---
export HOME=/root
export XDG_CONFIG_HOME=/root/.config
export XDG_RUNTIME_DIR=/run/user/0
export PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin
export INCUS_DIR=/var/lib/incus
umask 077
mkdir -p "$XDG_CONFIG_HOME" "$XDG_RUNTIME_DIR"

# Dirs básicos
mkdir -p /var/lib/incus /run/incus /var/log/incus

# Grupos y pertenencias
getent group incus-admin >/dev/null 2>&1 || groupadd -r incus-admin
getent group lxd         >/dev/null 2>&1 || groupadd -r lxd
usermod -aG incus-admin,lxd www-data || true

# --- Localiza binarios ---
INCUSD_BIN="$(command -v incusd || true)"
if [ -z "$INCUSD_BIN" ]; then
  for p in /usr/libexec/incus/incusd /usr/sbin/incusd /usr/bin/incusd; do
    [ -x "$p" ] && INCUSD_BIN="$p" && break
  done
fi
[ -n "$INCUSD_BIN" ] || die "No encuentro 'incusd' en el sistema."

INCUS_CLI="$(command -v incus || echo /usr/bin/incus)"
CURL="$(command -v curl || echo /usr/bin/curl)"

# --- Arranca incusd (si no está) con logs verbosos ---
if ! pgrep -x incusd >/dev/null 2>&1; then
  echo "[start-incus] Lanzando incusd ($INCUSD_BIN)..."
  # Nos aseguramos de que el log existe
  touch "$LOG" && chmod 600 "$LOG"
  "$INCUSD_BIN" --group incus-admin --verbose --debug >>"$LOG" 2>&1 &
fi

# --- Espera a que aparezca el socket ---
for i in $(seq 1 80); do
  if [ -S "$INCUS_DIR/unix.socket" ]; then
    echo "[start-incus] Socket OK: $INCUS_DIR/unix.socket"
    break
  fi
  if ! pgrep -x incusd >/dev/null 2>&1; then
    die "incusd murió durante la fase de socket."
  fi
  sleep 0.25
done
[ -S "$INCUS_DIR/unix.socket" ] || die "No apareció el socket $INCUS_DIR/unix.socket"

# --- Espera de readiness del API (rápida) ---
echo "[start-incus] Comprobando readiness del API…"
READY=0
for i in $(seq 1 160); do
  if "$INCUS_CLI" query -X GET /1.0 >/dev/null 2>&1; then
    READY=1; break
  fi
  if "$CURL" -sS --unix-socket "$INCUS_DIR/unix.socket" http://unix.socket/1.0 >/dev/null 2>&1; then
    READY=1; break
  fi
  if ! pgrep -x incusd >/dev/null 2>&1; then
    die "incusd murió durante la fase de readiness."
  fi
  sleep 0.25
done
[ "$READY" -eq 1 ] || die "Timeout esperando readiness del API."
echo "[start-incus] Daemon listo: API responde."

# --- Inicializa el servidor en primer arranque (idempotente) ---
if ! "$INCUS_CLI" info >/dev/null 2>&1; then
  echo "[start-incus] Inicializando Incus (almacenamiento dir, red incusbr0)…"
  "$INCUS_CLI" admin init --auto || true
  if "$INCUS_CLI" network show incusbr0 >/dev/null 2>&1; then
    "$INCUS_CLI" network rename incusbr0 lxdbr0 || true
  fi
fi

# --- Config de CLIENTE para www-data ---
user_home="$(getent passwd www-data | cut -d: -f6)"

# Si ~/.config lo creó root, elimínalo para evitar bloqueos
if [ -e "$user_home/.config" ] && [ "$(stat -c %U "$user_home/.config")" != "www-data" ]; then
  rm -rf "$user_home/.config"
fi

# Estructura y permisos mínimos
install -d -m 700 -o www-data -g www-data "$user_home"
install -d -m 700 -o www-data -g www-data "$user_home/.config"

# Genera config de cliente para www-data (INCUS_DIR explícito)
su -s /bin/bash - www-data -c '
  set -e
  umask 077
  env -i HOME="$HOME" XDG_CONFIG_HOME="$HOME/.config" INCUS_DIR="/var/lib/incus" \
    PATH="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin" \
    incus info >/dev/null 2>&1 || true
'

# Propietarios/permisos finales correctos
if [ -e "$user_home/.config/incus" ]; then
  chown -R www-data:www-data "$user_home/.config/incus" || true
  chmod 700 "$user_home/.config/incus" || true
  [ -f "$user_home/.config/incus/config.yml" ] && chmod 600 "$user_home/.config/incus/config.yml" || true
fi

# --- Handover a www-data ---
if [[ "$#" -gt 0 ]]; then
  exec su -s /bin/bash - www-data -c "$*"
elif [ -t 0 ]; then
  exec su - www-data
else
  exec su -s /bin/bash - www-data -c 'exec sleep infinity'
fi