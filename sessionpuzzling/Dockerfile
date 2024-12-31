FROM php:7.3-apache

COPY ./src/ /var/www/html/

# Instalar dependencias
RUN apt-get update && apt-get install -y libmcrypt-dev xvfb x11vnc x11-xkb-utils xfonts-100dpi xfonts-75dpi xfonts-scalable xfonts-cyrillic x11-apps python3 default-mysql-server
RUN docker-php-ext-install mysqli

# Habilitar la extensión de PHP en Apache
RUN a2enmod rewrite

RUN chmod 777 /var/www/html/comments.txt

RUN apt update

RUN apt-get install python3-pip -y
RUN pip3 install selenium
RUN pip3 install xvfbwrapper
RUN pip3 install requests
COPY geckodriver /usr/local/bin/
COPY request.py /usr/local/bin/
RUN apt install firefox-esr -y

RUN apt-get install -y cron

# Crear el archivo de tarea cron
RUN echo "* * * * * /usr/bin/python3 /usr/local/bin/request.py >> /var/log/cron.log 2>&1" > /var/spool/cron/crontabs/root
RUN chmod 600 /var/spool/cron/crontabs/root
RUN chown root:crontab /var/spool/cron/crontabs/root

# Copiar el script de arranque
COPY start.sh /start.sh
RUN chmod +x /start.sh

# Exponer puertos: 80 (Apache) y 3306 (MySQL)
EXPOSE 80 3306

# El contenedor ejecutará start.sh al iniciarse
CMD ["/start.sh"]