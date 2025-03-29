#!/bin/bash

chown -R www-data:www-data /var/www/html

# Iniciar el servidor Apache
apache2-foreground