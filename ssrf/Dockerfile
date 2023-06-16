FROM php:7.2-apache

# Copia los archivos del sitio web
COPY src/ /var/www/html/

# Expone el puerto 80 para el servicio web público
EXPOSE 80

# Instala Python y otras dependencias necesarias
RUN apt-get update && apt-get install -y python3

# Copia el script de inicio y otorga permisos de ejecución
COPY startup.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/startup.sh

# Define el comando de inicio para ejecutar el script
CMD ["/usr/local/bin/startup.sh"]