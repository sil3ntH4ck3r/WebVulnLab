#!/bin/bash

# Asegúrate de que estás en la rama 'dev'
git checkout dev

# Obtiene los cambios más recientes del repositorio remoto
git fetch origin

# Comprueba si hay actualizaciones disponibles
UPDATES_AVAILABLE=$(git log HEAD..origin/dev --oneline)

if [ -z "$UPDATES_AVAILABLE" ]; then
    echo "No hay actualizaciones disponibles."
else
    echo "Actualizaciones disponibles. Actualizando..."
    git merge origin/dev
    echo "Actualización completada."
fi
