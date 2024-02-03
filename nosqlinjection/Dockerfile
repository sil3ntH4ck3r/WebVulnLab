FROM php:7.4-apache

RUN apt-get update && apt-get upgrade -y

# Instalar MongoDB
RUN apt-get install -y wget gnupg
RUN wget -qO - https://www.mongodb.org/static/pgp/server-4.4.asc | apt-key add -
RUN echo "deb http://repo.mongodb.org/apt/debian buster/mongodb-org/4.4 main" | tee /etc/apt/sources.list.d/mongodb-org-4.4.list
RUN apt-get update && apt-get install -y mongodb-org

# Copiar los archivos fuente a la carpeta del servidor web
COPY src/ /var/www/html/

# Exponer el puerto 80
EXPOSE 80

# Instalar la extensión de MongoDB para PHP
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Crear directorio de datos y asignar permisos
RUN mkdir -p /data/db && chown -R mongodb:mongodb /data/db

# Iniciar el servidor de MongoDB al ejecutar el contenedor
CMD service apache2 start && mongod --bind_ip_all --dbpath /data/db
