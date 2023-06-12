FROM ubuntu:14.04

RUN apt-get update && \
    apt-get install -y apache2

RUN a2enmod cgi && \
    mkdir /var/www/html/cgi-bin && \
 chown www-data:www-data /var/www/html/cgi-bin

COPY system_info.cgi /var/www/html/cgi-bin/
RUN chmod +x /var/www/html/cgi-bin/system_info.cgi

COPY 000-default.conf /etc/apache2/sites-available/

COPY serve-cgi-bin.conf /etc/apache2/conf-available/

# Install vulnerable bash

RUN apt-get install -y build-essential wget
RUN wget https://ftp.gnu.org/gnu/bash/bash-4.3.tar.gz --no-check-certificate && \
    tar zxvf bash-4.3.tar.gz && \
    cd bash-4.3 && \
    ./configure && \
    make && \
    make install

RUN mkdir /var/www/html/website

EXPOSE 80

CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]