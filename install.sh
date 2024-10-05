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
  "yamldeseralization_v2;$PWD/yamldeseralization;8036:5000" # Contenedor para YAML Deserialization
  "pickledeseralization_v2;$PWD/pickledeseralization;8038:5000" # Contenedor para Pickle Deserialization
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

    echo "127.0.0.1 ${hosts_entries[*]} tablero.local codefusiondev.domainzonetransfer.local apiabuse.local mail.local webdav.local graphql.local oauth_printing.local oauth_gallery.local" >> /etc/hosts


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

# Preguntar al usuario si desea ignorar errores
echo -e "\n${yellowColour}[${endColour}${Colour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}¿Desea ignorar los errores, a la hora de construirlos? (s/N)${endColour}"
read user_input_ignore_errors

#Pregustar si desea ocultar el output de los comandos

echo -e "\n${yellowColour}[${endColour}${Colour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}¿Desea ocultar el output de los comandos ejecutados durante la ejecución del script? (S/n)${endColour}"
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
