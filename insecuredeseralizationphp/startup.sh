#!/bin/bash

# Cambiar los permisos del archivo index.php
chmod 777 /var/www/html/note.txt
chown -R www-data:www-data /var/www/html

# Iniciar el servidor Apache
apache2-foreground