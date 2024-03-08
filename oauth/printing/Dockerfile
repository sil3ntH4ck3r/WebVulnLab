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

CMD ["python3", "app.py"]