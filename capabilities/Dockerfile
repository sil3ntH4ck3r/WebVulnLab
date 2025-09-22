FROM debian:bookworm-slim

RUN apt-get update && apt-get install -y --no-install-recommends \
    bash ca-certificates libcap2-bin coreutils procps findutils util-linux \
    gdb python3-minimal \
 && rm -rf /var/lib/apt/lists/*

RUN (id -u www-data >/dev/null 2>&1) || useradd -r -u 33 -g 33 -s /usr/sbin/nologin www-data

RUN mkdir -p /var/www/site && printf 'Contenido web\n' > /var/www/site/index.html \
 && chown -R root:root /var/www && chmod -R go-rwx /var/www

RUN setcap cap_setuid=ep "$(command -v gdb)" && getcap "$(command -v gdb)" || true

RUN chown -R www-data:www-data /var/www

USER www-data
WORKDIR /var/www
ENTRYPOINT ["/bin/bash","-lc","exec tail -f /dev/null"]