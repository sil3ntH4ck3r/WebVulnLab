FROM --platform=linux/amd64 debian:12-slim

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y --no-install-recommends \
    build-essential gdb python3 file strace vim less procps binutils wget openssh-server sudo \
 && rm -rf /var/lib/apt/lists/*

RUN set -eux; \
    usermod -d /home/www-data -m www-data; \
    chsh -s /bin/bash www-data; \
    echo 'www-data:password123' | chpasswd

RUN set -eux; \
    sed -ri 's/^#?PasswordAuthentication .*/PasswordAuthentication yes/' /etc/ssh/sshd_config; \
    sed -ri 's/^#?PermitRootLogin .*/PermitRootLogin no/' /etc/ssh/sshd_config; \
    sed -ri 's/^#?UsePAM .*/UsePAM yes/' /etc/ssh/sshd_config; \
    mkdir -p /run/sshd; \
    ssh-keygen -A

RUN printf 'www-data ALL=(root) NOPASSWD: /usr/sbin/sshd, /bin/mkdir, /usr/bin/mkdir\n' \
    > /etc/sudoers.d/www-data-sshd && chmod 0440 /etc/sudoers.d/www-data-sshd

WORKDIR /opt/bof-lab
COPY src/adminsvc.c src/adminsvc.c
COPY build.sh build.sh
COPY check_env.sh check_env.sh
COPY MOTD.txt /etc/motd

RUN chmod +x build.sh check_env.sh && ./build.sh

RUN sh -lc "printf '\ncat /etc/motd || true\n' >> /etc/bash.bashrc"

EXPOSE 22

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

USER www-data
WORKDIR /home/www-data

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]