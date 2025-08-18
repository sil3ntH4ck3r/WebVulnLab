#!/bin/bash

# ----------------------------------------------------------------------
#                       DECLARACIÓN DE VARIABLES Y ARRAYS
# ----------------------------------------------------------------------

# Variables
FILE="/etc/docker/daemon.json"
MAX_SUBNETS=100
GENERATE_ULA=true
TMP_FILE=""
BACKUP_FILE=""
LOG_FILE="/var/log/assign_subnet.log"

# Establecer valor predeterminado para ignore_errors y hide_output
ignore_errors="n"   # Por defecto "n"
hide_output="s"     # Por defecto "s"

ipv6_exists=false
fixed_cidr_exists=false+

containers=(
  #Siguen la siguiente estructura:
    #nombreContenedor ; rutaDockerfile ; mapeoDePuertos ; parametrosAdicionales ; ipEstatica
    
  "menu_v2;$PWD/menu;8080:80;--add-host=menu.local:172.18.0.2;172.18.0.2"
  "lfi_v2;$PWD/lfi;8000:80;;172.18.0.3"
  "csrf_v2;$PWD/csrf;8001:80;;172.18.0.4"
  "blindxxe_v2;$PWD/blindxxe;8002:80;;172.18.0.5"
  "xxe_v2;$PWD/xxe;8003:80;;172.18.0.6"
  "xss_v2;$PWD/xss;8004:80;;172.18.0.7"
  "sqli_v2;$PWD/sqli;8005:80;;172.18.0.8"
  "domainzonetransfer_v2;$PWD/domainzonetransfer;8039:80;;172.18.0.9"
  "ssrf_v2;$PWD/ssrf;8006:80;;172.18.0.10"
  "paddingoracleattack_v2;$PWD/paddingoracleattack;8007:80;;172.18.0.11"
  "typejuggling_v2;$PWD/typejuggling;8008:80;;172.18.0.12"
  "rfi_v2;$PWD/rfi;8009:80;;172.18.0.13"
  "insecuredeseralizationphp_v2;$PWD/insecuredeseralizationphp;8010:80;;172.18.0.14"
  "latexinjection_v2;$PWD/latexinjection;8011:80;;172.18.0.15"
  "xpathinjection_v2;$PWD/xpathinjection;8012:80;;172.18.0.16"
  "shellshock_v2;$PWD/shellshock;8013:80;;172.18.0.17"
  "blindsqli_v2;$PWD/blindsqli;8014:80;;172.18.0.18"
  "blindxss_v2;$PWD/blindxss;8015:80;;172.18.0.19"
  "htmlinjection_v2;$PWD/htmlinjection;8016:80;;172.18.0.20"
  "idor_v2;$PWD/idor;8017:80;;172.18.0.21"
  "ssti_v2;$PWD/ssti;8018:80;;172.18.0.22"
  "csti_v2;$PWD/csti;8019:80;;172.18.0.23"
  "nosqlinjection_v2;$PWD/nosqlinjection;8020:80;;172.18.0.24"
  "fileuploadabuse_v2;$PWD/fileuploadabuse;8024:80;;172.18.0.25"
  "prototypepollution_v2;$PWD/prototypepollution;8025:80;;172.18.0.26"
  "openredirect_v2;$PWD/openredirect;8026:80;;172.18.0.27"
  "webdav_v2;$PWD/webdav;8027:80;;172.18.0.28"
  "squidproxy_v2;$PWD/squidproxy;8028:80;--cap-add=NET_ADMIN;172.18.0.29"
  "cors_v2;$PWD/cors;8029:80;;172.18.0.30"
  "sqltruncation_v2;$PWD/sqltruncation;8030:80;;172.18.0.31"
  "jwt_v2;$PWD/jwt;8032:80;;172.18.0.32"
  "racecondition_v2;$PWD/racecondition;8033:80;;172.18.0.33"
  "cssi_v2;$PWD/cssi;8034:80;;172.18.0.34"
  "yamldeseralization_v2;$PWD/yamldeseralization;8042:80;;172.18.0.35"
  "pickledeseralization_v2;$PWD/pickledeseralization;8038:80;;172.18.0.36"
  "snmp_v2;$PWD/snmp;8040:80 161:161/udp;--sysctl net.ipv6.conf.all.disable_ipv6=0 --sysctl net.ipv6.conf.default.disable_ipv6=0;172.18.0.37"
  "http3_v2;$PWD/http3;;;172.18.0.38"
  #"httpsmuggling_v2;$PWD/httpsmuggling;8043:80;;172.18.0.39"
  "sessionpuzzling_v2;$PWD/sessionpuzzling;8031:80;;172.18.0.40"
  "redis_v2;$PWD/redis;8044:80;;172.18.0.54"
  "nodejsdeserelization_v2;$PWD/nodejsdeserelization;8045:80;;172.18.0.55"
  "esiinjection_v2;$PWD/esiinjection;8046:8080;;172.18.0.56"
  "cypherinjection_v2;$PWD/cypherinjection;8047:80;;172.18.0.57"
  "javadeserelization_v2;$PWD/javadeserelization;8048:80;;172.18.0.58"
  #"jndiinjection_v2;$PWD/jndiinjection;8049:80;;172.18.0.59"
)

