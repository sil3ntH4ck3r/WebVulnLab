FROM ubuntu:latest

RUN apt-get update && \
    apt-get install -y bind9 bind9utils bind9-doc dnsutils

COPY named.conf.options /etc/bind/named.conf.options
COPY named.conf.local /etc/bind/named.conf.local
COPY db.domainzonetransfer.local /etc/bind/db.domainzonetransfer.local

EXPOSE 53/udp 53/tcp

CMD ["/usr/sbin/named", "-g"]