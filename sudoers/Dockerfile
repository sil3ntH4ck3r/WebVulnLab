FROM debian:bookworm-slim

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y \
    sudo \
    vim \
    wget \
    curl \
    python3 \
    gcc \
    make \
    tar \
    findutils \
    && rm -rf /var/lib/apt/lists/*

# Crear usuarios realistas
RUN id -u www-data || useradd -m -s /bin/bash www-data && echo 'www-data:webpass123' | chpasswd
RUN id -u backup || useradd -m -s /bin/bash backup && echo 'backup:backup456' | chpasswd
RUN id -u sysadmin || useradd -m -s /bin/bash sysadmin && echo 'sysadmin:admin789' | chpasswd
RUN id -u developer || useradd -m -s /bin/bash developer && echo 'developer:dev000' | chpasswd

# Crear directorios
RUN mkdir -p /opt/scripts
RUN mkdir -p /tmp/backup
RUN mkdir -p /var/log/app
RUN mkdir -p /var/www/html

# Copiar scripts y configuraciones
COPY scripts/ /opt/scripts/
RUN chmod +x /opt/scripts/*.sh

COPY sudoers.d/ /etc/sudoers.d/
RUN chmod 440 /etc/sudoers.d/*

RUN usermod -a -G backup backup
RUN usermod -a -G sysadmin backup
RUN usermod -a -G developer backup
RUN usermod -a -G backup sysadmin
RUN usermod -a -G developer sysadmin

RUN mkdir -p /home/backup /home/sysadmin /home/developer
RUN chmod 750 /home/backup /home/sysadmin /home/developer
RUN chown backup:backup /home/backup
RUN chown sysadmin:sysadmin /home/sysadmin
RUN chown developer:developer /home/developer

RUN chmod 755 /tmp/backup
RUN chown backup:backup /tmp/backup
COPY app.log /var/log/app/app.log
USER www-data
CMD ["/bin/bash", "-c", "tail -f /dev/null"]