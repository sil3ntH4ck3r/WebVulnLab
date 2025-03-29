FROM php:7.2-apache

COPY src/ /var/www/html/
COPY startup.sh /usr/local/bin/
RUN  chmod +x /usr/local/bin/startup.sh
RUN chown -R www-data:www-data /var/www/html
RUN chmod 777 /var/www/html/note.txt

EXPOSE 80

CMD ["/usr/local/bin/startup.sh"]