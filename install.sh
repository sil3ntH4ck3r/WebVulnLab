#!/bin/bash

# Variables
    # Establecer valor predeterminado para ignore_errors
ignore_errors="n"
hide_output="s"

# Arrays

containers=(
  "menu_v2;$PWD/menu;8080:80" # Contenedor para el menú
  "lfi_v2;$PWD/lfi;8000:80" # Contenedor para LFI
  "csrf_v2;$PWD/csrf;8001:80" # Contenedor para CSRF
  "blindxxe_v2;$PWD/blindxxe;8002:80" # Contenedor para Blind XXE
  "xxe_v2;$PWD/xxe;8003:80" # Contenedor para XXE
  "xss_v2;$PWD/xss;8004:80" # Contenedor para XSS
  "domainzonetransfer_v2;$PWD/domainzonetransfer;8039:80 -p 53:53/tcp -p 53:53/udp" # Contenedor para Domain Zone Transfer
  "ssrf_v2;$PWD/ssrf;8006:80" # Contenedor para SSRF
  "typejuggling_v2;$PWD/typejuggling;8008:80" # Contenedor para Type Juggling
  "rfi_v2;$PWD/rfi;8009:80" # Contenedor para RFI
  "insecuredeseralizationphp_v2;$PWD/insecuredeseralizationphp;8010:80" # Contenedor para Insecure Deserialization en PHP
  "latexinjection_v2;$PWD/latexinjection;8011:80" # Contenedor para LaTeX Injection
  "xpathinjection_v2;$PWD/xpathinjection;8012:80" # Contenedor para xPath Injection
  "shellshock_v2;$PWD/shellshock;8013:80" # Contenedor para ShellShock
  "blindxss_v2;$PWD/blindxss;8015:80" # Contenedor para Blind XSS
  "htmlinjection_v2;$PWD/htmlinjection;8016:80" # Contenedor para HTML Injection
  "ssti_v2;$PWD/ssti;8018:80"  # Contenedor para SSTI
  "csti_v2;$PWD/csti;8019:80" # Contenedor para CSTI
  "nosqlinjection_v2;$PWD/nosqlinjection;8020:80" # Contenedor para NoSQL Injection
  "ldap_server_v2;$PWD/ldapinjection/ldapserver;389:389" # Contenedor LDAP Server
  "ldapinjection_v2;$PWD/ldapinjection/webserver;8021:80" # Contenedor LDAP Injection (Necesario iniciar también: LDAP Server y Configurar archivos LDAP, que están en el array "otros")
  "fileuploadabuse_v2;$PWD/fileuploadabuse;8024:80" # Contenedor para File Upload Abuse
  "prototypepollution_v2;$PWD/prototypepollution;8025:3000" # Contenedor para Prototype Pollution
  "openredirect_v2;$PWD/openredirect;8026:80" # Contenedor para Open Redirect
  "squidproxy_v2;$PWD/squidproxy;8028:80 -p 3128:3128 --cap-add=NET_ADMIN" # Contenedor para SQUID Proxy
  "cors_v2;$PWD/cors;8029:80" # Contenedor para CORS
  "racecondition_v2;$PWD/racecondition;8033:80" # Contenedor para Race Condition
  "cssi_v2;$PWD/cssi;8034:80" # Contenedor para CSS Injection
  "yamldeseralization_v2;$PWD/yamldeseralization;8042:5000" # Contenedor para YAML Deserialization
  "pickledeseralization_v2;$PWD/pickledeseralization;8038:5000" # Contenedor para Pickle Deserialization
  "snmp_v2;$PWD/snmp;8040:80 -p 161:161/udp --sysctl net.ipv6.conf.all.disable_ipv6=0 --sysctl net.ipv6.conf.default.disable_ipv6=0 " # Contenedor para SNMP
)
database=(
    "sqli_db_v2;$PWD/sqli;8005:80;sqli_v2" # Contenedor para SQL Injection
    "blindsqli_db_v2;$PWD/blindsqli;8014:80;blindsqli_v2" # Contenedor para Blind SQL Injection
    "paddingoracleattack_db_v2;$PWD/paddingoracleattack;8007:80;paddingoracleattack_v2" # Contenedor para Padding Oracle Attack
    "idor_db_v2;$PWD/idor;8017:80;idor_v2" # Contenedor para iDOR
    "sqltruncation_db_v2;$PWD/sqltruncation;8030:80;sqltruncation_v2" # Contenedor para SQL Truncation
    "sessionpuzzling_db_v2;$PWD/sessionpuzzling;8031:80;sessionpuzzling_v2" # Contenedor para Session Puzzling
    "jwt_db_v2;$PWD/jwt;8032:80;jwt_v2" # Contenedor para JWT
)
otros=(
    "Construyendo contenedores para AWS Abuse;docker-compose -f $PWD/aws/docker-compose.yml up -d" # Contenedor para AWS Abuse
    "Construyendo contenedores para API Abuse;docker-compose -f $PWD/apiabuse/docker-compose.yml up -d" # Contenedor para API abuse (recomiendo ejecutarlo solo, sin otros contenedores)
    "Contruyendo contenedores para WebDAV;docker-compose -f $PWD/webdav/docker-compose.yml up -d" # Contenedor para WebDAV
    "Contruyendo contenedores para GraphQL;docker-compose -f $PWD/graphql/docker-compose.yml up -d" # Contenedor para GrphQL
    "Contruyendo contenedores para OAuth;docker-compose -f $PWD/oauth/docker-compose.yml up -d" # Contenedor para OAuth
    "Configurando archivos para LDAP;configure_ldap_files" # Función para configurar los archivos de LDAP
    "Configurando red para los contenedores;configure_network" # Función para poner todos los contenedores en una misma red
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

configure_ldap_files(){
    for container in "${containers[@]}"; do

            IFS=';' read -ra container_info <<< "$container"
            container_name=${container_info[0]}
            container_dir=${container_info[1]}
            container_ports=${container_info[2]}

            if [ "$container_name" == "ldap_server_v2" ]; then
                echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Configurando archivos para LDAP Server${endColour}"
                docker start ldap_server_v2
                ldapadd -x -H ldap://localhost -D "cn=admin,dc=ldapinjection,dc=local" -w admin -f $PWD/ldapinjection/ldapserver/users.ldif
                if [ $? -ne 0 ]; then
                    echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al configurar users.ldif${endColour}"
                else
                    echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Configurado correctamente users.ldif${endColour}"
                fi
                ldapadd -x -D "cn=admin,dc=ldapinjection,dc=local" -w admin -f $PWD/ldapinjection/ldapserver/user1.ldif
                if [ $? -ne 0 ]; then
                    echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al configurar user1.ldif${endColour}"
                    docker stop ldap_server_v2
                else
                    echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Configurado correctamente user1.ldif${endColour}"
                    docker stop ldap_server_v2
                fi
            fi

    done
}

configure_network() {

    # Crear network
    docker network create WebVulnLab-Network

    # Añadir contenedores a la red WebVulnLab-Network
    for container in "${containers[@]}"; do
        container_info=($(echo "$container" | tr ';' ' '))
        container_name=${container_info[0]%%_v2}
        container_dir=${container_info[1]}
        container_ports=${container_info[2]}

        docker network connect WebVulnLab-Network $container_info     

    done

    for db_container in "${database[@]}"; do
        db_container_info=($(echo "$db_container" | tr ';' ' '))
        db_container_name=${db_container_info[0]%%_db_v2}
        db_container_dir=${db_container_info[1]}
        db_container_ports=${db_container_info[2]}
        db_container_link=${db_container_info[3]}

        docker network connect WebVulnLab-Network $db_container_info

    done

    if [ -s "$error_file" ]; then
        echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Se encontraron errores. Por favor, revise el archivo $error_file para más detalles.${endColour}"
    else
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}WebVulnLab-Network configurada correctamente${endColour}"
    fi

    # Eliminar el archivo de errores si está vacío
    #[ -s "$error_file" ] || rm "$error_file"
}
# Función para crear archivo de virtual hosting

