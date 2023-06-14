#!/bin/bash

# Cambiar los permisos del archivo index.php
chmod 666 /var/www/html/users.txt

# Iniciar el servidor Apache
apache2-foreground