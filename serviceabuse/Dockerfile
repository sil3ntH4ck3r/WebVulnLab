FROM debian:12

ENV container=docker
STOPSIGNAL SIGRTMIN+3

RUN apt-get update && apt-get install -y --no-install-recommends \
    systemd systemd-sysv dbus bash ca-certificates tar findutils coreutils \
    && rm -rf /var/lib/apt/lists/*

RUN mkdir -p /opt/serviceAbuse/pre.d /opt/serviceAbuse/post.d /opt/serviceAbuse/bin \
    /etc/serviceAbuse /var/app/data /var/app/backups

COPY data-pack.service /etc/systemd/system/data-pack.service
COPY data-pack.timer   /etc/systemd/system/data-pack.timer
COPY run-hooks         /usr/local/sbin/run-hooks
COPY data-pack.sh       /usr/local/sbin/data-pack.sh
COPY init-launcher.c   /usr/local/src/init-launcher.c

RUN apt-get update && apt-get install -y --no-install-recommends gcc libc6-dev && rm -rf /var/lib/apt/lists/* && \
    gcc -O2 -s -o /usr/local/bin/init-launcher /usr/local/src/init-launcher.c && \
    apt-get purge -y gcc libc6-dev && apt-get autoremove -y && rm -rf /var/lib/apt/lists/*

RUN grep -qE '^[[:space:]]*OnBootSec=' /etc/systemd/system/data-pack.timer || \
      sed -i '/^\[Timer\]/a OnBootSec=10s' /etc/systemd/system/data-pack.timer && \
    grep -q '^\[Install\]' /etc/systemd/system/data-pack.timer || \
      printf '\n[Install]\nWantedBy=timers.target\n' >> /etc/systemd/system/data-pack.timer

RUN mkdir -p /etc/systemd/system/timers.target.wants && \
    ln -sf /etc/systemd/system/data-pack.timer \
          /etc/systemd/system/timers.target.wants/data-pack.timer

RUN mkdir -p /etc/systemd/system/default.target.wants && \
    ln -sf /lib/systemd/system/timers.target \
          /etc/systemd/system/default.target.wants/timers.target

RUN echo 'Just for testing propouses' > /var/app/data/ejemplo.txt

RUN chmod -R 0777 /opt/serviceAbuse && \
    chmod 0755 /usr/local/sbin/run-hooks /usr/local/sbin/data-pack.sh && \
    chown root:root /usr/local/sbin/run-hooks /usr/local/sbin/data-pack.sh && \
    chown root:root /usr/local/bin/init-launcher && chmod 4755 /usr/local/bin/init-launcher

RUN usermod -s /bin/bash www-data || true
USER www-data
WORKDIR /home/www-data

ENTRYPOINT ["/usr/local/bin/init-launcher"]