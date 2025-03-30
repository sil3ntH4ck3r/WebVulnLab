FROM php:8.0-apache

# Instalar dependencias: redis-server y supervisor
RUN apt-get update && apt-get install -y redis-server supervisor && apt-get clean

# Instalar la extensión de PHP para Redis vía PECL
RUN pecl install redis && docker-php-ext-enable redis

# Copiar la aplicación PHP a la raíz de Apache
COPY src /var/www/html/

# Copiar la configuración de Supervisor
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Exponer puertos: 80 para la web y 6379 para Redis
EXPOSE 80 6379

CMD ["/usr/bin/supervisord", "-n"]
