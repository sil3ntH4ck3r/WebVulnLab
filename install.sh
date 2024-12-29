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
ignore_errors="n"
hide_output="s"

# Arrays
containers=(
  "menu_v2;$PWD/menu;8080:80"
  "lfi_v2;$PWD/lfi;8000:80"
  "csrf_v2;$PWD/csrf;8001:80"
  "blindxxe_v2;$PWD/blindxxe;8002:80"
  "xxe_v2;$PWD/xxe;8003:80"
  "xss_v2;$PWD/xss;8004:80"
  "domainzonetransfer_v2;$PWD/domainzonetransfer;8039:80 -p 53:53/tcp -p 53:53/udp"
  "ssrf_v2;$PWD/ssrf;8006:80"
  "typejuggling_v2;$PWD/typejuggling;8008:80"
  "rfi_v2;$PWD/rfi;8009:80"
  "insecuredeseralizationphp_v2;$PWD/insecuredeseralizationphp;8010:80"
  "latexinjection_v2;$PWD/latexinjection;8011:80"
  "xpathinjection_v2;$PWD/xpathinjection;8012:80"
  "shellshock_v2;$PWD/shellshock;8013:80"
  "blindxss_v2;$PWD/blindxss;8015:80"
  "htmlinjection_v2;$PWD/htmlinjection;8016:80"
  "ssti_v2;$PWD/ssti;8018:80"
  "csti_v2;$PWD/csti;8019:80"
  "nosqlinjection_v2;$PWD/nosqlinjection;8020:80"
  "ldap_server_v2;$PWD/ldapinjection/ldapserver;389:389"
  "ldapinjection_v2;$PWD/ldapinjection/webserver;8021:80"
  "fileuploadabuse_v2;$PWD/fileuploadabuse;8024:80"
  "prototypepollution_v2;$PWD/prototypepollution;8025:3000"
  "openredirect_v2;$PWD/openredirect;8026:80"
  "squidproxy_v2;$PWD/squidproxy;8028:80 -p 3128:3128 --cap-add=NET_ADMIN"
  "cors_v2;$PWD/cors;8029:80"
  "racecondition_v2;$PWD/racecondition;8033:80"
  "cssi_v2;$PWD/cssi;8034:80"
  "yamldeseralization_v2;$PWD/yamldeseralization;8042:5000"
  "pickledeseralization_v2;$PWD/pickledeseralization;8038:5000"
  "snmp_v2;$PWD/snmp;8040:80 -p 161:161/udp --sysctl net.ipv6.conf.all.disable_ipv6=0 --sysctl net.ipv6.conf.default.disable_ipv6=0 "
  "http3_v2;$PWD/http3;8043:443"
  "httpsmuggling_v2;$PWD/httpsmuggling;8043:80"
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
  "Configurando archivos para LDAP;configure_ldap_files"
  "Configurando red para los contenedores;configure_network"
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
echo "                              Created by sil3nth4ck3r"

# ----------------------------------------------------------------------
#                               LDAP Y RED
# ----------------------------------------------------------------------

configure_ldap_files(){
    for container in "${containers[@]}"; do
        IFS=';' read -ra container_info <<< "$container"
        container_name=${container_info[0]}
        container_dir=${container_info[1]}
        container_ports=${container_info[2]}

        if [ "$container_name" == "ldap_server_v2" ]; then
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
        fi
    done
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

    # Suponiendo que uses un archivo de errores en configure_network
    # if [ -s "$error_file" ]; then
    #     log_error "Se encontraron errores. Revise el archivo $error_file."
    # else
    #     log_info "WebVulnLab-Network configurada correctamente."
    # fi
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
#                           CONFIGURACIÓN DE IPV6 
# ----------------------------------------------------------------------

cleanup() {
    if [[ -n "$TMP_FILE" && -f "$TMP_FILE" ]]; then
        rm -f "$TMP_FILE"
        log_info "Archivo temporal $TMP_FILE eliminado."
    fi
}
trap cleanup EXIT INT TERM

if [[ "$EUID" -ne 0 ]]; then
    log_error "Este script debe ejecutarse con privilegios de superusuario (sudo)."
    exit 1
fi

if ! command -v jq &> /dev/null; then
    log_info "Instalando jq..."
    apt-get update && apt-get install -y jq >> "$LOG_FILE" 2>&1
    log_info "jq instalado correctamente."
fi

if ! command -v openssl &> /dev/null; then
    log_info "Instalando openssl..."
    apt-get update && apt-get install -y openssl >> "$LOG_FILE" 2>&1
    log_info "openssl instalado correctamente."
fi

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

ipv6_exists=false
fixed_cidr_exists=false

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

log_info "¿Desea ocultar el output de los comandos ejecutados durante el script? (S/n)"
read user_input_hide_output

if [[ "$user_input_ignore_errors" =~ [sS] ]]; then
    ignore_errors="s"
elif [[ "$user_input_ignore_errors" =~ [nN] ]]; then
    ignore_errors="n"
fi

if [[ "$user_input_hide_output" =~ [sS] ]]; then
    hide_output="s"
elif [[ "$user_input_hide_output" =~ [nN] ]]; then
    hide_output="n"
fi

# ----------------------------------------------------------------------
#                   CONSTRUIR/INICIAR CONTENEDORES
# ----------------------------------------------------------------------

build_docker_image() {
    local container_name="$1"
    local container_dir="$2"
    local hide_output="$3"
    local ignore_errors="$4"

    log_info "Construyendo imagen de $container_name"
    if [ "$hide_output" = "s" ]; then
        docker build -t "$container_name" "$container_dir" >> "$LOG_FILE" 2>&1
    else
        docker build -t "$container_name" "$container_dir"
    fi

    if [ $? -ne 0 ]; then
        log_error "Error al construir la imagen de $container_name"
        [ "$ignore_errors" = "n" ] && exit 1
    else
        log_info "Imagen $container_name construida correctamente"
    fi
}

run_docker_container() {
    local container_name="$1"
    local container_dir="$2"
    local container_ports="$3"
    local hide_output="$4"
    local ignore_errors="$5"

    log_info "Iniciando contenedor $container_name"
    if [ "$container_name" == "ldap_server_v2" ]; then
        if [ "$hide_output" = "s" ]; then
            docker run --name "$container_name" -d -p "$container_ports" "$container_name" >> "$LOG_FILE" 2>&1
        else
            docker run --name "$container_name" -d -p "$container_ports" "$container_name"
        fi
    else
        if [ "$hide_output" = "s" ]; then
            docker run --name "$container_name" -d -v "$container_dir/src":/var/www/html -p "$container_ports" "$container_name" >> "$LOG_FILE" 2>&1
        else
            docker run --name "$container_name" -d -v "$container_dir/src":/var/www/html -p "$container_ports" "$container_name"
        fi
    fi

    if [ $? -ne 0 ]; then
        log_error "Error al iniciar contenedor $container_name"
        [ "$ignore_errors" = "n" ] && exit 1
    else
        log_info "Contenedor $container_name iniciado correctamente"
    fi
}

run_docker_db() {
    local database_name="$1"
    local container_dir="$2"
    local container_ports="$3"
    local container_name="$4"
    local hide_output="$5"
    local ignore_errors="$6"

    log_info "Construyendo imagen para contenedor con BD: $container_name"
    if [ "$hide_output" = "s" ]; then
        docker build -t "$container_name" "$container_dir" >> "$LOG_FILE" 2>&1
    else
        docker build -t "$container_name" "$container_dir"
    fi

    if [ $? -ne 0 ]; then
        log_error "Error al construir la imagen $container_name"
        [ "$ignore_errors" = "n" ] && exit 1
    else
        log_info "Imagen $container_name construida correctamente"
    fi

    log_info "Iniciando contenedor de base de datos $database_name"
    if [ "$hide_output" = "s" ]; then
        docker run --name "$database_name" -e MYSQL_ROOT_PASSWORD=rootpassword -e MYSQL_DATABASE=database -e MYSQL_USER=usuario -e MYSQL_PASSWORD=contraseña -d mysql:5.7 >> "$LOG_FILE" 2>&1
    else
        docker run --name "$database_name" -e MYSQL_ROOT_PASSWORD=rootpassword -e MYSQL_DATABASE=database -e MYSQL_USER=usuario -e MYSQL_PASSWORD=contraseña -d mysql:5.7
    fi

    if [ $? -ne 0 ]; then
        log_error "Error al iniciar contenedor DB $database_name"
        [ "$ignore_errors" = "n" ] && exit 1
    else
        log_info "Contenedor DB $database_name iniciado correctamente"
    fi

    log_info "Iniciando contenedor de aplicación $container_name (link a DB)"
    if [ "$hide_output" = "s" ]; then
        docker run --name "$container_name" --link "$database_name":db -p "$container_ports" -v "$container_dir/src":/var/www/html/ -d "$container_name" >> "$LOG_FILE" 2>&1
    else
        docker run --name "$container_name" --link "$database_name":db -p "$container_ports" -v "$container_dir/src":/var/www/html/ -d "$container_name"
    fi

    if [ $? -ne 0 ]; then
        log_error "Error al iniciar contenedor $container_name"
        [ "$ignore_errors" = "n" ] && exit 1
    else
        log_info "Contenedor $container_name iniciado correctamente"
    fi
}

run_otros() {
    local info="$1"
    local command="$2"
    local hide_output="$3"
    local ignore_errors="$4"

    log_info "$info"
    if [ "$hide_output" = "s" ]; then
        eval "$command >> \"$LOG_FILE\" 2>&1"
    else
        eval "$command"
    fi

    if [ $? -ne 0 ]; then
        log_error "$info (falló)"
        [ "$ignore_errors" = "n" ] && exit 1
    else
        log_info "$info (ejecutado correctamente)"
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

# Ejemplo de contenedores
for container in "${containers[@]}"; do
    IFS=';' read -ra container_info <<< "$container"
    container_name=${container_info[0]}
    container_dir=${container_info[1]}
    container_ports=${container_info[2]}

    build_docker_image "$container_name" "$container_dir" "$hide_output" "$ignore_errors"
    run_docker_container "$container_name" "$container_dir" "$container_ports" "$hide_output" "$ignore_errors"
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
