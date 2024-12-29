#!/bin/bash

# ==========================================================================
#                          VARIABLES Y CONFIGURACIÓN
# ==========================================================================
LOG_FILE="/var/log/auto_update.log"

# Colores (los mismos que utilizamos en el script anterior)
greenColour="\e[0;32m\033[1m"
endColour="\033[0m\e[0m"
redColour="\e[0;31m\033[1m"
blueColour="\e[0;34m\033[1m"
yellowColour="\e[0;33m\033[1m"
grayColour="\e[0;37m\033[1m"

# ==========================================================================
#                         FUNCIONES DE LOGGING
# ==========================================================================
log_info() {
    # Mensaje de información: azul en la consola
    echo -e "${yellowColour}[${endColour}${blueColour}INFO${endColour}${yellowColour}]${endColour} ${grayColour}$(date '+%Y-%m-%d %H:%M:%S') - $*${endColour}" | tee -a "$LOG_FILE"
}

log_success() {
    # Mensaje de éxito: verde en la consola
    echo -e "${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}$(date '+%Y-%m-%d %H:%M:%S') - $*${endColour}" | tee -a "$LOG_FILE"
}

log_warn() {
    # Mensaje de advertencia: amarillo en la consola
    echo -e "${yellowColour}[WARN]${endColour} ${grayColour}$(date '+%Y-%m-%d %H:%M:%S') - $*${endColour}" | tee -a "$LOG_FILE" >&2
}

log_error() {
    # Mensaje de error: rojo en la consola
    echo -e "${yellowColour}[${endColour}${redColour}ERROR${endColour}${yellowColour}]${endColour} ${grayColour}$(date '+%Y-%m-%d %H:%M:%S') - $*${endColour}" | tee -a "$LOG_FILE" >&2
}

# ==========================================================================
#                            BANNER INICIAL
# ==========================================================================
cat << "EOF"
  __      __         ___.    ____   ____       .__           .____             ___.    
 /  \    /  \  ____  \_ |__  \   \ /   / __ __ |  |    ____  |    |    _____   \_ |__  
 \   \/\/   /_/ __ \  | __ \  \   Y   / |  |  \|  |   /    \ |    |    \__  \   | __ \ 
  \        / \  ___/  | \_\ \  \     /  |  |  /|  |__|   |  \|    |___  / __ \_ | \_\ \
   \__/\  /   \___  > |___  /   \___/   |____/ |____/|___|  /|_______ \(____  / |___  /
        \/        \/      \/                              \/         \/     \/      \/ 
EOF
echo "                              Created by sil3nth4ck3r"

# ==========================================================================
#                           LÓGICA PRINCIPAL
# ==========================================================================

# Ruta del directorio del proyecto
PROJECT_DIR="$(dirname "$(readlink -f "$0")")"

# Cambiar al directorio del proyecto
if cd "$PROJECT_DIR" >> "$LOG_FILE" 2>&1; then
    log_info "Cambiando al directorio del proyecto: $PROJECT_DIR"
else
    log_error "No se pudo cambiar al directorio del proyecto: $PROJECT_DIR"
    exit 1
fi

# Asegurarse de que estamos en la rama 'dev'
if git checkout dev >> "$LOG_FILE" 2>&1; then
    log_info "Cambiando a la rama 'dev'"
else
    log_error "No se pudo cambiar a la rama 'dev'"
    exit 1
fi

# Obtener los cambios más recientes del repositorio remoto
if git fetch origin >> "$LOG_FILE" 2>&1; then
    log_info "Obteniendo cambios del repositorio remoto"
else
    log_error "No se pudieron obtener los cambios del repositorio remoto"
    exit 1
fi

# Mostrar los commits de la actualización (HEAD..origin/dev)
log_info "Commits de la actualización:"
git log HEAD..origin/dev --oneline | tee -a "$LOG_FILE"

# Verificar si hay actualizaciones disponibles
UPDATES_AVAILABLE=$(git log HEAD..origin/dev --oneline)

if [ -z "$UPDATES_AVAILABLE" ]; then
    log_warn "No hay actualizaciones disponibles."
else
    log_info "Actualizaciones disponibles. ¿Deseas instalarlas? (s/n)"
    read -r install_choice
    if [[ $install_choice == "s" ]]; then
        log_info "Actualizando..."
        if git merge origin/dev >> "$LOG_FILE" 2>&1; then
            log_success "Actualización completada."
        else
            log_error "No se pudo completar la actualización."
            exit 1
        fi
    else
        log_info "No se realizará la instalación de la actualización."
    fi
fi

log_info "Script de actualización finalizado."
exit 0