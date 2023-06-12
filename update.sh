#!/bin/bash

# Funciones para imprimir mensajes con colores
print_info() {
    printf "\e[1;34m[*] $1\e[0m\n"
}

print_success() {
    printf "\e[1;32m[+] $1\e[0m\n"
}

print_warning() {
    printf "\e[1;33m[!] $1\e[0m\n"
}

print_error() {
    printf "\e[1;31m[-] $1\e[0m\n"
}

# Utilizar Here Document para imprimir el banner
cat << "EOF"
  __      __         ___.    ____   ____       .__           .____             ___.    
 /  \    /  \  ____  \_ |__  \   \ /   / __ __ |  |    ____  |    |    _____   \_ |__  
 \   \/\/   /_/ __ \  | __ \  \   Y   / |  |  \|  |   /    \ |    |    \__  \   | __ \ 
  \        / \  ___/  | \_\ \  \     /  |  |  /|  |__|   |  \|    |___  / __ \_ | \_\ \
   \__/\  /   \___  > |___  /   \___/   |____/ |____/|___|  /|_______ \(____  / |___  /
        \/        \/      \/                              \/         \/     \/      \/ 
EOF
echo "                              Created by sil3nth4ck3r"

# Obtiene la ruta del directorio actual
PROJECT_DIR="$(dirname "$(readlink -f "$0")")"

# Cambia al directorio del proyecto
if cd "$PROJECT_DIR"; then
    print_info "Cambiando al directorio del proyecto: $PROJECT_DIR"
else
    print_error "No se pudo cambiar al directorio del proyecto: $PROJECT_DIR"
    exit 1
fi

# Asegúrate de que estás en la rama 'dev'
if git checkout dev > /dev/null 2>&1; then
    print_info "Cambiando a la rama 'dev'"
else
    print_error "No se pudo cambiar a la rama 'dev'"
    exit 1
fi

# Obtiene los cambios más recientes del repositorio remoto
if git fetch origin > /dev/null 2>&1; then
    print_info "Obteniendo cambios del repositorio remoto"
else
    print_error "No se pudieron obtener los cambios del repositorio remoto"
    exit 1
fi

# Comprueba si hay actualizaciones disponibles
UPDATES_AVAILABLE=$(git log HEAD..origin/dev --oneline)

if [ -z "$UPDATES_AVAILABLE" ]; then
    print_warning "No hay actualizaciones disponibles."
else
    print_info "Actualizaciones disponibles. ¿Deseas instalarlas? (s/n)"
    read -r install_choice
    if [[ $install_choice == "s" ]]; then
        print_info "Actualizando..."
        if git merge origin/dev > /dev/null 2>&1; then
            print_success "Actualización completada."
        else
            print_error "No se pudo completar la actualización."
            exit 1
        fi
    else
        print_info "No se realizará la instalación de la actualización."
    fi
fi
