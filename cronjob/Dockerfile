FROM debian:bookworm-slim

# Evitar prompts interactivos durante la instalación
ENV DEBIAN_FRONTEND=noninteractive

# Actualizar e instalar paquetes necesarios
RUN apt-get update && apt-get install -y \
    rsync \
    cron \
    sudo \
    openssh-server \
    vim \
    curl \
    apache2 \
    && rm -rf /var/lib/apt/lists/*

# Configurar sudo para que www-data pueda ejecutar cron sin contraseña
RUN echo "www-data ALL=(ALL) NOPASSWD: /usr/sbin/cron" >> /etc/sudoers

# Crear usuarios
RUN id -u www-data || useradd -m -s /bin/bash www-data && echo 'www-data:webpass123' | chpasswd

# Crear directorios necesarios con permisos adecuados
RUN mkdir -p /backup/logs && \
    mkdir -p /var/backup && \
    mkdir -p /var/www/html && \
    chown -R www-data:www-data /var/www/html /backup /var/backup && \
    chmod -R 755 /var/www/html && \
    chmod 1777 /backup /var/backup

# Crear archivo de log con los permisos correctos
RUN touch /backup/logs/backup.log && \
    chown www-data:www-data /backup/logs/backup.log && \
    chmod 664 /backup/logs/backup.log

# Copiar archivos de configuración
COPY compress.sh /usr/local/bin/compress.sh
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh

# Establecer permisos en el script de backup
RUN chmod +x /usr/local/bin/compress.sh
RUN chown root:root /usr/local/bin/compress.sh
RUN echo "*/2 * * * * /usr/local/bin/compress.sh" | crontab -u root -
RUN chmod 0755 /usr/local/bin/docker-entrypoint.sh
RUN chown root:root /usr/local/bin/docker-entrypoint.sh

RUN mkdir -p /backup/logs && \
    touch /backup/logs/backup.log && \
    chown root:root /backup/logs/backup.log

EXPOSE 22

USER www-data

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]