setup_file_virtual_hosting() {
    config_file="WebVulnLab.conf"
    error_file="error.log"

    # Create the Apache config file
    echo > "$config_file"

    # Create the Apache config file
    echo > "$config_file"

    # Add VirtualHost entry for tablero.local
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

    # Añadir las entradas de VirtualHost
    for container in "${containers[@]}"; do
        container_info=($(echo "$container" | tr ';' ' '))
        container_name=${container_info[0]%%_v2}
        container_dir=${container_info[1]}
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

    # Añadir las entradas de VirtualHost
    for db_container in "${database[@]}"; do
        db_container_info=($(echo "$db_container" | tr ';' ' '))
        db_container_name=${db_container_info[0]%%_db_v2}
        db_container_dir=${db_container_info[1]}
        db_container_ports=${db_container_info[2]}
        db_container_link=${db_container_info[3]}

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

    echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Apache configurado correctamente.${endColour}"

    if [ -s "$error_file" ]; then
        echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Se encontraron errores. Por favor, revise el archivo $error_file para más detalles.${endColour}"
    else
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Archivo WebVulnLab.conf generado correctamente.${endColour}"
    fi

    # Eliminar el archivo de errores si está vacío
    [ -s "$error_file" ] || rm "$error_file"
}

# Función para construir el servidor local
build_local_server() {
    log_file="build_server.log"

    echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo Tablero${endColour}"
    sudo cp -R tablero /var/www/html >> "$log_file" 2>&1
    if [ $? -eq 0 ]; then
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Tablero construido correctamente${endColour}"
    else
        echo -e "\n${yellowColour}[${endColour}${redColour}!${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Ocurrió un error durante la construcción del Tablero. Por favor, revise el archivo $log_file para más detalles.${endColour}"
        return 1
    fi

    echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando Tablero${endColour}"
    sudo sed -i 's/\-\-containerd=\/run\/containerd\/containerd.sock/\-H=tcp\:\/\/0\.0\.0\.0\:2375/' /lib/systemd/system/docker.service >> "$log_file" 2>&1
    sudo systemctl daemon-reload >> "$log_file" 2>&1
    sudo systemctl restart docker >> "$log_file" 2>&1

    version=$(php -v | sed -nr 's/PHP[[:space:]]+([0-9]+\.[0-9]+).*/\1/p')
    sudo apt-get install php$version-curl -y >> "$log_file" 2>&1
    sudo service apache2 restart >> "$log_file" 2>&1

    if [ $? -eq 0 ]; then
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}API REST de Docker configurada correctamente.${endColour}"
    else
        echo -e "\n${yellowColour}[${endColour}${redColour}!${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Ocurrió un error al configurar el API REST de Docker. Por favor, revise el archivo $log_file para más detalles.${endColour}"
        return 1
    fi

    # Eliminar el archivo de registro de errores si no contiene errores
    [ -s "$log_file" ] || rm "$log_file"
}

