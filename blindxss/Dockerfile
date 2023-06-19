FROM php:7.2-apache

COPY src/ /var/www/html/

COPY startup.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/startup.sh

RUN chmod 666 /var/www/html/comments.txt

RUN apt update

RUN apt-get install python-pip -y
RUN pip install selenium
RUN pip install xvfbwrapper
COPY geckodriver /usr/local/bin/
COPY request.py /usr/local/bin/
RUN apt install firefox-esr -y

# Instalar Xvfb y otras dependencias necesarias
RUN apt-get install -y xvfb x11vnc x11-xkb-utils xfonts-100dpi xfonts-75dpi xfonts-scalable xfonts-cyrillic x11-apps

RUN apt install cron -y

# Agregar tarea cron
COPY cronjob /etc/cron.d/cronjob
RUN chmod 0644 /etc/cron.d/cronjob
RUN echo "" >> /etc/cron.d/cronjob  # Agregar una nueva línea vacía al final del archivo
RUN crontab /etc/cron.d/cronjob
RUN touch /var/log/cron.log

CMD ["/usr/local/bin/startup.sh"]

