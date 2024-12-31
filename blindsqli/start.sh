#!/bin/bash
set -e

echo "Iniciando MySQL..."
service mysql start

echo "Esperando a que MySQL esté listo..."
until mysqladmin ping -h "localhost" --silent; do
    sleep 1
done

echo "MySQL está listo. Ejecutando scripts de inicialización..."

# Crear la base de datos
mysql -u root -p"$MYSQL_ROOT_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS \`$MYSQL_DATABASE\` CHARACTER SET utf8 COLLATE utf8_general_ci;"

# Crear el usuario
mysql -u root -p"$MYSQL_ROOT_PASSWORD" -e "CREATE USER IF NOT EXISTS '$MYSQL_USER'@'localhost' IDENTIFIED BY '$MYSQL_PASSWORD';"

# Otorgar privilegios al usuario sobre la base de datos
mysql -u root -p"$MYSQL_ROOT_PASSWORD" -e "GRANT ALL PRIVILEGES ON \`$MYSQL_DATABASE\`.* TO '$MYSQL_USER'@'localhost';"

# Aplicar los cambios de privilegios
mysql -u root -p"$MYSQL_ROOT_PASSWORD" -e "FLUSH PRIVILEGES;"

# Ejecutar SQL adicional (por ejemplo, init.sql) si así lo deseas
# Verificar si existe /docker-entrypoint-initdb.d/init.sql
if [ -f /docker-entrypoint-initdb.d/init.sql ]; then
    echo "Ejecutando init.sql..."
    mysql -u root -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE" < /docker-entrypoint-initdb.d/init.sql
fi

echo "Inicialización de MySQL completada."

echo "Iniciando Apache..."
service apache2 start

# Para que el contenedor no se detenga,
# dejamos el proceso en primer plano:
tail -f /var/log/apache2/access.log