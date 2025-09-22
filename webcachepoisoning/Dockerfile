FROM node:18-alpine

# Instalar nginx y supervisor
RUN apk add --no-cache nginx supervisor

# Crear directorios necesarios
RUN mkdir -p /var/log/supervisor
RUN mkdir -p /run/nginx

# Copiar archivos de la aplicación
WORKDIR /var/www/html/
COPY src/package.json src/package-lock.json ./
RUN npm ci --omit=dev

COPY src/ .

# Copiar configuración de nginx
COPY src/nginx.conf /etc/nginx/nginx.conf

# Copiar configuración de supervisor
COPY src/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Exponer puertos
EXPOSE 80 3000

# Comando para iniciar supervisor que manejará nginx y node
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]