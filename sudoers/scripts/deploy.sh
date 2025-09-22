#!/bin/bash
set -e  # Salir si cualquier comando falla

error_handler() {
    echo "Error en línea $1: Falló el despliegue"
    exit 1
}

trap 'error_handler $LINENO' ERR

if [ $# -eq 0 ]; then
    echo "Uso: $0 <directorio_proyecto>"
    exit 1
fi

PROJECT_DIR=$1
echo "Desplegando proyecto desde: $PROJECT_DIR"

echo "Copiando archivos..."
eval "cp -r $PROJECT_DIR/* /var/www/html/"
echo "Proyecto desplegado exitosamente"