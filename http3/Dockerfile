# Usar la imagen oficial de Caddy
FROM caddy:latest

# Copiar los archivos del sitio web
COPY src/h2 /usr/share/caddy/sitio-http2
COPY src/h3 /usr/share/caddy/sitio-http3

# Copiar el Caddyfile
COPY Caddyfile /etc/caddy/Caddyfile

COPY certs /usr/share/caddy/certs

# Exponer los puertos necesarios
EXPOSE 80 443/udp 443/tcp