# Función para configurar el virtual host
configure_virtual_host() {
    log_file="configure_virtual_host.log"

    sudo a2enmod proxy proxy_http >> "$log_file" 2>&1
    if [ $? -ne 0 ]; then
        echo -e "\n${yellowColour}[${endColour}${redColour}!${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Ocurrió un error al habilitar los módulos de proxy. Por favor, revise el archivo $log_file para más detalles.${endColour}"
        return 1
    fi

    sudo cp WebVulnLab.conf /etc/apache2/sites-available >> "$log_file" 2>&1
    if [ $? -ne 0 ]; then
        echo -e "\n${yellowColour}[${endColour}${redColour}!${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Ocurrió un error al copiar el archivo de configuración. Por favor, revise el archivo $log_file para más detalles.${endColour}"
        return 1
    fi

    sudo a2ensite WebVulnLab.conf >> "$log_file" 2>&1
    if [ $? -ne 0 ]; then
        echo -e "\n${yellowColour}[${endColour}${redColour}!${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Ocurrió un error al habilitar el sitio virtual. Por favor, revise el archivo $log_file para más detalles.${endColour}"
        return 1
    fi

    sudo systemctl reload apache2 >> "$log_file" 2>&1
    if [ $? -ne 0 ]; then
        echo -e "\n${yellowColour}[${endColour}${redColour}!${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Ocurrió un error al recargar Apache. Por favor, revise el archivo $log_file para más detalles.${endColour}"
        return 1
    fi

    # Añadir entradas al archivo /etc/hosts
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
        echo -e "\n${yellowColour}[${endColour}${redColour}!${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Ocurrió un error al modificar el archivo de hosts. Por favor, revise el archivo $log_file para más detalles.${endColour}"
        return 1
    fi
    echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Configuración de Virtual Host completada correctamente.${endColour}"
    # Eliminar el archivo de registro de errores si no contiene errores
    [ -s "$log_file" ] || rm "$log_file"
}


# Comprobar si se esta ejecutando como usuario administrador

if [ "$(id -u)" != "0" ]; then
   echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Este script debe ser ejecutado con permisos de superusuario${endColour}"
   exit 1
fi

# Llamada a la funciones

build_local_server
setup_file_virtual_hosting
configure_virtual_host

# Habilitando IPv6 en los contenedores Docker, para el contenedor snmp

#------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