otros=(
  #"Construyendo contenedores para AWS Abuse;docker-compose -f $PWD/aws/docker-compose.yml up -d"
  #"Construyendo contenedores para LDAP Injection;docker-compose -f $PWD/ldapinjection/docker-compose.yml up -d"
  #"Construyendo contenedores para API Abuse;docker-compose -f $PWD/apiabuse/docker-compose.yml up -d"
  #"Contruyendo contenedores para GraphQL;docker-compose -f $PWD/graphql/docker-compose.yml up -d" # CAMBIAR A CONTENEDOR SOLO NORMAL
  #"Contruyendo contenedores para OAuth;docker-compose -f $PWD/oauth/docker-compose.yml up -d"
)

# Colores
greenColour="\e[0;32m\033[1m"
endColour="\033[0m\e[0m"
redColour="\e[0;31m\033[1m"
blueColour="\e[0;34m\033[1m"
yellowColour="\e[0;33m\033[1m"
purpleColour="\e[0;35m\033[1m"
turquoiseColour="\e[0;36m\033[1m"
grayColour="\e[0;37m\033[1m"

# ----------------------------------------------------------------------
#                            BANNER
# ----------------------------------------------------------------------
cat << "EOF"
  __      __         ___.    ____   ____       .__           .____             ___.    
 /  \    /  \  ____  \_ |__  \   \ /   / __ __ |  |    ____  |    |    _____   \_ |__  
 \   \/\/   /_/ __ \  | __ \  \   Y   / |  |  \|  |   /    \ |    |    \__  \   | __ \ 
  \        / \  ___/  | \_\ \  \     /  |  |  /|  |__|   |  \|    |___  / __ \_ | \_\ \
   \__/\  /   \___  > |___  /   \___/   |____/ |____/|___|  /|_______ \(____  / |___  /
        \/        \/      \/                              \/         \/     \/      \/
EOF
echo -e "                              Created by sil3nth4ck3r \n"

# ----------------------------------------------------------------------
#                         CERTIFICADOS PARA HTTP3
# ----------------------------------------------------------------------
generate_http3_certificates() {
    local cert="/etc/ssl/certs/http3.local.crt"
    local key="/etc/ssl/private/http3.local.key"
    if [ ! -f "$cert" ] || [ ! -f "$key" ]; then
        log_info "Generando certificados autofirmados para http3.local..."
        mkdir -p /etc/ssl/certs /etc/ssl/private
        openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
            -keyout "$key" -out "$cert" \
            -subj "/CN=http3.local"
        if [ $? -eq 0 ]; then
            log_info "Certificados generados correctamente en $cert y $key."
        else
            log_error "Error generando certificados para http3.local."
            exit 1
        fi
    else
        log_info "Certificados para http3.local ya existen."
    fi
}

# ----------------------------------------------------------------------
#                           CERTIFICADOS PARA MENU.LOCAL
# ----------------------------------------------------------------------
generate_http3_certificates_menu() {
    local cert="/etc/ssl/certs/menu.local.crt"
    local key="/etc/ssl/private/menu.local.key"
    local caroot userhome userdb exp_date exp_ts now_ts

    # 1) Instalar mkcert si no está
    if ! command -v mkcert &>/dev/null; then
        log_info "mkcert no encontrado, instalando..."
        apt-get update -qq
        apt-get install -y --no-install-recommends libnss3-tools wget ca-certificates
        MKCERT_LATEST=$(wget -qO- https://api.github.com/repos/FiloSottile/mkcert/releases/latest \
                       | grep '"tag_name":' | head -1 | cut -d '"' -f4)
        wget -qO /usr/local/bin/mkcert \
             "https://github.com/FiloSottile/mkcert/releases/download/${MKCERT_LATEST}/mkcert-${MKCERT_LATEST}-linux-amd64"
        chmod +x /usr/local/bin/mkcert
        log_info "mkcert instalado."
    fi

    # 2) Si ya existen, comprobamos fecha de caducidad
    if [[ -f "$cert" && -f "$key" ]]; then
        exp_date=$(openssl x509 -enddate -noout -in "$cert" | cut -d= -f2)
        exp_ts=$(date --date="$exp_date" +%s)
        now_ts=$(date +%s)
        if (( exp_ts > now_ts )); then
            log_info "Certificado válido hasta $exp_date. Nada que hacer."
            return
        else
            log_warn "Certificado caducado ($exp_date). Regenerando..."
        fi
    else
        log_info "No existe certificado previo. Generando uno nuevo..."
    fi

    # 3) mkcert -install y obtenemos CAROOT
    log_info "Instalando la CA local de mkcert..."
    mkcert -install
    caroot=$(mkcert -CAROOT)

    # 4) Importar la CA en el store del sistema (Chrome/Chromium)
    log_info "Copiando rootCA.pem al store del sistema..."
    cp "${caroot}/rootCA.pem" /usr/local/share/ca-certificates/mkcert-rootCA.crt
    update-ca-certificates -q

    # 5) Inicializar NSS DB global (solo si no existe)
    log_info "Inicializando NSS DB global..."
    mkdir -p /etc/pki/nssdb
    if [[ ! -f /etc/pki/nssdb/key4.db ]]; then
        certutil -N -d sql:/etc/pki/nssdb -f /dev/null
    fi

    # 6) Importar la CA en la base NSS global
    log_info "Importando CA en NSS DB global..."
    certutil -A \
      -d sql:/etc/pki/nssdb \
      -n "mkcert development CA" \
      -t "CT,," \
      -i "${caroot}/rootCA.pem" \
      -f /dev/null

    # 7) Importar la CA en el perfil Firefox del SUDO_USER
    if [[ -n "$SUDO_USER" && "$SUDO_USER" != "root" ]]; then
        userhome=$(getent passwd "$SUDO_USER" | cut -d: -f6)
        userdb="sql:${userhome}/.pki/nssdb"
        log_info "Importando CA en NSS DB de Firefox para $SUDO_USER..."
        mkdir -p "${userhome}/.pki/nssdb"
        certutil -N -d "${userdb}" -f /dev/null 2>/dev/null || true
        certutil -A \
          -d "${userdb}" \
          -n "mkcert development CA" \
          -t "CT,," \
          -i "${caroot}/rootCA.pem" \
          -f /dev/null
        chown -R "$SUDO_USER":"$SUDO_USER" "${userhome}/.pki/nssdb"
    else
        log_warn "No se detectó SUDO_USER válido; omitiendo importación en Firefox."
    fi

    # 8) Generar el certificado para menu.local
    log_info "Generando certificado mkcert para menu.local..."
    mkdir -p "$(dirname "$cert")" "$(dirname "$key")"
    mkcert -cert-file "$cert" -key-file "$key" "menu.local" "127.0.0.1" "::1"

    log_info "Certificado menu.local listo en:"
    log_info "  CRT=$cert"
    log_info "  KEY=$key"
}

remove_http3_certificates_menu() {
    local cert="/etc/ssl/certs/menu.local.crt"
    local key="/etc/ssl/private/menu.local.key"
    local caroot

    # 1) Borrar certificados de menu.local
    rm -f "$cert" "$key"
    log_info "Eliminados $cert y $key."

    # 2) Recuperar CAROOT y eliminar CA del sistema
    if command -v mkcert &>/dev/null; then
        caroot=$(mkcert -CAROOT)
        rm -f /usr/local/share/ca-certificates/mkcert-rootCA.crt
        update-ca-certificates -q
        log_info "CA mkcert eliminada del store del sistema."

        # 3) Eliminar CA de NSS global
        certutil -d sql:/etc/pki/nssdb -D -n "mkcert development CA" -f /dev/null || true
        log_info "CA mkcert eliminada de NSS DB global."

        # 4) Eliminar CA del perfil de Firefox del usuario
        if [[ -n "$SUDO_USER" && "$SUDO_USER" != "root" ]]; then
            local userhome
            userhome=$(getent passwd "$SUDO_USER" | cut -d: -f6)
            local userdb="sql:${userhome}/.pki/nssdb"
            certutil -d "${userdb}" -D -n "mkcert development CA" -f /dev/null || true
            log_info "CA mkcert eliminada del perfil NSS de $SUDO_USER."
        fi
    fi
}

# ----------------------------------------------------------------------
#           CONFIGURAR RED CON SUBNET FIJO (para IP estáticas)
# ----------------------------------------------------------------------
configure_network() {
    log_info "Creando la red WebVulnLab-Network con subnet 172.18.0.0/16"
    # Si la red ya existe, no se vuelve a crear.
    docker network inspect WebVulnLab-Network > /dev/null 2>&1 || \
      docker network create --subnet=172.18.0.0/16 WebVulnLab-Network >> "$LOG_FILE" 2>&1
}

# ----------------------------------------------------------------------
#                               LOGGING
# ----------------------------------------------------------------------
log_info() {
    echo -e "${yellowColour}[${endColour}${blueColour}INFO${endColour}${yellowColour}]${endColour} ${grayColour}$(date '+%Y-%m-%d %H:%M:%S') - $*${endColour}" | tee -a "$LOG_FILE"
}
log_warn() {
    echo -e "${yellowColour}[WARN]${endColour} ${grayColour}$(date '+%Y-%m-%d %H:%M:%S') - $*${endColour}" | tee -a "$LOG_FILE" >&2
}
log_error() {
    echo -e "${yellowColour}[${endColour}${redColour}ERROR${endColour}${yellowColour}]${endColour} ${grayColour}$(date '+%Y-%m-%d %H:%M:%S') - $*${endColour}" | tee -a "$LOG_FILE" >&2
}

# ----------------------------------------------------------------------
#                           CLEANUP Y TRAPS
# ----------------------------------------------------------------------
original_stty=$(stty -g)
stty -echoctl
cleanup_exit() {
    if [[ -n "$TMP_FILE" && -f "$TMP_FILE" ]]; then
        rm -f "$TMP_FILE"
        log_info "Archivo temporal $TMP_FILE eliminado."
    fi
    stty "$original_stty"
}
cleanup_signal() {
    log_warn "Saliendo del programa..."
    setup_tablero_vhost
    entradasEtcHosts
    cleanup_exit
    exit 1
}
trap cleanup_exit EXIT
trap cleanup_signal INT TERM

# ----------------------------------------------------------------------
#                           DEPENDENCIAS
# ----------------------------------------------------------------------
if [[ "$EUID" -ne 0 ]]; then
    log_error "Este script debe ejecutarse con privilegios de superusuario (sudo)."
    exit 1
fi

declare -A CMD_TO_PKG=(
    ["jq"]="jq"
    ["openssl"]="openssl"
    ["docker"]="docker-ce"
    ["docker-compose"]="docker-compose"
    ["php"]="php"
    ["apache2"]="apache2"
    ["ldapadd"]="ldap-utils"
    ["ip"]="iproute2"
    ["stty"]="coreutils"
    ["sed"]="sed"
    ["systemctl"]="systemd"
    ["a2enmod"]="apache2"
    ["a2ensite"]="apache2"
)
REQUIRED_COMMANDS=(
    "jq" "openssl" "docker" "docker-compose" "php" "apache2"
    "ldapadd" "ip" "stty" "sed" "systemctl" "a2enmod" "a2ensite"
)
INSTALL_PACKAGES=(
    "build-essential" "cmake" "git" "libjson-c-dev" "libwebsockets-dev"
)
for cmd in "${REQUIRED_COMMANDS[@]}"; do
    if ! command -v "$cmd" &> /dev/null; then
        pkg=${CMD_TO_PKG[$cmd]}
        if [[ -n "$pkg" && ! " ${INSTALL_PACKAGES[@]} " =~ " ${pkg} " ]]; then
            INSTALL_PACKAGES+=("$pkg")
        fi
    fi
done

install_docker_official() {
    log_info "Instalando Docker desde el repositorio oficial..."
    apt-get install -y apt-transport-https ca-certificates curl gnupg lsb-release
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg | gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg
    echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | tee /etc/apt/sources.list.d/docker.list > /dev/null
    apt-get update && apt-get install -y docker-ce docker-ce-cli containerd.io
    if ! command -v docker &> /dev/null; then
        log_error "Error al instalar Docker desde el repositorio oficial."
        exit 1
    fi
    log_info "Docker instalado correctamente."
}

install_docker_compose() {
    log_info "Instalando docker-compose manualmente..."
    COMPOSE_VERSION=$(curl -s https://api.github.com/repos/docker/compose/releases/latest | grep tag_name | cut -d '"' -f 4)
    curl -L "https://github.com/docker/compose/releases/download/${COMPOSE_VERSION}/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    chmod +x /usr/local/bin/docker-compose
    ln -s /usr/local/bin/docker-compose /usr/bin/docker-compose
    if ! command -v docker-compose &> /dev/null; then
        log_error "Error al instalar docker-compose."
        exit 1
    fi
    log_info "docker-compose instalado correctamente."
}

install_ttyd() {
    log_info "Actualizando repositorios..."
    sudo apt-get update
    if [[ $? -ne 0 ]]; then
        log_error "Error al actualizar repositorios."
        exit 1
    fi

    log_info "Instalando dependencias: build-essential, cmake, git, libjson-c-dev, libwebsockets-dev..."
    sudo apt-get install -y build-essential cmake git libjson-c-dev libwebsockets-dev
    if [[ $? -ne 0 ]]; then
        log_error "Error al instalar las dependencias."
        exit 1
    fi

    if command -v ttyd &> /dev/null; then
        log_info "ttyd ya está instalado. Saltando instalación."
        return
    fi

    log_info "Clonando el repositorio de ttyd..."
    git clone https://github.com/tsl0922/ttyd.git
    if [[ $? -ne 0 ]]; then
        log_error "Error al clonar el repositorio de ttyd."
        exit 1
    fi

    cd ttyd || { log_error "No se pudo acceder al directorio ttyd."; exit 1; }
    log_info "Creando y accediendo al directorio build..."
    mkdir -p build && cd build
    if [[ $? -ne 0 ]]; then
        log_error "Error al crear o acceder al directorio build."
        exit 1
    fi

    log_info "Ejecutando cmake..."
    cmake ..
    if [[ $? -ne 0 ]]; then
        log_error "Error al ejecutar cmake."
        exit 1
    fi

    log_info "Compilando ttyd..."
    make
    if [[ $? -ne 0 ]]; then
        log_error "Error al compilar ttyd."
        exit 1
    fi

    log_info "Instalando ttyd..."
    sudo make install
    if [[ $? -ne 0 ]]; then
        log_error "Error al instalar ttyd."
        exit 1
    fi

    if ! command -v ttyd &> /dev/null; then
        log_error "ttyd no se instaló correctamente."
        exit 1
    fi

    log_info "ttyd instalado correctamente."
}

if ! command -v docker &> /dev/null; then
    install_docker_official
fi

if ! command -v docker-compose &> /dev/null; then
    install_docker_compose
fi

if [ ${#INSTALL_PACKAGES[@]} -gt 0 ]; then
    log_info "Instalando dependencias necesarias: ${INSTALL_PACKAGES[*]}"
    apt-get update && apt-get install -y "${INSTALL_PACKAGES[@]}" >> "$LOG_FILE" 2>&1
    if [ $? -ne 0 ]; then
        log_error "Error al instalar las dependencias: ${INSTALL_PACKAGES[*]}"
        exit 1
    fi
    log_info "Dependencias instaladas correctamente."
fi

if ! systemctl is-active --quiet docker; then
    log_info "Iniciando el servicio de Docker..."
    systemctl start docker
    if ! systemctl is-active --quiet docker; then
        log_error "Error al iniciar el servicio de Docker."
        exit 1
    fi
    log_info "Servicio de Docker iniciado correctamente."
fi

# Verificamos e instalamos ttyd si no está presente
if ! command -v ttyd &> /dev/null; then
    install_ttyd
fi

# ----------------------------------------------------------------------
#                           CONFIGURACIÓN TERMINAL
# ----------------------------------------------------------------------
log_info "Configurando los archivos necesarios para la terminal vía web"
WRAPPER_SCRIPT="/usr/local/bin/docker_exec_wrapper.sh"
if [ ! -f "./docker_exec_wrapper.sh" ]; then
    log_error "El script docker_exec_wrapper.sh no se encuentra en el directorio actual."
    exit 1
fi
if [ ! -f "$WRAPPER_SCRIPT" ] || ! cmp -s "./docker_exec_wrapper.sh" "$WRAPPER_SCRIPT"; then
    cp "./docker_exec_wrapper.sh" "$WRAPPER_SCRIPT"
    log_info "Script docker_exec_wrapper.sh copiado a $WRAPPER_SCRIPT"
else
    log_info "El script docker_exec_wrapper.sh ya está presente en $WRAPPER_SCRIPT"
fi
chmod 755 "$WRAPPER_SCRIPT"
chown root:root "$WRAPPER_SCRIPT"
log_info "Permisos y propiedad asignados a $WRAPPER_SCRIPT"
SUDOERS_FILE="/etc/sudoers.d/www-data-docker-exec"
if [ ! -f "$SUDOERS_FILE" ]; then
    echo "www-data ALL=(ALL) NOPASSWD: $WRAPPER_SCRIPT" > "$SUDOERS_FILE"
    chmod 440 "$SUDOERS_FILE"
    log_info "Regla sudoers creada en $SUDOERS_FILE"
else
    log_info "El archivo sudoers $SUDOERS_FILE ya existe. Se omite la creación."
fi

# ----------------------------------------------------------------------
#                           CONFIGURACIÓN DE IPV6
# ----------------------------------------------------------------------
is_subnet_in_use() {
    local subnet=$1
    if ip -6 addr show | grep -qE "${subnet%/*}"; then
        return 0
    else
        return 1
    fi
}
find_free_subnet() {
    local base_subnet=$1
    for i in $(seq 1 "$MAX_SUBNETS"); do
        SUBNET="${base_subnet}${i}::/64"
        if ! is_subnet_in_use "$SUBNET"; then
            echo "$SUBNET"
            return 0
        fi
    done
    log_error "No hay subredes libres disponibles dentro de ${base_subnet}"
    return 1
}
detect_global_prefix() {
    local prefix=""
    while read -r addr; do
        IFS=':' read -r -a blocks <<< "$addr"
        if [ ${#blocks[@]} -ge 3 ]; then
            prefix="${blocks[0]}:${blocks[1]}:${blocks[2]}:"
            echo "$prefix"
            return 0
        fi
    done < <(ip -6 addr show scope global | grep -oP 'inet6 \K[^/]+(?=/)')
    return 1
}
generate_ula_prefix() {
    local identifier
    identifier=$(openssl rand -hex 5)
    echo "fd${identifier}:"
}
if [[ -f "$FILE" && -s "$FILE" ]]; then
    if ! jq empty "$FILE" 2>/dev/null; then
        log_error "El archivo $FILE contiene JSON inválido. Corrígelo manualmente."
        exit 1
    fi
    ipv6_check=$(jq 'has("ipv6")' "$FILE")
    if [[ "$ipv6_check" == "true" ]]; then
        ipv6_exists=true
        log_info "La clave 'ipv6' ya existe en $FILE."
    fi
    fixed_cidr_check=$(jq 'has("fixed-cidr-v6")' "$FILE")
    if [[ "$fixed_cidr_check" == "true" ]]; then
        fixed_cidr_exists=true
        log_info "La clave 'fixed-cidr-v6' ya existe en $FILE."
    fi
fi
if $ipv6_exists && $fixed_cidr_exists; then
    log_info "La configuración de IPv6 ya existe en $FILE. No se realizarán cambios."
else
    BASE_PREFIX=$(detect_global_prefix)
    if [[ -z "$BASE_PREFIX" ]]; then
        if $GENERATE_ULA; then
            log_info "No se detectó un prefijo IPv6 global. Generando ULA..."
            BASE_PREFIX=$(generate_ula_prefix)
            log_info "Prefijo ULA generado: ${BASE_PREFIX}/48"
        else
            log_error "No se detectó prefijo global y la generación de ULA está deshabilitada."
            exit 1
        fi
    else
        log_info "Prefijo IPv6 global detectado: ${BASE_PREFIX}/48"
    fi
    FREE_SUBNET=$(find_free_subnet "$BASE_PREFIX") || { exit 1; }
    log_info "Subred libre encontrada: $FREE_SUBNET"
    if [[ -f "$FILE" && -s "$FILE" ]]; then
        BACKUP_FILE="${FILE}.bak_$(date +%F_%T)"
        cp "$FILE" "$BACKUP_FILE"
        log_info "Copia de seguridad creada en: $BACKUP_FILE"
    fi
    if [[ -f "$FILE" && -s "$FILE" ]]; then
        if $ipv6_exists; then
            UPDATED_JSON=$(jq --arg cidr "$FREE_SUBNET" '.["fixed-cidr-v6"] = $cidr' "$FILE") || {
                log_error "Error al actualizar 'fixed-cidr-v6' en $FILE."
                exit 1
            }
            log_info "Actualizando 'fixed-cidr-v6' en $FILE."
        else
            UPDATED_JSON=$(jq --arg cidr "$FREE_SUBNET" '. + { "ipv6": true, "fixed-cidr-v6": $cidr }' "$FILE") || {
                log_error "Error al añadir 'ipv6' y 'fixed-cidr-v6' en $FILE."
                exit 1
            }
            log_info "Añadiendo 'ipv6' y 'fixed-cidr-v6' en $FILE."
        fi
    else
        UPDATED_JSON=$(jq -n --arg cidr "$FREE_SUBNET" '{ "ipv6": true, "fixed-cidr-v6": $cidr }') || {
            log_error "Error al crear JSON nuevo en $FILE."
            exit 1
        }
        log_info "Creando archivo JSON en $FILE con 'ipv6' y 'fixed-cidr-v6'."
    fi
    TMP_FILE=$(mktemp)
    echo "$UPDATED_JSON" > "$TMP_FILE"
    if jq empty "$TMP_FILE" 2>/dev/null; then
        mv "$TMP_FILE" "$FILE"
        log_info "Subred asignada: $FREE_SUBNET"
    else
        log_error "JSON temporal inválido. No se realizaron cambios."
        rm -f "$TMP_FILE"
        exit 1
    fi
    if systemctl restart docker; then
        log_info "Docker reiniciado para aplicar cambios."
    else
        log_error "Error al reiniciar Docker. Restaurando backup."
        if [[ -n "$BACKUP_FILE" && -f "$BACKUP_FILE" ]]; then
            cp "$BACKUP_FILE" "$FILE"
            log_info "Backup restaurado desde $BACKUP_FILE."
        fi
        systemctl restart docker || { log_error "No se puede reiniciar Docker. Revisar logs."; exit 1; }
        exit 1
    fi
fi

# ----------------------------------------------------------------------
#                           OPCIONES USUARIO
# ----------------------------------------------------------------------
log_info "¿Desea ignorar los errores a la hora de construirlos? (s/N)"
read user_input_ignore_errors
if [[ -z "$user_input_ignore_errors" ]]; then user_input_ignore_errors="n"; fi
if [[ "$user_input_ignore_errors" =~ [sS] ]]; then
    ignore_errors="s"
else
    ignore_errors="n"
fi
log_info "¿Desea ocultar el output de los comandos ejecutados durante el script? (S/n)"
read user_input_hide_output
if [[ -z "$user_input_hide_output" ]]; then user_input_hide_output="s"; fi
if [[ "$user_input_hide_output" =~ [nN] ]]; then
    hide_output="n"
else
    hide_output="s"
fi

# ----------------------------------------------------------------------
#                   CONSTRUIR/INICIAR CONTENEDORES
# ----------------------------------------------------------------------
is_port_in_use() {
    local port="$1"
    if lsof -i :"$port" -sTCP:LISTEN &>/dev/null; then
        return 0
    else
        return 1
    fi
}
show_port_details() {
    local port="$1"
    log_warn "Proceso(s) que ocupan el puerto $port:"
    lsof -i :"$port" -sTCP:LISTEN
}
kill_process_on_port() {
    local port="$1"
    local pid
    pid="$(lsof -t -i :"$port" -sTCP:LISTEN 2>/dev/null)"
    if [ -n "$pid" ]; then
        log_warn "Matando proceso(s) $pid en puerto $port ..."
        kill -9 "$pid"
        log_info "Proceso $pid eliminado. Continuando."
    else
        log_warn "No se encontró PID ocupando el puerto $port"
    fi
}
# Se modifica run_docker_container para recibir la IP estática como parámetro ($7)
run_docker_container() {
    local container_name="$1"
    local container_dir="$2"
    local container_ports="$3"
    local container_options="$4"
    local hide_output="$5"
    local ignore_errors="$6"
    local static_ip="$7"

    log_info "Iniciando contenedor $container_name con IP estática $static_ip"

    for port_map in $container_ports; do
        local host_port="${port_map%%:*}"
        if is_port_in_use "$host_port"; then
            log_warn "El puerto $host_port ya está en uso."
            show_port_details "$host_port"
            log_warn "¿Deseas matar el proceso que ocupa el puerto $host_port y continuar (k), o saltar este contenedor (s)? [k/s]"
            read -r kill_or_skip
            if [[ "$kill_or_skip" =~ ^[kK]$ ]]; then
                kill_process_on_port "$host_port"
            else
                log_info "SALTANDO contenedor $container_name"
                return 0
            fi
        fi
    done

    port_args=()
    for port in $container_ports; do
        port_args+=("-p" "$port")
    done

    additional_args=()
    if [[ -n "$container_options" ]]; then
        read -r -a additional_args <<< "$container_options"
    fi

    # Se especifica la red y la IP estática
    local -a run_cmd
    run_cmd=(docker run --name "$container_name" --network WebVulnLab-Network --ip "$static_ip" -d
              "${port_args[@]}"
              "${additional_args[@]}"
              -v "$container_dir/src":/var/www/html
              "$container_name")
    if [ "$hide_output" = "s" ]; then
        local run_output
        run_output="$("${run_cmd[@]}" 2>&1)"
        local exit_code=$?
        echo "$run_output" >> "$LOG_FILE"
        if [ $exit_code -ne 0 ]; then
            log_error "Error al iniciar contenedor $container_name. Detalles:"
            echo "$run_output"
            [ "$ignore_errors" = "n" ] && exit 1
        else
            log_info "Contenedor $container_name iniciado correctamente"
        fi
    else
        "${run_cmd[@]}"
        local exit_code=$?
        if [ $exit_code -ne 0 ]; then
            log_error "Error al iniciar contenedor $container_name"
            [ "$ignore_errors" = "n" ] && exit 1
        else
            log_info "Contenedor $container_name iniciado correctamente"
        fi
    fi
}
build_docker_image() {
    local container_name="$1"
    local container_dir="$2"
    local hide_output="$3"
    local ignore_errors="$4"
    log_info "Construyendo imagen de $container_name"
    if [ "$hide_output" = "s" ]; then
        local build_out
        build_out="$(docker build -t "$container_name" "$container_dir" 2>&1)"
        local exit_code=$?
        echo "$build_out" >> "$LOG_FILE"
        if [ $exit_code -ne 0 ]; then
            log_error "Error al construir la imagen de $container_name. Detalles:"
            echo "$build_out"
            [ "$ignore_errors" = "n" ] && exit 1
        else
            log_info "Imagen $container_name construida correctamente"
        fi
    else
        docker build -t "$container_name" "$container_dir"
        if [ $? -ne 0 ]; then
            log_error "Error al construir la imagen de $container_name"
            [ "$ignore_errors" = "n" ] && exit 1
        else
            log_info "Imagen $container_name construida correctamente"
        fi
    fi
}
run_otros() {
    local info="$1"
    local command="$2"
    local hide_output="$3"
    local ignore_errors="$4"
    log_info "$info"
    if [ "$hide_output" = "s" ]; then
        local other_out
        other_out="$(eval "$command" 2>&1)"
        local exit_code=$?
        echo "$other_out" >> "$LOG_FILE"
        if [ $exit_code -ne 0 ]; then
            log_error "$info (falló). Detalles:"
            echo "$other_out"
            [ "$ignore_errors" = "n" ] && exit 1
        else
            log_info "$info (ejecutado correctamente)"
        fi
    else
        eval "$command"
        local exit_code=$?
        if [ $exit_code -ne 0 ]; then
            log_error "$info (falló)"
            [ "$ignore_errors" = "n" ] && exit 1
        else
            log_info "$info (ejecutado correctamente)"
        fi
    fi
}

# ----------------------------------------------------------------------
#                                   MAIN
# ----------------------------------------------------------------------
if [ "$(id -u)" != "0" ]; then
   log_error "El script debe ser ejecutado como root"
   exit 1
fi

# Función para construir el servidor local (p.ej. copiar "tablero")
build_local_server() {
    local log_file="build_server.log"
    log_info "Construyendo Tablero"
    cp -R tablero /var/www/html >> "$log_file" 2>&1
    if [ $? -eq 0 ]; then
        log_info "Tablero construido correctamente"
    else
        log_error "Error durante la construcción del Tablero. Revisar $log_file."
        return 1
    fi
    log_info "Iniciando Tablero (Activando API REST de Docker en puerto 2375)"
    sed -i 's/\-\-containerd=\/run\/containerd\/containerd.sock/\-H=tcp\:\/\/0\.0\.0\.0\:2375/' /lib/systemd/system/docker.service >> "$log_file" 2>&1
    systemctl daemon-reload >> "$log_file" 2>&1
    systemctl restart docker >> "$log_file" 2>&1
    version=$(php -v | sed -nr 's/PHP[[:space:]]+([0-9]+\.[0-9]+).*/\1/p')
    apt-get install php$version-curl -y >> "$log_file" 2>&1
    service apache2 restart >> "$log_file" 2>&1
    if [ $? -eq 0 ]; then
        log_info "API REST de Docker configurada correctamente."
    else
        log_error "Error al configurar el API REST de Docker. Revisar $log_file."
        return 1
    fi
    [ -s "$log_file" ] || rm "$log_file"
}
# --- No se usan funciones de VirtualHost, ya que se usará /etc/hosts ---
build_local_server

# Si se requiere generar certificados para http3_v2 o menu_v2, se comprueba aquí
for container in "${containers[@]}"; do
    IFS=';' read -ra container_info <<< "$container"

    if [ "${container_info[0]}" == "http3_v2" ]; then
        generate_http3_certificates
        break
    elif [ "${container_info[0]}" == "menu_v2" ]; then
        remove_http3_certificates_menu
        #generate_http3_certificates_menu
        #sudo cp /etc/ssl/certs/menu.local.crt     $PWD/menu/src/certs/menu.local.crt
        #sudo cp /etc/ssl/private/menu.local.key   $PWD/menu/src/certs/menu.local.key
        break
    fi
done

configure_network

# --- Bucle principal para cada contenedor ---
for container in "${containers[@]}"; do
    IFS=';' read -ra container_info <<< "$container"
    container_name="${container_info[0]}"
    container_dir="${container_info[1]}"
    container_ports="${container_info[2]}"
    container_options="${container_info[3]}"   # Puede estar vacío
    static_ip="${container_info[4]}"             # Campo obligatorio con la IP hardcodeada
    
    if [ -z "$static_ip" ]; then
        log_error "No se especificó una IP estática para $container_name. Se omite este contenedor."
        continue
    fi

    build_docker_image "$container_name" "$container_dir" "$hide_output" "$ignore_errors"
    run_docker_container "$container_name" "$container_dir" "$container_ports" "$container_options" "$hide_output" "$ignore_errors" "$static_ip"
    
    # Determinar qué dominios añadir en /etc/hosts
    if [[ "$container_name" == "domainzonetransfer_v2" ]]; then
        domain="domainzonetransfer.local codefusiondev.domainzonetransfer.local"
    else
        domain="${container_name%%_v2}.local"
    fi

    # Actualizar /etc/hosts usando la IP hardcodeada y la(s) entrada(s) deseada(s)
    if grep -qE "\b$(echo $domain | awk '{print $1}')\b" /etc/hosts; then
        if grep -qE "^$static_ip\s+$domain" /etc/hosts; then
            log_info "La entrada para $domain ya existe con la IP correcta ($static_ip)."
        else
            log_info "La entrada para $domain existe pero con IP diferente. Actualizando..."
            sed -i.bak "/\b$(echo $domain | awk '{print $1}')\b/ s/^[0-9.]\+/$static_ip/" /etc/hosts
            log_info "Entrada para $domain actualizada a $static_ip."
        fi
    else
        echo "$static_ip $domain" >> /etc/hosts
        log_info "Entrada añadida: $static_ip $domain"
    fi

    # Detener el contenedor para ahorrar recursos
    docker stop "$container_name" >> "$LOG_FILE" 2>&1
done

# --- Ejecutar comandos para "otros" contenedores (si los hubiera) ---
for other in "${otros[@]}"; do
    IFS=';' read -ra otros_info <<< "$other"
    info=${otros_info[0]}
    command=${otros_info[1]}
    run_otros "$info" "$command" "$hide_output" "$ignore_errors"
    docker stop $(docker ps -aq) >> "$LOG_FILE" 2>&1
done

# ----------------------------------------------------------------------
#             FUNCION: Configurar VirtualHost para tablero.local
# ----------------------------------------------------------------------
setup_tablero_vhost() {
    log_info "Configurando VirtualHost para tablero.local..."

    local vhost_file="/etc/apache2/sites-available/tablero.local.conf"
    local vhost_content
    vhost_content=$(cat <<'EOF'
        <VirtualHost *:80>
            ServerName tablero.local
            ProxyPass / http://localhost/tablero/
            ProxyPassReverse / http://localhost/tablero/
        </VirtualHost>
EOF
    )

    # Crear o actualizar el archivo del VirtualHost
    echo "$vhost_content" > "$vhost_file"
    if [ $? -eq 0 ]; then
         log_info "Archivo VirtualHost creado/actualizado en $vhost_file"
    else
         log_error "Error al crear/actualizar el archivo VirtualHost en $vhost_file"
         return 1
    fi

    # Activar el sitio (si no está ya habilitado)
    a2ensite tablero.local.conf >> "$LOG_FILE" 2>&1
    if [ $? -eq 0 ]; then
         log_info "VirtualHost tablero.local habilitado"
    else
         log_error "Error al habilitar el VirtualHost tablero.local (quizá ya estaba habilitado)"
    fi

    # Recargar Apache para aplicar los cambios
    systemctl reload apache2 >> "$LOG_FILE" 2>&1
    if [ $? -eq 0 ]; then
         log_info "Apache recargado correctamente"
    else
         log_error "Error al recargar Apache"
         return 1
    fi

    # Actualizar /etc/hosts para que tablero.local apunte a 127.0.0.1
    if ! grep -qE "^[[:space:]]*127\.0\.0\.1[[:space:]]+tablero\.local" /etc/hosts; then
         echo "127.0.0.1 tablero.local" >> /etc/hosts
         if [ $? -eq 0 ]; then
             log_info "Entrada añadida a /etc/hosts: 127.0.0.1 tablero.local"
         else
             log_error "Error al actualizar /etc/hosts con la entrada tablero.local"
         fi
    else
         log_info "/etc/hosts ya contiene la entrada para tablero.local"
    fi

    return 0
}

setup_tablero_vhost

entradasEtcHosts(){
    host_entries=(
    "172.18.0.46 aws.local"
    "172.18.0.47  ldapinjection.local"
    "172.18.0.44  mail.local"
    "172.18.0.43  apiabuse.local"
    "172.18.0.49  graphql.local"
    "172.18.0.53  oauth_gallery.local"
    "172.18.0.52  oauth_printing.local"
    )

    # Recorre cada entrada del array para añadirla o actualizarla en /etc/hosts.
    for entry in "${host_entries[@]}"; do
        # Se extrae la IP (primer campo) y los dominios (todos los campos siguientes)
        ip=$(echo "$entry" | awk '{print $1}')
        domains=$(echo "$entry" | cut -d' ' -f2-)
        # Utilizamos el primer dominio para buscar la entrada en /etc/hosts
        first_domain=$(echo "$domains" | awk '{print $1}')

        if grep -qE "\b${first_domain}\b" /etc/hosts; then
            # Si ya existe, comprobamos que la línea comience con la IP deseada
            if grep -qE "^${ip}[[:space:]]+${domains}" /etc/hosts; then
                log_info "La entrada para ${domains} ya existe con la IP correcta (${ip})."
            else
                log_info "La entrada para ${domains} existe pero con IP diferente. Actualizando..."
                sed -i.bak "/\b${first_domain}\b/ s/^[0-9.]\+\s\+/${ip} /" /etc/hosts
                log_info "Entrada para ${domains} actualizada a ${ip}."
            fi
        else
            echo "$entry" >> /etc/hosts
            log_info "Entrada añadida: $entry"
        fi
    done
}

entradasEtcHosts

log_info "Script finalizado con éxito." 
exit 0