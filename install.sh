#!/bin/bash

# Variables
    # Establecer valor predeterminado para ignore_errors
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
  "domainzonetransfer_v2;$PWD/domainzonetransfer;53:53/udp -p 53:53/tcp"
  "ssrf_v2;$PWD/ssrf;8006:80"
  "typejuggling_v2;$PWD/typejuggling;8008:80"
  "rfi_v2;$PWD/rfi;8009:80"
  "insecuredeseralizationphp_v2;$PWD/insecuredeseralizationphp;8010:80"
  "latexinjection_v2;$PWD/latexinjection;8011:80"
  "xpathinjection_v2;$PWD/xpathinjection;8012:80"
  "shellshock_v2;$PWD/shellshock;8013:80"
)
database=(
    "sqli_db_v2;$PWD/sqli;8005:80;sqli_v2"
    "blindsqli_db_v2;$PWD/blindsqli;8014:80;blindsqli_v2"
    "paddingoracleattack_db_v2;$PWD/paddingoracleattack;8007:80;paddingoracleattack_v2"
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

    echo "127.0.0.1 ${hosts_entries[*]} tablero.local" >> /etc/hosts


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
            sudo docker run --name $container_name -d -v $container_dir/src:/var/www/html -p $container_ports $container_name > /dev/null 2>&1
            if [ $? -ne 0 ]; then
                echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor $container_name${endColour}"
            else
                echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker $container_name iniciado correctamente${endColour}"
            fi

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
            sudo docker run --name $container_name -d -v $container_dir/src:/var/www/html -p $container_ports $container_name > /dev/null 2>&1
            if [ $? -ne 0 ]; then
                echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor $container_name${endColour}"
                exit 1;
            else
                echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker $container_name iniciado correctamente${endColour}"
            fi
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
            sudo docker run --name $container_name -d -v $container_dir/src:/var/www/html -p $container_ports $container_name
            if [ $? -ne 0 ]; then
                echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor $container_name${endColour}"
            else
                echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker $container_name iniciado correctamente${endColour}"
            fi

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
            sudo docker run --name $container_name -d -v $container_dir/src:/var/www/html -p $container_ports $container_name
            if [ $? -ne 0 ]; then
                echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor $container_name${endColour}"
                exit 1;
            else
                echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker $container_name iniciado correctamente${endColour}"
            fi
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

        done

    fi

fi
