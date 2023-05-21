FROM php:7.4-apache

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    pkg-config libxml2-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install zip xml

COPY src/ /var/www/html/

EXPOSE 80