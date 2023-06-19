#!/bin/bash

# Cambiar los permisos del archivo index.php
chmod 666 /var/www/html/comments.txt

# Iniciar el servicio cron en segundo plano
service cron start

# Iniciar el servidor Apache en primer plano
exec apache2-foreground