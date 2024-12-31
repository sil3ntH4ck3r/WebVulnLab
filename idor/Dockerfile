FROM php:7.3-apache

# Instalar dependencias necesarias (MySQL, etc.)
RUN apt-get update && apt-get install -y \
    default-mysql-server \
    && docker-php-ext-install mysqli \
    && rm -rf /var/lib/apt/lists/*

# Copiar el script de arranque
COPY start.sh /start.sh
RUN chmod +x /start.sh

# Copiar la aplicación PHP a /var/www/html/
# Ajusta la ruta según dónde tengas tu "src"
COPY ./src/ /var/www/html/

# Exponer puertos: 80 (Apache) y 3306 (MySQL)
EXPOSE 80 3306

# El contenedor ejecutará start.sh al iniciarse
CMD ["/start.sh"]