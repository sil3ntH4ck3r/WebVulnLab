FROM php:7.4-apache

# Copiar archivos al contenedor
COPY src/ /var/www/html/

# Configurar el servidor Apache
RUN apt-get update && \
    apt-get install -y texlive imagemagick

RUN chown -R www-data:www-data /var/www/html/
RUN chmod 666 /var/www/html/index.php

# Instalar paquetes LaTeX
#RUN apt-get update && apt-get install -y --no-install-recommends \
#    texlive-latex-extra \
#    && rm -rf /var/lib/apt/lists/*