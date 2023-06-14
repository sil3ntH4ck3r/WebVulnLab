FROM php:7.2-apache

COPY src/ /var/www/html/

COPY startup.sh /usr/local/bin/
RUN  chmod +x /usr/local/bin/startup.sh

CMD ["/usr/local/bin/startup.sh"]

EXPOSE 80