# Salir inmediatamente si un comando falla, si una variable no está definida y si falla alguna parte de un pipeline
set -euo pipefail

# Variables
FILE="/etc/docker/daemon.json"
MAX_SUBNETS=100
GENERATE_ULA=true
TMP_FILE=""
BACKUP_FILE=""
LOG_FILE="/var/log/assign_subnet.log"

# Funciones de Logging
log_info() {
    echo -e "${yellowColour}[${endColour}${blueColour}INFO${endColour}${yellowColour}]${endColour} ${grayColour} $(date '+%Y-%m-%d %H:%M:%S') - $* ${endColour}\n"
}

log_warn() {
    echo -e "${yellowColour}[WARN]${endColour} ${grayColour} $(date '+%Y-%m-%d %H:%M:%S') - $* ${endColour}\n" | tee -a "$LOG_FILE" >&2
}

log_error() {
    echo -e "${yellowColour}[${endColour}${redColour}ERROR${endColour}${yellowColour}]${endColour} ${grayColour} $(date '+%Y-%m-%d %H:%M:%S') - $* ${endColour}\n" | tee -a "$LOG_FILE" >&2
}

echo -e "\n${yellowColour}[${endColour}${blueColour}INFO${endColour}${yellowColour}]${endColour} ${grayColour} $(date '+%Y-%m-%d %H:%M:%S') - Configurando IPv6 para el contenedor snmp ${endColour}\n"

# Función de limpieza en caso de error o interrupción
cleanup() {
    if [[ -n "$TMP_FILE" && -f "$TMP_FILE" ]]; then
        sudo rm -f "$TMP_FILE"
        log_info "Archivo temporal $TMP_FILE eliminado."
    fi
}
trap cleanup EXIT INT TERM

# Verificar si el script se ejecuta como root
if [[ "$EUID" -ne 0 ]]; then
    log_error "Este script debe ejecutarse con privilegios de superusuario. Usa sudo."
    exit 1
fi

# Verificar si jq está instalado
if ! command -v jq &> /dev/null; then
    log_info "jq no está instalado. Instalándolo ahora..."
    sudo apt-get update && sudo apt-get install -y jq
    log_info "jq instalado correctamente."
fi

# Verificar si openssl está instalado (para generar prefijos ULA)
if ! command -v openssl &> /dev/null; then
    log_info "openssl no está instalado. Instalándolo ahora..."
    sudo apt-get update && sudo apt-get install -y openssl
    log_info "openssl instalado correctamente."
fi

# Función para comprobar si una subred está en uso
is_subnet_in_use() {
    local subnet=$1
    if ip -6 addr show | grep -qE "${subnet%/*}"; then
        return 0  # En uso
    else
        return 1  # Libre
    fi
}

# Función para encontrar una subred libre
find_free_subnet() {
    local base_subnet=$1
    for i in $(seq 1 "$MAX_SUBNETS"); do
        SUBNET="${base_subnet}${i}::/64"
        if ! is_subnet_in_use "$SUBNET"; then
            echo "$SUBNET"
            return 0
        fi
    done
    echo "No hay subredes libres disponibles dentro de ${base_subnet}" >&2
    return 1
}

