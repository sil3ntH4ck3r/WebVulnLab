FROM php:7.2-apache

COPY src/ /var/www/html/

RUN chown -R www-data:www-data /var/www/html
RUN chmod 777 /var/www/html/note.txt

EXPOSE 80