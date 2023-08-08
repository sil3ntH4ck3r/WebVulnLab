FROM php:7.2-apache

COPY src/ /var/www/html/

COPY startup.sh /usr/local/bin/
RUN  chmod +x /usr/local/bin/startup.sh

# Instalar dependencias
RUN apt-get update && apt-get install -y libmcrypt-dev xvfb x11vnc x11-xkb-utils xfonts-100dpi xfonts-75dpi xfonts-scalable xfonts-cyrillic x11-apps python3

RUN chmod 666 /var/www/html/comments.txt

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

CMD ["/usr/local/bin/startup.sh"]

EXPOSE 80
