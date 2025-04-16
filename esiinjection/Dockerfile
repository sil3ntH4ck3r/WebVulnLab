FROM php:8.0-apache

RUN apt-get update && \
    apt-get install -y libxslt1-dev libxml2-dev openjdk-11-jdk supervisor wget && \
    docker-php-ext-install xsl

RUN sed -i 's/Listen 80/Listen 127.0.0.1:8080/g' /etc/apache2/ports.conf

COPY src/ /var/www/html/
RUN chown -R www-data:www-data /var/www/html && \
    chmod 666 /var/www/html/tickets.json

RUN mkdir -p /app
WORKDIR /app
COPY XalanProxy.java /app/
RUN javac XalanProxy.java

COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
EXPOSE 80

CMD ["/usr/bin/supervisord", "-n"]