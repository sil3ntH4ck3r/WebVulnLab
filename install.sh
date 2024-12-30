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
ignore_errors="n"   # Si el usuario no escribe nada, asume n
hide_output="s"     # Si el usuario no escribe nada, asume s

ipv6_exists=false
fixed_cidr_exists=false

# Arrays
containers=(
  "menu_v2;$PWD/menu;8080:80;"
  "lfi_v2;$PWD/lfi;8000:80;"
  "csrf_v2;$PWD/csrf;8001:80;"
  "blindxxe_v2;$PWD/blindxxe;8002:80;"
  "xxe_v2;$PWD/xxe;8003:80;"
  "xss_v2;$PWD/xss;8004:80;"
  "domainzonetransfer_v2;$PWD/domainzonetransfer;8039:80 53:53/tcp 53:53/udp;"
  "ssrf_v2;$PWD/ssrf;8006:80;"
  "typejuggling_v2;$PWD/typejuggling;8008:80;"
  "rfi_v2;$PWD/rfi;8009:80;"
  "insecuredeseralizationphp_v2;$PWD/insecuredeseralizationphp;8010:80;"
  "latexinjection_v2;$PWD/latexinjection;8011:80;"
  "xpathinjection_v2;$PWD/xpathinjection;8012:80;"
  "shellshock_v2;$PWD/shellshock;8013:80;"
  "blindxss_v2;$PWD/blindxss;8015:80;"
  "htmlinjection_v2;$PWD/htmlinjection;8016:80;"
  "ssti_v2;$PWD/ssti;8018:80;"
  "csti_v2;$PWD/csti;8019:80;"
  "nosqlinjection_v2;$PWD/nosqlinjection;8020:80;"
  "ldap_server_v2;$PWD/ldapinjection/ldapserver;389:389;"
  "ldapinjection_v2;$PWD/ldapinjection/webserver;8021:80;"
  "fileuploadabuse_v2;$PWD/fileuploadabuse;8024:80;"
  "prototypepollution_v2;$PWD/prototypepollution;8025:3000;"
  "openredirect_v2;$PWD/openredirect;8026:80;"
  "squidproxy_v2;$PWD/squidproxy;8028:80 3128:3128;--cap-add=NET_ADMIN"
  "cors_v2;$PWD/cors;8029:80;"
  "racecondition_v2;$PWD/racecondition;8033:80;"
  "cssi_v2;$PWD/cssi;8034:80;"
  "yamldeseralization_v2;$PWD/yamldeseralization;8042:5000;"
  "pickledeseralization_v2;$PWD/pickledeseralization;8038:5000;"
  "snmp_v2;$PWD/snmp;8040:80 161:161/udp;--sysctl net.ipv6.conf.all.disable_ipv6=0 --sysctl net.ipv6.conf.default.disable_ipv6=0"
  "http3_v2;$PWD/http3;8043:443/tcp 8043:443/udp 8044:80;"
  "httpsmuggling_v2;$PWD/httpsmuggling;8043:80;"
)

database=(
  "sqli_db_v2;$PWD/sqli;8005:80;sqli_v2"
  "blindsqli_db_v2;$PWD/blindsqli;8014:80;blindsqli_v2"
  "paddingoracleattack_db_v2;$PWD/paddingoracleattack;8007:80;paddingoracleattack_v2"
  "idor_db_v2;$PWD/idor;8017:80;idor_v2"
  "sqltruncation_db_v2;$PWD/sqltruncation;8030:80;sqltruncation_v2"
  "sessionpuzzling_db_v2;$PWD/sessionpuzzling;8031:80;sessionpuzzling_v2"
  "jwt_db_v2;$PWD/jwt;8032:80;jwt_v2"
)

