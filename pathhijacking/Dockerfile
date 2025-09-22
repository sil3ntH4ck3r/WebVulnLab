FROM debian:bookworm-slim

WORKDIR /var/www/html
ENV DEBIAN_FRONTEND=noninteractive

#RUN sed -i 's/dk.archive.ubuntu.com/en.archive.ubuntu.com/g' /etc/apt/sources.list

RUN apt-get update && apt-get install -y \
    gcc \
    tar \
    sudo \
    nano \
 && rm -rf /var/lib/apt/lists/*

COPY backup.c .
RUN gcc -Wall -O2 backup.c -o /usr/local/bin/backup && \
    chown root:root /usr/local/bin/backup && \
    chmod u+s /usr/local/bin/backup && \
    rm backup.c && \
    mkdir /backup

EXPOSE 22

USER www-data
CMD ["bash", "-c", "tail -f /dev/null"]