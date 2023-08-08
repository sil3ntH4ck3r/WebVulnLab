#!/bin/bash

# Agregar la entrada al archivo /etc/hosts
echo "127.0.0.1 sessionpuzzling.local" >> /etc/hosts

# Cambiar los permisos del archivo index.php
chmod 777 /var/www/html/comments.txt

# Agregar enlace simbólico para geckodriver
ln -s /usr/local/bin/geckodriver /usr/bin/geckodriver

chmod +x /usr/local/bin/request.py

# Iniciar el servicio cron
service cron start

# Iniciar el servidor Apache en primer plano
exec apache2-foreground