otros=(
  "Construyendo contenedores para AWS Abuse;docker-compose -f $PWD/aws/docker-compose.yml up -d"
  "Construyendo contenedores para API Abuse;docker-compose -f $PWD/apiabuse/docker-compose.yml up -d"
  "Contruyendo contenedores para WebDAV;docker-compose -f $PWD/webdav/docker-compose.yml up -d"
  "Contruyendo contenedores para GraphQL;docker-compose -f $PWD/graphql/docker-compose.yml up -d"
  "Contruyendo contenedores para OAuth;docker-compose -f $PWD/oauth/docker-compose.yml up -d"
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
#                               LDAP Y RED
# ----------------------------------------------------------------------

configure_ldap_files(){

    log_info "Configurando archivos para LDAP Server"
    docker start ldap_server_v2 >> "$LOG_FILE" 2>&1

    ldapadd -x -H ldap://localhost -D "cn=admin,dc=ldapinjection,dc=local" -w admin -f "$PWD/ldapinjection/ldapserver/users.ldif" >> "$LOG_FILE" 2>&1
    if [ $? -ne 0 ]; then
        log_error "Error al configurar users.ldif"
    else
        log_info "Configurado correctamente users.ldif"
    fi

    ldapadd -x -D "cn=admin,dc=ldapinjection,dc=local" -w admin -f "$PWD/ldapinjection/ldapserver/user1.ldif" >> "$LOG_FILE" 2>&1
    if [ $? -ne 0 ]; then
        log_error "Error al configurar user1.ldif"
        docker stop ldap_server_v2 >> "$LOG_FILE" 2>&1
    else
        log_info "Configurado correctamente user1.ldif"
        docker stop ldap_server_v2 >> "$LOG_FILE" 2>&1
    fi
}

configure_network() {
    log_info "Creando la red WebVulnLab-Network"
    docker network create WebVulnLab-Network >> "$LOG_FILE" 2>&1

    # Añadir contenedores a la red
    for container in "${containers[@]}"; do
        container_info=($(echo "$container" | tr ';' ' '))
        docker network connect WebVulnLab-Network "${container_info[0]}" >> "$LOG_FILE" 2>&1
    done

    for db_container in "${database[@]}"; do
        db_container_info=($(echo "$db_container" | tr ';' ' '))
        docker network.connect WebVulnLab-Network "${db_container_info[0]}" >> "$LOG_FILE" 2>&1
    done
}

# ----------------------------------------------------------------------
#                           APACHE Y VHOST
# ----------------------------------------------------------------------

setup_file_virtual_hosting() {
    config_file="WebVulnLab.conf"
    error_file="error.log"

    # Vaciar el archivo de config (o crearlo si no existe)
    > "$config_file"

    # Añadir VirtualHosts fijos
    {
        echo "<VirtualHost *:80>"
        echo "    ServerName tablero.local"
        echo "    ProxyPass / http://localhost/tablero/"
        echo "    ProxyPassReverse / http://localhost/tablero/"
        echo "</VirtualHost>"
        echo
        echo "<VirtualHost *:80>"
        echo "    ServerName apiabuse.local"
        echo "    ProxyPass / http://localhost:8022/"
        echo "    ProxyPassReverse / http://localhost:8022/"
        echo "</VirtualHost>"
        echo
        echo "<VirtualHost *:80>"
        echo "    ServerName mail.local"
        echo "    ProxyPass / http://localhost:8023/"
        echo "    ProxyPassReverse / http://localhost:8023/"
        echo "</VirtualHost>"
        echo
        echo "<VirtualHost *:80>"
        echo "    ServerName webdav.local"
        echo "    ProxyPass / http://localhost:8027/"
        echo "    ProxyPassReverse / http://localhost:8027/"
        echo "</VirtualHost>"
        echo
        echo "<VirtualHost *:80>"
        echo "    ServerName graphql.local"
        echo "    ProxyPass / http://localhost:8035/"
        echo "    ProxyPassReverse / http://localhost:8035/"
        echo "</VirtualHost>"
        echo
        echo "<VirtualHost *:80>"
        echo "    ServerName oauth_gallery.local"
        echo "    ProxyPass / http://localhost:8037/"
        echo "    ProxyPassReverse / http://localhost:8037/"
        echo "</VirtualHost>"
        echo
        echo "<VirtualHost *:80>"
        echo "    ServerName oauth_printing.local"
        echo "    ProxyPass / http://localhost:8036/"
        echo "    ProxyPassReverse / http://localhost:8036/"
        echo "</VirtualHost>"
        echo
        echo "<VirtualHost *:80>"
        echo "    ServerName codefusiondev.domainzonetransfer.local"
        echo "    ProxyPreserveHost On"
        echo "    ProxyPass / http://localhost:8039/"
        echo "    ProxyPassReverse / http://localhost:8039/"
        echo "</VirtualHost>"
        echo
        echo "<VirtualHost *:80>"
        echo "    ServerName aws.local"
        echo "    ProxyPreserveHost On"
        echo "    ProxyPass / http://localhost:8041/"
        echo "    ProxyPassReverse / http://localhost:8041/"
        echo "</VirtualHost>"
        echo
    } >> "$config_file" 2>> "$error_file"

    # Añadir entradas de VirtualHost para contenedores
    for container in "${containers[@]}"; do
        container_info=($(echo "$container" | tr ';' ' '))
        container_name=${container_info[0]%%_v2}
        container_ports=${container_info[2]}
        container_port=$(echo "$container_ports" | cut -d ':' -f 1)

        # Si es http3, saltar la generación de VirtualHost y continuar con el siguiente contenedor
        if [[ "$container_name" == "http3" ]]; then
            log_info "Saltando entrada de VirtualHost para el contenedor http3_v2"
            continue
        fi

        {
            echo "<VirtualHost *:80>"
            echo "    ServerName $container_name.local"
            echo "    ProxyPass / http://localhost:$container_port/"
            echo "    ProxyPassReverse / http://localhost:$container_port/"
            echo "</VirtualHost>"
            echo
        } >> "$config_file" 2>> "$error_file"
    done

    # Añadir las entradas de VirtualHost para contenedores con base de datos
    for db_container in "${database[@]}"; do
        db_container_info=($(echo "$db_container" | tr ';' ' '))
        db_container_name=${db_container_info[0]%%_db_v2}
        db_container_ports=${db_container_info[2]}
        db_container_port=$(echo "$db_container_ports" | cut -d ':' -f 1)

        {
            echo "<VirtualHost *:80>"
            echo "    ServerName $db_container_name.local"
            echo "    ProxyPass / http://localhost:$db_container_port/"
            echo "    ProxyPassReverse / http://localhost:$db_container_port/"
            echo "</VirtualHost>"
            echo
        } >> "$config_file" 2>> "$error_file"
    done

    log_info "Archivo Apache VirtualHost (WebVulnLab.conf) generado."

    if [ -s "$error_file" ]; then
        log_error "Se encontraron errores generando el archivo de virtualhost. Revisar $error_file."
    else
        log_info "Archivo WebVulnLab.conf generado correctamente."
        [ -s "$error_file" ] || rm "$error_file"
    fi
}

build_local_server() {
    local log_file="build_server.log"

    log_info "Construyendo Tablero"
    sudo cp -R tablero /var/www/html >> "$log_file" 2>&1
    if [ $? -eq 0 ]; then
        log_info "Tablero construido correctamente"
    else
        log_error "Error durante la construcción del Tablero. Revisar $log_file."
        return 1
    fi

    log_info "Iniciando Tablero (Activando API REST de Docker en puerto 2375)"
    sudo sed -i 's/\-\-containerd=\/run\/containerd\/containerd.sock/\-H=tcp\:\/\/0\.0\.0\.0\:2375/' /lib/systemd/system/docker.service >> "$log_file" 2>&1
    sudo systemctl daemon-reload >> "$log_file" 2>&1
    sudo systemctl restart docker >> "$log_file" 2>&1

    version=$(php -v | sed -nr 's/PHP[[:space:]]+([0-9]+\.[0-9]+).*/\1/p')
    sudo apt-get install php$version-curl -y >> "$log_file" 2>&1
    sudo service apache2 restart >> "$log_file" 2>&1

    if [ $? -eq 0 ]; then
        log_info "API REST de Docker configurada correctamente."
    else
        log_error "Error al configurar el API REST de Docker. Revisar $log_file."
        return 1
    fi

    [ -s "$log_file" ] || rm "$log_file"
}

configure_virtual_host() {
    local log_file="configure_virtual_host.log"

    log_info "Habilitando módulos de proxy en Apache"
    sudo a2enmod proxy proxy_http >> "$log_file" 2>&1
    if [ $? -ne 0 ]; then
        log_error "Error al habilitar módulos de proxy. Revisar $log_file."
        return 1
    fi

    log_info "Copiando WebVulnLab.conf a /etc/apache2/sites-available"
    sudo cp WebVulnLab.conf /etc/apache2/sites-available >> "$log_file" 2>&1
    if [ $? -ne 0 ]; then
        log_error "Error al copiar archivo de configuración. Revisar $log_file."
        return 1
    fi

    log_info "Habilitando VirtualHost WebVulnLab.conf"
    sudo a2ensite WebVulnLab.conf >> "$log_file" 2>&1
    if [ $? -ne 0 ]; then
        log_error "Error al habilitar sitio virtual. Revisar $log_file."
        return 1
    fi

    log_info "Recargando Apache"
    sudo systemctl reload apache2 >> "$log_file" 2>&1
    if [ $? -ne 0 ]; then
        log_error "Error al recargar Apache. Revisar $log_file."
        return 1
    fi

    # Añadir entradas /etc/hosts
    hosts_entries=()
    for container in "${containers[@]}"; do
        container_info=($(echo "$container" | tr ';' ' '))
        container_name=${container_info[0]%%_v2}
        hosts_entries+=("$container_name.local")
    done
    for db_container in "${database[@]}"; do
        db_container_info=($(echo "$db_container" | tr ';' ' '))
        db_container_name=${db_container_info[0]%%_db_v2}
        hosts_entries+=("$db_container_name.local")
    done

    echo "127.0.0.1 ${hosts_entries[*]} tablero.local aws.local codefusiondev.domainzonetransfer.local apiabuse.local mail.local webdav.local graphql.local oauth_printing.local oauth_gallery.local" >> /etc/hosts

    if [ $? -ne 0 ]; then
        log_error "Error al modificar /etc/hosts. Revisar $log_file."
        return 1
    fi

    log_info "Configuración de Virtual Host completada correctamente."
    [ -s "$log_file" ] || rm "$log_file"
}

# ----------------------------------------------------------------------
#                               LOGGING
# ----------------------------------------------------------------------
log_info() {
    # Muestra el mensaje en pantalla (con colores) y lo guarda en el log
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

# Guardar la configuración original de stty
original_stty=$(stty -g)

# Desactivar la eco de caracteres de control para ocultar ^C
stty -echoctl

cleanup_exit() {
    if [[ -n "$TMP_FILE" && -f "$TMP_FILE" ]]; then
        rm -f "$TMP_FILE"
        log_info "Archivo temporal $TMP_FILE eliminado."
    fi
    # Restaurar configuración original de stty
    stty "$original_stty"
}

cleanup_signal() {
    log_warn "Saliendo del programa..."
    cleanup_exit
    exit 1
}

trap cleanup_exit EXIT
trap cleanup_signal INT TERM

# ----------------------------------------------------------------------
#                           DEPENDENCIAS
# ----------------------------------------------------------------------

# Verificar si el script se está ejecutando como root
if [[ "$EUID" -ne 0 ]]; then
    log_error "Este script debe ejecutarse con privilegios de superusuario (sudo)."
    exit 1
fi

# Definir una lista de comandos y sus paquetes asociados
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

# Lista de comandos requeridos
REQUIRED_COMMANDS=(
    "jq"
    "openssl"
    "docker"
    "docker-compose"
    "php"
    "apache2"
    "ldapadd"
    "ip"
    "stty"
    "sed"
    "systemctl"
    "a2enmod"
    "a2ensite"
)

# Lista de paquetes a instalar
INSTALL_PACKAGES=()

# Verificar comandos faltantes y mapear a paquetes
for cmd in "${REQUIRED_COMMANDS[@]}"; do
    if ! command -v "$cmd" &> /dev/null; then
        pkg=${CMD_TO_PKG[$cmd]}
        if [[ -n "$pkg" && ! " ${INSTALL_PACKAGES[@]} " =~ " ${pkg} " ]]; then
            INSTALL_PACKAGES+=("$pkg")
        fi
    fi
done

# Función para instalar Docker desde el repositorio oficial
install_docker_official() {
    log_info "Instalando Docker desde el repositorio oficial..."
    
    # Instalar paquetes necesarios para permitir a apt usar repositorios sobre HTTPS
    apt-get install -y apt-transport-https ca-certificates curl gnupg lsb-release

    # Añadir la clave GPG oficial de Docker
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg | gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg

    # Añadir el repositorio estable de Docker
    echo \
      "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu \
      $(lsb_release -cs) stable" | tee /etc/apt/sources.list.d/docker.list > /dev/null

    # Instalar Docker Engine
    apt-get update && apt-get install -y docker-ce docker-ce-cli containerd.io

    if ! command -v docker &> /dev/null; then
        log_error "Error al instalar Docker desde el repositorio oficial."
        exit 1
    fi

    log_info "Docker instalado correctamente."
}

# Función para instalar docker-compose manualmente
install_docker_compose() {
    log_info "Instalando docker-compose manualmente..."
    # Obtener la última versión de docker-compose
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

# Si docker está faltando, instalar desde el repositorio oficial
if ! command -v docker &> /dev/null; then
    install_docker_official
fi

# Si docker-compose está faltando, instalar manualmente
if ! command -v docker-compose &> /dev/null; then
    install_docker_compose
fi

# Si hay paquetes que instalar, instálalos
if [ ${#INSTALL_PACKAGES[@]} -gt 0 ]; then
    log_info "Instalando dependencias necesarias: ${INSTALL_PACKAGES[*]}"
    apt-get update && apt-get install -y "${INSTALL_PACKAGES[@]}" >> "$LOG_FILE" 2>&1
    if [ $? -ne 0 ]; then
        log_error "Error al instalar las dependencias: ${INSTALL_PACKAGES[*]}"
        exit 1
    fi
    log_info "Dependencias instaladas correctamente."
fi

# Verificar si Docker está activo y en ejecución
if ! systemctl is-active --quiet docker; then
    log_info "Iniciando el servicio de Docker..."
    systemctl start docker
    if ! systemctl is-active --quiet docker; then
        log_error "Error al iniciar el servicio de Docker."
        exit 1
    fi
    log_info "Servicio de Docker iniciado correctamente."
fi

# ----------------------------------------------------------------------
#                           CONFIGURACIÓN DE IPV6 
# ----------------------------------------------------------------------

is_subnet_in_use() {
    local subnet=$1
    if ip -6 addr show | grep -qE "${subnet%/*}"; then
        return 0  # En uso
    else
        return 1  # Libre
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

    FREE_SUBNET=$(find_free_subnet "$BASE_PREFIX") || {
        exit 1
    }
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
        UPDATED_JSON=$(jq -n --arg cidr "$FREE_SUBNET" '
            {
                "ipv6": true,
                "fixed-cidr-v6": $cidr
            }
        ') || {
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
        systemctl restart docker || {
            log_error "No se puede reiniciar Docker. Revisar logs."
            exit 1
        }
        exit 1
    fi
fi

# ----------------------------------------------------------------------
#                           OPCIONES USUARIO
# ----------------------------------------------------------------------
log_info "¿Desea ignorar los errores a la hora de construirlos? (s/N)"
read user_input_ignore_errors
# Si el usuario presiona Enter, se asume N
if [[ -z "$user_input_ignore_errors" ]]; then
    user_input_ignore_errors="n"
fi
if [[ "$user_input_ignore_errors" =~ [sS] ]]; then
    ignore_errors="s"
else
    ignore_errors="n"
fi

log_info "¿Desea ocultar el output de los comandos ejecutados durante el script? (S/n)"
read user_input_hide_output
# Si el usuario presiona Enter, se asume S
if [[ -z "$user_input_hide_output" ]]; then
    user_input_hide_output="s"
fi
if [[ "$user_input_hide_output" =~ [nN] ]]; then
    hide_output="n"
else
    hide_output="s"
fi

# ----------------------------------------------------------------------
#                   CONSTRUIR/INICIAR CONTENEDORES
# ----------------------------------------------------------------------

# Comprueba si un puerto está en uso (ejemplo con lsof)
is_port_in_use() {
    local port="$1"
    if lsof -i :"$port" -sTCP:LISTEN &>/dev/null; then
        return 0  # Sí está en uso
    else
        return 1  # No está en uso
    fi
}

# Mostrar propiedades del proceso
show_port_details() {
    local port="$1"
    log_warn "Proceso(s) que ocupan el puerto $port:"
    # Muestra el listado con detalles
    lsof -i :"$port" -sTCP:LISTEN
}

# Mata el proceso que está usando un puerto
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

build_docker_image() {
    local container_name="$1"
    local container_dir="$2"
    local hide_output="$3"
    local ignore_errors="$4"

    log_info "Construyendo imagen de $container_name"

    if [ "$hide_output" = "s" ]; then
        # CAPTURAMOS la salida de docker build
        local build_out
        build_out="$(docker build -t "$container_name" "$container_dir" 2>&1)"
        local exit_code=$?
        # La guardamos en el log
        echo "$build_out" >> "$LOG_FILE"

        # Verificamos si hubo error
        if [ $exit_code -ne 0 ]; then
            # Si hay error, lo mostramos en pantalla, aunque sea "silencioso"
            log_error "Error al construir la imagen de $container_name. Detalles:"
            echo "$build_out"

            # Si ignore_errors = "n", detenemos el script
            [ "$ignore_errors" = "n" ] && exit 1
        else
            log_info "Imagen $container_name construida correctamente"
        fi
    else
        # Modo no silencioso
        docker build -t "$container_name" "$container_dir"
        if [ $? -ne 0 ]; then
            log_error "Error al construir la imagen de $container_name"
            [ "$ignore_errors" = "n" ] && exit 1
        else
            log_info "Imagen $container_name construida correctamente"
        fi
    fi
}

run_docker_container() {
    local container_name="$1"
    local container_dir="$2"
    local container_ports="$3"
    local container_options="$4"
    local hide_output="$5"
    local ignore_errors="$6"

    log_info "Iniciando contenedor $container_name"

    # 1) Verificar puertos ocupados
    for port_map in $container_ports; do
        local host_port="${port_map%%:*}"
        if is_port_in_use "$host_port"; then
            log_warn "El puerto $host_port ya está en uso."
            show_port_details "$host_port"  # lsof del proceso

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

    # 2) Argumentos de puertos
    port_args=()
    for port in $container_ports; do
        port_args+=("-p" "$port")
    done

    # 3) Argumentos adicionales
    additional_args=()
    if [[ -n "$container_options" ]]; then
        read -r -a additional_args <<< "$container_options"
    fi

    # 4) Comando docker run
    local -a run_cmd
    run_cmd=(docker run --name "$container_name" -d
              "${port_args[@]}"
              "${additional_args[@]}"
              -v "$container_dir/src":/var/www/html
              "$container_name")

    # 5) Ejecutar en modo silencioso o normal
    if [ "$hide_output" = "s" ]; then
        # CAPTURAMOS la salida de docker run
        local run_output
        run_output="$("${run_cmd[@]}" 2>&1)"
        local exit_code=$?

        # La guardamos en el log
        echo "$run_output" >> "$LOG_FILE"

        # Si hay error, se imprime en pantalla
        if [ $exit_code -ne 0 ]; then
            log_error "Error al iniciar contenedor $container_name. Detalles:"
            echo "$run_output"
            [ "$ignore_errors" = "n" ] && exit 1
        else
            log_info "Contenedor $container_name iniciado correctamente"
        fi
    else
        # Modo no silencioso
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

run_docker_db() {
    local database_name="$1"
    local container_dir="$2"
    local container_ports="$3"
    local container_name="$4"
    local hide_output="$5"
    local ignore_errors="$6"

    # 1) Verificar puertos ocupados
    for port_map in $container_ports; do
        local host_port="${port_map%%:*}"
        if is_port_in_use "$host_port"; then
            log_warn "El puerto $host_port está en uso."
            show_port_details "$host_port"

            echo "¿Deseas matar el proceso que ocupa el puerto $host_port y continuar (k), o saltar este contenedor (s)? [k/s]"
            read -r kill_or_skip
            if [[ "$kill_or_skip" =~ ^[kK]$ ]]; then
                kill_process_on_port "$host_port"
            else
                log_info "SALTANDO contenedor $container_name"
                return 0
            fi
        fi
    done

    # 2) Construir imagen
    log_info "Construyendo imagen para contenedor con BD: $container_name"
    if [ "$hide_output" = "s" ]; then
        local build_out
        build_out="$(docker build -t "$container_name" "$container_dir" 2>&1)"
        local exit_code=$?

        echo "$build_out" >> "$LOG_FILE"

        if [ $exit_code -ne 0 ]; then
            log_error "Error al construir la imagen $container_name. Detalles:"
            echo "$build_out"
            [ "$ignore_errors" = "n" ] && exit 1
        else
            log_info "Imagen $container_name construida correctamente"
        fi
    else
        docker build -t "$container_name" "$container_dir"
        local exit_code=$?

        if [ $exit_code -ne 0 ]; then
            log_error "Error al construir la imagen $container_name"
            [ "$ignore_errors" = "n" ] && exit 1
        else
            log_info "Imagen $container_name construida correctamente"
        fi
    fi

    # 3) Iniciar contenedor de DB
    log_info "Iniciando contenedor de base de datos $database_name"
    if [ "$hide_output" = "s" ]; then
        local db_out
        db_out="$(docker run --name "$database_name" \
            -e MYSQL_ROOT_PASSWORD=rootpassword \
            -e MYSQL_DATABASE=database \
            -e MYSQL_USER=usuario \
            -e MYSQL_PASSWORD=contraseña \
            -d mysql:5.7 2>&1)"
        local exit_code=$?

        echo "$db_out" >> "$LOG_FILE"

        if [ $exit_code -ne 0 ]; then
            log_error "Error al iniciar contenedor DB $database_name. Detalles:"
            echo "$db_out"
            [ "$ignore_errors" = "n" ] && exit 1
        else
            log_info "Contenedor DB $database_name iniciado correctamente"
        fi
    else
        docker run --name "$database_name" \
            -e MYSQL_ROOT_PASSWORD=rootpassword \
            -e MYSQL_DATABASE=database \
            -e MYSQL_USER=usuario \
            -e MYSQL_PASSWORD=contraseña \
            -d mysql:5.7
        local exit_code=$? 

        if [ $exit_code -ne 0 ]; then
            log_error "Error al iniciar contenedor DB $database_name"
            [ "$ignore_errors" = "n" ] && exit 1
        else
            log_info "Contenedor DB $database_name iniciado correctamente"
        fi
    fi

    # 4) Iniciar contenedor de aplicación (link a DB)
    log_info "Iniciando contenedor de aplicación $container_name (link a DB)"
    if [ "$hide_output" = "s" ]; then
        local app_out
        app_out="$(docker run --name "$container_name" \
            --network WebVulnLab-Network \
            --link "$database_name":db \
            -p "$container_ports" \
            -v "$container_dir/src":/var/www/html/ \
            -d "$container_name" 2>&1)"
        local exit_code=$?

        echo "$app_out" >> "$LOG_FILE"

        if [ $exit_code -ne 0 ]; then
            log_error "Error al iniciar contenedor $container_name. Detalles:"
            echo "$app_out"
            [ "$ignore_errors" = "n" ] && exit 1
        else
            log_info "Contenedor $container_name iniciado correctamente"
        fi
    else
        docker run --name "$container_name" \
            --network WebVulnLab-Network \
            --link "$database_name":db \
            -p "$container_ports" \
            -v "$container_dir/src":/var/www/html/ \
            -d "$container_name"
        local exit_code=$? 

        if [ $exit_code -ne 0 ]; then
            log_error "Error al iniciar contenedor $container_name"
            [ "$ignore_errors" = "n" ] && exit 1
        else
            log_info "Contenedor $container_name iniciado correctamente"
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

# Configuración de servidor local y virtual host
build_local_server
setup_file_virtual_hosting
configure_virtual_host

configure_network

# Ejemplo de contenedores
for container in "${containers[@]}"; do
    IFS=';' read -ra container_info <<< "$container"
    container_name=${container_info[0]}
    container_dir=${container_info[1]}
    container_ports=${container_info[2]}
    container_options=${container_info[3]}  # <--- Aquí capturamos un cuarto campo si existe

    if [ "$container_name" == "ldap_server_v2" ]; then
        configure_ldap_files
    fi

    build_docker_image "$container_name" "$container_dir" "$hide_output" "$ignore_errors"
    run_docker_container "$container_name" "$container_dir" "$container_ports" "$container_options" "$hide_output" "$ignore_errors"

    # Esto detiene todos los contenedores inmediatamente después de iniciarlos.
    docker stop $(docker ps -aq) >> "$LOG_FILE" 2>&1
done

for db in "${database[@]}"; do
    IFS=';' read -ra db_info <<< "$db"
    database_name=${db_info[0]}
    container_dir=${db_info[1]}
    container_ports=${db_info[2]}
    container_name=${db_info[3]}

    run_docker_db "$database_name" "$container_dir" "$container_ports" "$container_name" "$hide_output" "$ignore_errors"
    docker stop $(docker ps -aq) >> "$LOG_FILE" 2>&1
done

for other in "${otros[@]}"; do
    IFS=';' read -ra otros_info <<< "$other"
    info=${otros_info[0]}
    command=${otros_info[1]}

    run_otros "$info" "$command" "$hide_output" "$ignore_errors"
    docker stop $(docker ps -aq) >> "$LOG_FILE" 2>&1
done

log_info "Script finalizado con éxito." 
exit 0