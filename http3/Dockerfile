FROM caddy:latest

# Copiamos el Caddyfile a la ubicación que Caddy usa por defecto
COPY Caddyfile /etc/caddy/Caddyfile

# Copiamos los directorios con los contenidos HTML
COPY src/html_http2 /var/www/html/html_http2
COPY src/html_http3 /var/www/html/html_http3

# Exponemos los puertos necesarios:
# - El 80 (opcional, si se quiere redirigir HTTP a HTTPS)
# - El 443 en TCP para HTTPS (HTTP/2 y HTTP/1.x)
# - El 443 en UDP para HTTP/3
EXPOSE 80
EXPOSE 443/tcp
EXPOSE 443/udp