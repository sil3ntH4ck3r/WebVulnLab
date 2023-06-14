#!/bin/bash

# Colores
greenColour="\e[0;32m\033[1m"
endColour="\033[0m\e[0m"
redColour="\e[0;31m\033[1m"
blueColour="\e[0;34m\033[1m"
yellowColour="\e[0;33m\033[1m"
purpleColour="\e[0;35m\033[1m"
turquoiseColour="\e[0;36m\033[1m"
grayColour="\e[0;37m\033[1m"

containers=(
  "lfi_v2;$PWD/lfi;8000:80"
  "menu_v2;$PWD/menu;8080:80"
  "csrf_v2;$PWD/csrf;8001:80"
  "blindxxe_v2;$PWD/blindxxe;8002:80"
  "xxe_v2;$PWD/xxe;8003:80"
  "xss_v2;$PWD/xss;8004:80"
  "domainzonetransfer_v2;$PWD/domainzonetransfer;53:53/udp -p 53:53/tcp"
  "typejuggling_v2;$PWD/typejuggling;8008:80"
  "rfi_v2;$PWD/rfi;8009:80"
  "latexinjection_v2;$PWD/latexinjection;8011:80"
  "xpathinjection_v2;$PWD/xpathinjection;8012:80"
  "shellshock_v2;$PWD/shellshock;8013:80"
)
database=(
    "sqli_db_v2;$PWD/sqli;8005:80;sqli_v2"
    "blind_sqli_db_v2;$PWD/blindsqli;8014:80;blindsqli_v2"
    "paddingoracleattack_db_v2;$PWD/paddingoracleattack;8007:80;paddingoracleattack_v2"
)

if [ "$(id -u)" != "0" ]; then
   echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Este script debe ser ejecutado con permisos de superusuario${endColour}"
   exit 1
fi

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

pwd=$(pwd)

# CONSTRUYENDO SERVIDOR LOCAL
echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo Tablero${endColour}"
sudo cp -R tablero /srv/http
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Tablero constuido correctamente${endColour}"
echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando Tablero${endColour}"
sudo sed -i 's/\-\-containerd=\/run\/containerd\/containerd.sock/\-H=tcp\:\/\/0\.0\.0\.0\:2375/' /usr/lib/systemd/system/docker.service
systemctl daemon-reload
sudo systemctl restart docker
version=$(php -v | sed -nr 's/PHP[[:space:]]+([0-9]+\.[0-9]+).*/\1/p')
sudo pacman -S --noconfirm php-curl
sudo systemctl restart httpd
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Tablero iniciado correctamente${endColour}"

# CONFIGURANDO VIRTUAL HOST
sudo sed -i 's/#LoadModule proxy_module modules\/mod_proxy.so/LoadModule proxy_module modules\/mod_proxy.so/' /etc/httpd/conf/httpd.conf
sudo sed -i 's/#LoadModule proxy_http_module modules\/mod_proxy_http.so/LoadModule proxy_http_module modules\/mod_proxy_http.so/' /etc/httpd/conf/httpd.conf
sudo cp WebVulnLabArchLinux.conf /etc/httpd/conf/extra/
echo "Include conf/extra/WebVulnLabArchLinux.conf" | sudo tee -a /etc/httpd/conf/httpd.conf > /dev/null
sudo systemctl reload httpd

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

ignore_errors="n"
hide_output="s"

# Preguntar al usuario si desea ignorar errores
echo -e "\n${yellowColour}[${endColour}${Colour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}¿Desea ignorar los errores, a la hora de construirlos? (s/N)${endColour}"
read user_input_hide_output

# Verificar si el usuario ingresó una respuesta válida
if [ "$user_input_hide_output" = "s" ] || [ "$user_input_hide_output" = "S" ]; then
    ignore_errors="s"
fi
if [ "$user_input_hide_output" = "n" ] || [ "$user_input_hide_output" = "N" ]; then
    ignore_errors="n"
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