# Función para detectar el prefijo IPv6 global existente
detect_global_prefix() {
    # Buscar direcciones IPv6 globales (excluyendo link-local y ULA)
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

# Función para generar un prefijo ULA único
generate_ula_prefix() {
    # Generar un identificador de 40 bits en hexadecimal
    local identifier
    identifier=$(openssl rand -hex 5)
    echo "fd${identifier}:"
}

# Verificar si la configuración de IPv6 ya existe en daemon.json
ipv6_exists=false
fixed_cidr_exists=false

if [[ -f "$FILE" && -s "$FILE" ]]; then
    # Validar que el JSON es válido
    if ! jq empty "$FILE" 2>/dev/null; then
        log_error "El archivo $FILE contiene JSON inválido. Por favor, corrígelo manualmente."
        exit 1
    fi

    # Comprobar si "ipv6" ya está configurado
    ipv6_check=$(jq 'has("ipv6")' "$FILE")
    if [[ "$ipv6_check" == "true" ]]; then
        ipv6_exists=true
        log_info "La clave 'ipv6' ya existe en $FILE."
    fi

    # Comprobar si "fixed-cidr-v6" ya está configurado
    fixed_cidr_check=$(jq 'has("fixed-cidr-v6")' "$FILE")
    if [[ "$fixed_cidr_check" == "true" ]]; then
        fixed_cidr_exists=true
        log_info "La clave 'fixed-cidr-v6' ya existe en $FILE."
    fi
fi

# Si tanto "ipv6" como "fixed-cidr-v6" ya están configurados, no hacer nada
if $ipv6_exists && $fixed_cidr_exists; then
    log_info "La configuración de IPv6 ya existe en $FILE. No se realizarán cambios."
else
    # Intentar detectar un prefijo IPv6 global existente
    BASE_PREFIX=$(detect_global_prefix)

    if [[ -z "$BASE_PREFIX" ]]; then
        if $GENERATE_ULA; then
            log_info "No se detectó un prefijo IPv6 global. Generando un prefijo ULA único..."
            BASE_PREFIX=$(generate_ula_prefix)
            log_info "Prefijo ULA generado: ${BASE_PREFIX}/48"
        else
            log_error "No se detectó un prefijo IPv6 global y la generación de ULA está deshabilitada."
            exit 1
        fi
    else
        log_info "Prefijo IPv6 global detectado: ${BASE_PREFIX}/48"
    fi

    # Encontrar una subred libre dentro del prefijo base
    FREE_SUBNET=$(find_free_subnet "$BASE_PREFIX") || {
        log_error "No se pudo encontrar una subred libre."
        exit 1
    }

    log_info "Subred libre encontrada: $FREE_SUBNET"

    # Crear una copia de seguridad del archivo existente, si no está vacío
    if [[ -f "$FILE" && -s "$FILE" ]]; then
        BACKUP_FILE="${FILE}.bak_$(date +%F_%T)"
        sudo cp "$FILE" "$BACKUP_FILE" || {
            log_error "No se pudo crear una copia de seguridad de $FILE."
            exit 1
        }
        log_info "Copia de seguridad creada: $BACKUP_FILE"
    fi

    # Actualizar el JSON existente o crear uno nuevo
    if [[ -f "$FILE" && -s "$FILE" ]]; then
        if $ipv6_exists; then
            # Actualizar solo "fixed-cidr-v6"
            UPDATED_JSON=$(jq --arg cidr "$FREE_SUBNET" '.["fixed-cidr-v6"] = $cidr' "$FILE") || {
                log_error "Error al actualizar 'fixed-cidr-v6' en $FILE."
                exit 1
            }
            log_info "Actualizando 'fixed-cidr-v6' en $FILE."
        else
            # Añadir "ipv6" y "fixed-cidr-v6"
            UPDATED_JSON=$(jq --arg cidr "$FREE_SUBNET" '. + { "ipv6": true, "fixed-cidr-v6": $cidr }' "$FILE") || {
                log_error "Error al añadir 'ipv6' y 'fixed-cidr-v6' en $FILE."
                exit 1
            }
            log_info "Añadiendo 'ipv6' y 'fixed-cidr-v6' en $FILE."
        fi
    else
        # Crear un nuevo JSON con "ipv6" y "fixed-cidr-v6"
        UPDATED_JSON=$(jq -n --arg cidr "$FREE_SUBNET" '
            {
                "ipv6": true,
                "fixed-cidr-v6": $cidr
            }
        ') || {
            log_error "Error al crear un nuevo JSON para $FILE."
            exit 1
        }
        log_info "Creando un nuevo archivo JSON en $FILE con 'ipv6' y 'fixed-cidr-v6'."
    fi

    # Escribir el JSON actualizado en un archivo temporal
    TMP_FILE=$(mktemp) || {
        log_error "No se pudo crear un archivo temporal."
        exit 1
    }

    echo "$UPDATED_JSON" | sudo tee "$TMP_FILE" > /dev/null || {
        log_error "No se pudo escribir en el archivo temporal $TMP_FILE."
        exit 1
    }

    # Validar el JSON temporal
    if jq empty "$TMP_FILE" 2>/dev/null; then
        # Mover el archivo temporal al destino
        sudo mv "$TMP_FILE" "$FILE" || {
            log_error "No se pudo mover el archivo temporal a $FILE."
            exit 1
        }
        log_info "Subred asignada y añadida a $FILE: $FREE_SUBNET"
    else
        log_error "El JSON temporal es inválido. No se realizó ningún cambio."
        sudo rm -f "$TMP_FILE"
        exit 1
    fi

    # Reiniciar Docker para aplicar los cambios
    if sudo systemctl restart docker; then
        log_info "Docker ha sido reiniciado exitosamente para aplicar los cambios."
    else
        log_error "Error al reiniciar Docker. Restaurando la copia de seguridad."
        if [[ -n "$BACKUP_FILE" && -f "$BACKUP_FILE" ]]; then
            sudo cp "$BACKUP_FILE" "$FILE" || {
                log_error "No se pudo restaurar la copia de seguridad desde $BACKUP_FILE."
                exit 1
            }
            log_info "Copia de seguridad restaurada desde $BACKUP_FILE."
        fi
        sudo systemctl restart docker || {
            log_error "Docker aún no se puede reiniciar correctamente. Revisa los logs para más detalles."
            exit 1
        }
        exit 1
    fi
fi

#------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

# Preguntar al usuario si desea ignorar errores
echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}¿Desea ignorar los errores, a la hora de construirlos? (s/N)${endColour}"
read user_input_ignore_errors

