FROM postgres:latest

# Instalar las dependencias necesarias
RUN apt-get update && apt-get install -y \
    postgresql-client-13 \
    postgresql-contrib-13 \
    python3.9 \
    python3-psycopg2 \
    python3-flask \
    python3-flask-sqlalchemy \
    python3-pip \
    python3-jwt \
    python3-requests-oauthlib \
    python3-reportlab \
    && rm -rf /var/lib/apt/lists/*

# Establecer el directorio de trabajo
WORKDIR /app

# Agregar la aplicación a la imagen
ADD . /app

# Establecer variables de entorno
ENV FLASK_ENV development
ENV DATABASE_URI=postgresql://username:password@db:5432/printing_db

# Exponer el puerto 5000
EXPOSE 5000

# Copiar el script de inicialización
COPY init_script.sh /usr/local/bin/init_script.sh

# Cambiar los permisos del script para que sea ejecutable
RUN chmod +x /usr/local/bin/init_script.sh

# Ejecutar el script al iniciar el contenedor
CMD ["bash", "-c", "/usr/local/bin/init_script.sh && python3 app.py"]