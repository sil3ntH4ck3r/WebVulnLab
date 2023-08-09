FROM php:7.3-apache

# Instalar dependencias
RUN apt-get update && apt-get install -y libmcrypt-dev
RUN docker-php-ext-install mysqli

# Habilitar la extensión de PHP en Apache
RUN a2enmod rewrite

# Copiar la configuración de Apache
#COPY apache2.conf /etc/apache2/apache2.conf

# Iniciar Apache en el contenedor
CMD ["apache2-foreground"]
