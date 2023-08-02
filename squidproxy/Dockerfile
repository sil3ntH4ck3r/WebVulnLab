FROM php:7.2-apache

# Instalar Squid
RUN apt-get update && \
    apt-get install -y squid

# Copiar el código del servicio web al contenedor
COPY ./src /var/www/html

# Habilitar el módulo de reescritura de Apache
RUN a2enmod rewrite

# Copiar la configuración personalizada de Apache al contenedor
COPY ./000-default.conf /etc/apache2/sites-available/000-default.conf

# Copiar la configuración personalizada de Squid al contenedor
COPY ./squid.conf /etc/squid/squid.conf

# Copiar el script de inicio personalizado al contenedor
COPY ./start.sh /start.sh

# Exponer el puerto del proxy Squid
EXPOSE 3128

# Ejecutar el script de inicio personalizado
CMD ["/startup.sh"]