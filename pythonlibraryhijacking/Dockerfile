FROM debian:bookworm-slim

RUN apt-get update && apt-get install -y --no-install-recommends \
    nano \
    python3 \
    sudo \
    ca-certificates \
    && rm -rf /var/lib/apt/lists/*

RUN mkdir -p /usr/local/share/backup_lib /var/backups/last /opt/hijack

COPY backup_utils.py /usr/local/share/backup_lib/backup_utils.py
RUN chmod 0644 /usr/local/share/backup_lib/backup_utils.py

COPY backup.py /usr/local/bin/backup.py
RUN chmod 0755 /usr/local/bin/backup.py

COPY run-backup /usr/local/bin/run-backup
RUN chmod 0755 /usr/local/bin/run-backup

COPY sudoers.d/www-data /etc/sudoers.d/www-data
RUN chmod 0440 /etc/sudoers.d/www-data

USER www-data

CMD ["bash", "-c", "tail -f /dev/null"]
