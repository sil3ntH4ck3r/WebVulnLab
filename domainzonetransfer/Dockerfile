FROM ubuntu:latest

RUN apt-get update && \
    apt-get install -y bind9 bind9utils bind9-doc dnsutils apache2

COPY named.conf.options /etc/bind/named.conf.options
COPY named.conf.local /etc/bind/named.conf.local
COPY db.domainzonetransfer.local /etc/bind/db.domainzonetransfer.local

COPY domainzonetransfer.local.conf /etc/apache2/sites-available/domainzonetransfer.local.conf
COPY src/index.html /var/www/html

RUN mkdir /var/www/codefusion
COPY index.html /var/www/codefusion


RUN a2ensite domainzonetransfer.local.conf && \
    a2dissite 000-default.conf && \
    service apache2 restart

EXPOSE 53/udp 53/tcp 80

CMD service apache2 start && /usr/sbin/named -g