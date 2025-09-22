# syntax=docker/dockerfile:1
FROM ubuntu:24.04

ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update && apt-get install -y --no-install-recommends \
    incus incus-client iproute2 iptables nftables dnsmasq-base uidmap \
    ca-certificates dbus gcc make sudo tini vim curl bash-completion \
    build-essential wget procps apparmor \
    && rm -rf /var/lib/apt/lists/*

# Crear grupo 'lxd' (cosmético para el reto) y el usuario www-data
RUN groupadd -r lxd || true && \
    groupadd -r incus-admin || true && \
    usermod -s /bin/bash -aG lxd,incus-admin www-data || \
    (useradd -m -s /bin/bash -G lxd,incus-admin www-data)

# Alias de 'lxc' -> 'incus' para que los comandos clásicos funcionen
RUN ln -sf /usr/bin/incus /usr/local/bin/lxc

# Copiar wrapper setuid y script de arranque
COPY entrypoint.c /tmp/entrypoint.c
RUN gcc -O2 -o /sbin/entrypoint /tmp/entrypoint.c && \
    chown root:root /sbin/entrypoint && chmod 4755 /sbin/entrypoint && \
    rm -f /tmp/entrypoint.c

COPY start-incus.sh /usr/local/sbin/start-incus.sh
RUN chmod +x /usr/local/sbin/start-incus.sh && mkdir -p /var/log/incus

RUN set -eux; \
    grep -q '^root:' /etc/subuid  || echo 'root:100000:65536'  >> /etc/subuid; \
    grep -q '^root:' /etc/subgid  || echo 'root:100000:65536'  >> /etc/subgid; \
    grep -q '^www-data:' /etc/subuid || echo 'www-data:165536:65536' >> /etc/subuid; \
    grep -q '^www-data:' /etc/subgid || echo 'www-data:165536:65536' >> /etc/subgid

# Por petición: por defecto entrar como www-data
RUN mkdir /var/www
RUN chown -R www-data:www-data /var/www
USER www-data

# El wrapper (setuid) arrancará Incus como root y luego hará su al www-data
ENTRYPOINT ["/sbin/entrypoint"]
CMD ["/bin/bash"]