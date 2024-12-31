#!/bin/bash
set -e

# Crear el directorio para el socket si no existe
mkdir -p /run/mysqld
chown mysql:mysql /run/mysqld

# Iniciar MySQL en segundo plano
echo "Iniciando MySQL..."
mysqld &

# Esperar a que MySQL esté accesible
until mysqladmin ping -h127.0.0.1 --silent; do
    sleep 1
done

echo "MySQL está listo."

# (Opcional) Crear usuario/BD con tus credenciales deseadas:
# Ajusta usuario/password/nombre_BD a lo que quieras
MYSQL_ROOT_PASSWORD=rootpassword
MYSQL_DATABASE=database
MYSQL_USER=usuario
MYSQL_PASSWORD=password

echo "Configurando base de datos..."
mysql -u root -prootpassword<<EOF
ALTER USER 'root'@'localhost' IDENTIFIED BY '${MYSQL_ROOT_PASSWORD}';
CREATE DATABASE IF NOT EXISTS \`${MYSQL_DATABASE}\` CHARACTER SET utf8 COLLATE utf8_general_ci;
CREATE USER IF NOT EXISTS '${MYSQL_USER}'@'%' IDENTIFIED BY '${MYSQL_PASSWORD}';
GRANT ALL PRIVILEGES ON \`${MYSQL_DATABASE}\`.* TO '${MYSQL_USER}'@'%';
FLUSH PRIVILEGES;
EOF

echo "Base de datos configurada."

echo "Iniciando Apache en primer plano..."
apache2-foreground