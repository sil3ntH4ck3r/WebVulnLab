#!/bin/bash

# Agregar la entrada al archivo /etc/hosts
echo "127.0.0.1 csrf.local" >> /etc/hosts

# Cambiar los permisos del archivo index.php
chmod 666 /var/www/html/comments.txt

# Agregar enlace simbólico para geckodriver
ln -s /usr/local/bin/geckodriver /usr/bin/geckodriver

chmod +x /usr/local/bin/request.py

# Iniciar el servicio cron
service cron start

# Cambiar los permisos del archivo index.php
chmod 666 /var/www/html/users.txt

# Iniciar el servidor Apache
exec apache2-foreground