#Pregustar si desea ocultar el output de los comandos

echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}¿Desea ocultar el output de los comandos ejecutados durante la ejecución del script? (S/n)${endColour}"
read user_input_hide_output

# Verificar si el usuario ingresó una respuesta válida
if [ "$user_input_ignore_errors" = "s" ] || [ "$user_input_ignore_errors" = "S" ]; then
    ignore_errors="s"
fi
if [ "$user_input_ignore_errors" = "n" ] || [ "$user_input_ignore_errors" = "N" ]; then
    ignore_errors="n"
fi
if [ "$user_input_hide_output" = "n" ] || [ "$user_input_hide_output" = "N" ]; then
    hide_output="n"
fi
if [ "$user_input_hide_output" = "s" ] || [ "$user_input_hide_output" = "S" ]; then
    hide_output="s"
fi

# Construir e iniciar sontenedores segun las opciones del usuario

if [ "$hide_output" = "s" ]; then

    if [ "$ignore_errors" = "s" ]; then

        # Si ha escogido ocultar el output y saltar los errores

        for container in "${containers[@]}"; do

            IFS=';' read -ra container_info <<< "$container"
            container_name=${container_info[0]}
            container_dir=${container_info[1]}
            container_ports=${container_info[2]}

            echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del $container_name${endColour}"
            sudo docker build -t $container_name $container_dir > /dev/null 2>&1
            if [ $? -ne 0 ]; then
                echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen $container_name${endColour}"
            else
                echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
            fi

            echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker $container_name${endColour}"
            if [ "$container_name" == "ldap_server_v2" ]; then
                sudo docker run --name $container_name -d -p $container_ports $container_name > /dev/null 2>&1
            else
                sudo docker run --name $container_name -d -v $container_dir/src:/var/www/html -p $container_ports $container_name > /dev/null 2>&1
            fi
            if [ $? -ne 0 ]; then
                echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor $container_name${endColour}"
            else
                echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker $container_name iniciado correctamente${endColour}"
            fi

            sudo docker stop $(docker ps -aq) > /dev/null 2>&1

        done

        for database in "${database[@]}"; do

            IFS=';' read -ra database_info <<< "$database"
            database_name=${database_info[0]}
            container_dir=${database_info[1]}
            container_ports=${database_info[2]}
            container_name=${database_info[3]}

            echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del $container_name${endColour}"
            sudo docker build -t $container_name $container_dir > /dev/null 2>&1
            if [ $? -ne 0 ]; then
                echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen $container_name${endColour}"
            else
                echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
            fi
            echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker $database_name${endColour}"
            sudo docker run --name $database_name -e MYSQL_ROOT_PASSWORD=rootpassword -e MYSQL_DATABASE=database -e MYSQL_USER=usuario -e MYSQL_PASSWORD=contraseña -d mysql:5.7 > /dev/null 2>&1
            if [ $? -ne 0 ]; then
                echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor $database_name${endColour}"
            else
                echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker $database_name iniciado correctamente${endColour}"
            fi                                                             
            sudo docker run --name $container_name --link $database_name:db -p $container_ports -v $container_dir/src:/var/www/html/ -d $container_name > /dev/null 2>&1
            if [ $? -ne 0 ]; then
                echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor $container_name${endColour}"
            else 
                echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker $container_name iniciado correctamente${endColour}"
            fi

            sudo docker stop $(docker ps -aq) > /dev/null 2>&1

        done

        for otros in "${otros[@]}"; do

            IFS=';' read -ra otros_info <<< "$otros"
            info=${otros_info[0]}
            command=${otros_info[1]}

            echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}$info${endColour}"
            eval "$command > /dev/null 2>&1"
            if [ $? -ne 0 ]; then
                echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}$info${endColour}"
            fi
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}$info${endColour}"
            sudo docker stop $(docker ps -aq) > /dev/null 2>&1
        done

    else

        # Si ha escogido esconder el output pero no saltar los errores

        for container in "${containers[@]}"; do

            IFS=';' read -ra container_info <<< "$container"
            container_name=${container_info[0]}
            container_dir=${container_info[1]}
            container_ports=${container_info[2]}

            echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del $container_name${endColour}"
            sudo docker build -t $container_name $container_dir > /dev/null 2>&1
            if [ $? -ne 0 ]; then
                echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen $container_name${endColour}"
                exit 1;
            else
                echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
            fi

            echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker $container_name${endColour}"
            if [ "$container_name" == "ldap_server_v2" ]; then
                sudo docker run --name $container_name -d -p $container_ports $container_name > /dev/null 2>&1
            else
                sudo docker run --name $container_name -d -v $container_dir/src:/var/www/html -p $container_ports $container_name > /dev/null 2>&1
            fi
            if [ $? -ne 0 ]; then
                echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor $container_name${endColour}"
                exit 1;
            else
                echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker $container_name iniciado correctamente${endColour}"
            fi
            sudo docker stop $(docker ps -aq) > /dev/null 2>&1
        done

        for database in "${database[@]}"; do

            IFS=';' read -ra database_info <<< "$database"
            database_name=${database_info[0]}
            container_dir=${database_info[1]}
            container_ports=${database_info[2]}
            container_name=${database_info[3]}

            echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del $container_name${endColour}"
            sudo docker build -t $container_name $container_dir > /dev/null 2>&1
            if [ $? -ne 0 ]; then
                echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen $container_name${endColour}"
                exit 1;
            else
                echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
            fi
            echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker $database_name${endColour}"
            sudo docker run --name $database_name -e MYSQL_ROOT_PASSWORD=rootpassword -e MYSQL_DATABASE=database -e MYSQL_USER=usuario -e MYSQL_PASSWORD=contraseña -d mysql:5.7 > /dev/null 2>&1
            if [ $? -ne 0 ]; then
                echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor $database_name${endColour}"
                exit 1;
            else
                echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker $database_name iniciado correctamente${endColour}"
            fi                                                             
            sudo docker run --name $container_name --link $database_name:db -p $container_ports -v $container_dir/src:/var/www/html/ -d $container_name > /dev/null 2>&1
            if [ $? -ne 0 ]; then
                echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor $container_name${endColour}"
                exit 1;
            else 
                echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker $container_name iniciado correctamente${endColour}"
            fi
            sudo docker stop $(docker ps -aq) > /dev/null 2>&1

        done

        for otros in "${otros[@]}"; do

            IFS=';' read -ra otros_info <<< "$otros"
            info=${otros_info[0]}
            command=${otros_info[1]}

            echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}$info${endColour}"
            eval "$command > /dev/null 2>&1"
            if [ $? -ne 0 ]; then
                echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}$info${endColour}"
                exit 1;
            fi
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}$info${endColour}"
            sudo docker stop $(docker ps -aq) > /dev/null 2>&1
        done

    fi
