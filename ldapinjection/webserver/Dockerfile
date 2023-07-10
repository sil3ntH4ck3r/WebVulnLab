FROM php:7.4-apache

RUN apt-get update && apt-get install -y libldap2-dev && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/
RUN docker-php-ext-install ldap

COPY src/ /var/www/html/