else

    if [ "$ignore_errors" = "s" ]; then

        # Si ha escogido no ocultar el output y saltar los errores

        for container in "${containers[@]}"; do

            IFS=';' read -ra container_info <<< "$container"
            container_name=${container_info[0]}
            container_dir=${container_info[1]}
            container_ports=${container_info[2]}

            echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del $container_name${endColour}"
            sudo docker build -t $container_name $container_dir
            if [ $? -ne 0 ]; then
                echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen $container_name${endColour}"
            else
                echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
            fi

            echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker $container_name${endColour}"
            if [ "$container_name" == "ldap_server_v2" ]; then
                sudo docker run --name $container_name -d -p $container_ports $container_name
            else
                sudo docker run --name $container_name -d -v $container_dir/src:/var/www/html -p $container_ports $container_name
            fi
            if [ $? -ne 0 ]; then
                echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor $container_name${endColour}"
            else
                echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker $container_name iniciado correctamente${endColour}"
            fi

            sudo docker stop $(docker ps -aq) > /dev/null 2>&1

        done

        for database in "${database[@]}"; do

            IFS=';' read -ra database_info <<< "$database"
            database_name=${database_info[0]}
            container_dir=${database_info[1]}
            container_ports=${database_info[2]}
            container_name=${database_info[3]}

            echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del $container_name${endColour}"
            sudo docker build -t $container_name $container_dir
            if [ $? -ne 0 ]; then
                echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen $container_name${endColour}"
            else
                echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
            fi
            echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker $database_name${endColour}"
            sudo docker run --name $database_name -e MYSQL_ROOT_PASSWORD=rootpassword -e MYSQL_DATABASE=database -e MYSQL_USER=usuario -e MYSQL_PASSWORD=contraseña -d mysql:5.7
            if [ $? -ne 0 ]; then
                echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor $database_name${endColour}"
            else
                echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker $database_name iniciado correctamente${endColour}"
            fi                                                             
            sudo docker run --name $container_name --link $database_name:db -p $container_ports -v $container_dir/src:/var/www/html/ -d $container_name
            if [ $? -ne 0 ]; then
                echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor $container_name${endColour}"
            else 
                echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker $container_name iniciado correctamente${endColour}"
            fi

            sudo docker stop $(docker ps -aq) > /dev/null 2>&1

        done

        for otros in "${otros[@]}"; do

            IFS=';' read -ra otros_info <<< "$otros"
            info=${otros_info[0]}
            command=${otros_info[1]}

            echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}$info${endColour}"
            eval "$command"
            if [ $? -ne 0 ]; then
                echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}$info${endColour}"
            fi
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}$info${endColour}"
            sudo docker stop $(docker ps -aq) > /dev/null 2>&1
        done

    else

        # Si ha escogido no esconder el output pero no saltar los errores

        for container in "${containers[@]}"; do

            IFS=';' read -ra container_info <<< "$container"
            container_name=${container_info[0]}
            container_dir=${container_info[1]}
            container_ports=${container_info[2]}

            echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del $container_name${endColour}"
            sudo docker build -t $container_name $container_dir
            if [ $? -ne 0 ]; then
                echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen $container_name${endColour}"
                exit 1;
            else
                echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
            fi

            echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker $container_name${endColour}"
           if [ "$container_name" == "ldap_server_v2" ]; then
                sudo docker run --name $container_name -d -p $container_ports $container_name
            else
                sudo docker run --name $container_name -d -v $container_dir/src:/var/www/html -p $container_ports $container_name
            fi
            if [ $? -ne 0 ]; then
                echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor $container_name${endColour}"
                exit 1;
            else
                echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker $container_name iniciado correctamente${endColour}"
            fi
            sudo docker stop $(docker ps -aq) > /dev/null 2>&1
        done

        for database in "${database[@]}"; do

            IFS=';' read -ra database_info <<< "$database"
            database_name=${database_info[0]}
            container_dir=${database_info[1]}
            container_ports=${database_info[2]}
            container_name=${database_info[3]}

            echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del $container_name${endColour}"
            sudo docker build -t $container_name $container_dir
            if [ $? -ne 0 ]; then
                echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen $container_name${endColour}"
                exit 1;
            else
                echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
            fi
            echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker $database_name${endColour}"
            sudo docker run --name $database_name -e MYSQL_ROOT_PASSWORD=rootpassword -e MYSQL_DATABASE=database -e MYSQL_USER=usuario -e MYSQL_PASSWORD=contraseña -d mysql:5.7
            if [ $? -ne 0 ]; then
                echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor $database_name${endColour}"
                exit 1;
            else
                echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker $database_name iniciado correctamente${endColour}"
            fi                                                             
            sudo docker run --name $container_name --link $database_name:db -p $container_ports -v $container_dir/src:/var/www/html/ -d $container_name
            if [ $? -ne 0 ]; then
                echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor $container_name${endColour}"
                exit 1;
            else 
                echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker $container_name iniciado correctamente${endColour}"
            fi

            sudo docker stop $(docker ps -aq) > /dev/null 2>&1

        done

        for otros in "${otros[@]}"; do

            IFS=';' read -ra otros_info <<< "$otros"
            info=${otros_info[0]}
            command=${otros_info[1]}

            echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}$info${endColour}"
            eval "$command"
            if [ $? -ne 0 ]; then
                echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}$info${endColour}"
                exit 1;
            fi
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}$info${endColour}"
            sudo docker stop $(docker ps -aq) > /dev/null 2>&1
        done

    fi

fi
