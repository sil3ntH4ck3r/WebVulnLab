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
sudo cp -R tablero /var/www/html
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Tablero constuido correctamente${endColour}"
echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando Tablero${endColour}"
sudo sed -i 's/\-\-containerd=\/run\/containerd\/containerd.sock/\-H=tcp\:\/\/0\.0\.0\.0\:2375/' /lib/systemd/system/docker.service
systemctl daemon-reload
sudo systemctl restart docker
$(php -v | sed -nr 's/PHP[[:space:]]+([0-9]+\.[0-9]+).*/\1/p') = $version
sudo apt-get install php$version-curl
sudo service apache2 restart
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Tablero iniciado correctamente${endColour}"

# CONFIGURANDO VIRTUAL HOST
sudo a2enmod proxy
sudo a2enmod proxy_http
sudo cp WebVulnLab.conf /etc/apache2/sites-available 
sudo a2ensite WebVulnLab.conf
sudo systemctl reload apache2

sudo echo "127.0.0.1 tablero.local lfi.local menu.local sqli.local paddingoracleattack.local typejuggling.local rfi.local xss.local xxe.local blindxxe.local latexinjection.local domainzonetransfer.local csrf.local xpathinjection.local shellshock.local" >> /etc/hosts

# MEJORAS

# Establecer valor predeterminado para ignore_errors
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

echo -e "\n${yellowColour}[${endColour}${Colour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}¿Desea ocultar el output de los comandos ejecutados durante la ejeciccion del script? (S/n)${endColour}"
read user_input_hide_output

if [ "$user_input_hide_output" = "s" ] || [ "$user_input_hide_output" = "S" ]; then
    hide_output="s"
fi
if [ "$user_input_hide_output" = "n" ] || [ "$user_input_hide_output" = "N" ]; then
    hide_output="n"
fi

if [ "$hide_output" = "s" ]; then

    if [ "$ignore_errors" = "s" ]; then
    
        # CONFIGURANDO CONTENEDORES (ignorando errores y sin mostrar output)

        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del LFI_V2${endColour}"
        sudo docker build -t lfi_v2 $pwd/lfi > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen LFI_V2${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        fi
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker LFI_V2${endColour}"
        sudo docker run --name lfi_v2 -d -v $pwd/lfi/src:/var/www/html -p 8000:80 lfi_v2 > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor LFI_V2${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker LFI_V2 iniciado correctamente${endColour}"
        fi
    #--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------    
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del MENU_V2${endColour}"
        sudo docker build -t menu_v2 $pwd/menu > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen MENU_V2${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        fi
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker MENU_V2${endColour}"
        sudo docker run --name menu_v2 -d -v $pwd/menu/src:/var/www/html -p 8080:80 menu_v2 > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor MENU_V2${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker MENU_V2 iniciado correctamente${endColour}"
        fi
    #-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- 
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del SQLI_V2${endColour}"
        sudo docker build -t sqli_v2 $pwd/sqli > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen SQLI_V2${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        fi
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker SQLI_V2 i SQLI_DB_V2${endColour}"
        sudo docker run --name sqli_db_v2 -e MYSQL_ROOT_PASSWORD=rootpassword -e MYSQL_DATABASE=database -e MYSQL_USER=usuario -e MYSQL_PASSWORD=contraseña -d mysql:5.7 > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor SQLI_DB_V2${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker SQLI_DB_V2 iniciado correctamente${endColour}"
        fi                                                             
        sudo docker run --name sqli_v2 --link sqli_db_v2:db -p 8005:80 -v $pwd/sqli/src:/var/www/html/ -d sqli_v2 > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor SQLI_V2${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker SQLI_V2 iniciado correctamente${endColour}"
        fi
    #--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del PADDING_V2${endColour}"
        sudo docker build -t padding_v2 $pwd/paddingOracleAttack > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen PADDING_V2${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        fi
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker PADDING_V2 i PADDING_DB_V2${endColour}"
        sudo docker run --name padding_db_v2 -e MYSQL_ROOT_PASSWORD=rootpassword -e MYSQL_DATABASE=database -e MYSQL_USER=usuario -e MYSQL_PASSWORD=contraseña -d mysql:5.7 > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor PADDING_DB_V2${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker PADDING_DB_V2 iniciado correctamente${endColour}"
        fi                                                             
        sudo docker run --name padding_v2 --link padding_db_v2:db -p 8007:80 -v $pwd/paddingOracleAttack/src:/var/www/html/ -d padding_v2 > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor PADDING_V2${endColour}"
        else 
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker PADDING_V2 iniciado correctamente${endColour}"
        fi
    #--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del TYPEJUGGLING_V2${endColour}"
        sudo docker build -t typejuggling_v2 $pwd/typeJuggling > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen TYPEJUGGLING_V22${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        fi
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker TYPEJUGGLING_V2${endColour}"
        sudo docker run --name typejuggling_v2 -d -v $pwd/typeJuggling/src:/var/www/html -p 8008:80 typejuggling_v2 > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor TYPEJUGGLING_V2${endColour}"
        else    
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker TYPEJUGGLING_V2 iniciado correctamente${endColour}"
        fi
    #--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del RFI_V2${endColour}"
        sudo docker build -t rfi_v2 $pwd/rfi > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen RFI_V22${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        fi
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker RFI_V2${endColour}"
        sudo docker run --name rfi_v2 -d -v $pwd/rfi/src:/var/www/html -p 8009:80 rfi_v2 > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor RFI_V2${endColour}"
        else 
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker RFI_V2 iniciado correctamente${endColour}"
        fi
    #--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del XSS_V2${endColour}"
        sudo docker build -t xss_v2 $pwd/xss > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen XSS_V22${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        fi
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker XSS_V2${endColour}"
        sudo docker run --name xss_v2 -d -v $pwd/xss/src:/var/www/html -p 8004:80 xss_v2 > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor XSS_V2${endColour}"
        else 
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker XSS_V2 iniciado correctamente${endColour}"
        fi
    #--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del XXE_V2${endColour}"
        sudo docker build -t xxe_v2 $pwd/xxe > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen XXE_V22${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        fi
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker XXE_V2${endColour}"
        sudo docker run --name xxe_v2 -d -v $pwd/xxe/src:/var/www/html -p 8003:80 xxe_v2 > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor XXE_V2${endColour}"
        else 
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker XXE_V2 iniciado correctamente${endColour}"
        fi
    #--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del BLIND_XXE_V2${endColour}"
        sudo docker build -t blindxxe_v2 $pwd/blindxxe > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen BLINDXXE_V2${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        fi
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker BLIND_XXE_V2${endColour}"
        sudo docker run --name blindxxe_v2 -d -v $pwd/blindxxe/src:/var/www/html -p 8002:80 blindxxe_v2 > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor BLINDXXE_V2${endColour}"
        else 
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker BLIND_XXE_V2 iniciado correctamente${endColour}"
        fi
    #--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del CSRF_V2${endColour}"
        sudo docker build -t csrf_v2 $pwd/csrf > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen CSRF_V2${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        fi
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker CSRF_V2${endColour}"
        sudo docker run --name csrf_v2 -d -v $pwd/csrf/src:/var/www/html -p 8001:80 csrf_v2 > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor CSRF_V2${endColour}"
        else 
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker CSRF_V2 iniciado correctamente${endColour}"
        fi
    #--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del DOMAIN_ZONE_TRANSFER_V2${endColour}"
        sudo docker build -t domainzonetransfer_v2 $pwd/domainZoneTransfer > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen DOMAIN_ZONE_TRANSFER_V2${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        fi
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker DOMAIN_ZONE_TRANSFER_V2${endColour}"
        sudo docker run --name domainzonetransfer_v2 -d -p 53:53/udp -p 53:53/tcp domainzonetransfer_v2 > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor DOMAIN_ZONE_TRANSFER_V2${endColour}"
        else 
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker DOMAIN_ZONE_TRANSFER_V2 iniciado correctamente${endColour}"
        fi
    #--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del LATEX_INJECTION_V2${endColour}"
        sudo docker build -t latexinjection_v2 $pwd/LaTeXInjection > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen LATEX_INJECTION_V2${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        fi 
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker LATEX_INJECTION_V2${endColour}"
        sudo docker run --name latexinjection_v2 -d -v $pwd/LaTeXInjection/src:/var/www/html -p 8011:80 latexinjection_v2 > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor LATEX_INJECTION_V2${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker LATEX_INJECTION_V2 iniciado correctamente${endColour}"
        fi
    #--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del XPATH_INJECTION_V2${endColour}"
        sudo docker build -t xpathinjection_v2 $pwd/xpathinjection > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen XPATH_INJECTION_V2${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        fi
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker XPATH_INJECTION_V2${endColour}"
        sudo docker run --name xpathinjection_v2 -d -v $pwd/xpathinjection/src:/var/www/html -p 8012:80 xpathinjection_v2 > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor XPATH_INJECTION_V2${endColour}"
        else 
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker XPATH_INJECTION_V2 iniciado correctamente${endColour}"
        fi
    #--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del SHELLSHOCK_V2${endColour}"
        sudo docker build -t shellshock_v2 $pwd/shellshock > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen SHELLSHOCK_V2${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        fi
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker SHELLSHOCK_V2${endColour}"
        sudo docker run --name shellshock_v2 -d -v $pwd/shellshock/src:/var/www/html/website -p 8013:80 shellshock_v2 > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor SHELLSHOCK_V2${endColour}"
        else 
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker SHELLSHOCK_V2 iniciado correctamente${endColour}"
        fi
    else #--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

        # CONFIGURANDO CONTENEDORES (sin ignorar errores y sin mostrar output) -------------------------------------------------------------------------------------------------------------------------------------------------------

        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del LFI_V2${endColour}"
        sudo docker build -t lfi_v2 $pwd/lfi > /dev/null 2>&1 
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen LFI_V2${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker LFI_V2${endColour}"
        sudo docker run --name lfi_v2 -d -v $pwd/lfi/src:/var/www/html -p 8000:80 lfi_v2 > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor LFI_V2${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker LFI_V2 iniciado correctamente${endColour}"

        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del MENU_V2${endColour}"
        sudo docker build -t menu_v2 $pwd/menu > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen MENU_V2${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker MENU_V2${endColour}"
        sudo docker run --name menu_v2 -d -v $pwd/menu/src:/var/www/html -p 8080:80 menu_v2 > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor MENU_V2${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker MENU_V2 iniciado correctamente${endColour}"

        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del SQLI_V2${endColour}"
        sudo docker build -t sqli_v2 $pwd/sqli > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen SQLI_V2${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker SQLI_V2 i SQLI_DB_V2${endColour}"
        sudo docker run --name sqli_db_v2 -e MYSQL_ROOT_PASSWORD=rootpassword -e MYSQL_DATABASE=database -e MYSQL_USER=usuario -e MYSQL_PASSWORD=contraseña -d mysql:5.7 > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor SQLI_DB_V2${endColour}"
            exit 1;
        fi                                                             
        sudo docker run --name sqli_v2 --link sqli_db_v2:db -p 8005:80 -v $pwd/sqli/src:/var/www/html/ -d sqli_v2 > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor SQLI_V2${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker SQLI_V2 iniciado correctamente${endColour}"

        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del PADDING_V2${endColour}"
        sudo docker build -t padding_v2 $pwd/paddingOracleAttack > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen PADDING_V2${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker PADDING_V2 i PADDING_DB_V2${endColour}"
        sudo docker run --name padding_db_v2 -e MYSQL_ROOT_PASSWORD=rootpassword -e MYSQL_DATABASE=database -e MYSQL_USER=usuario -e MYSQL_PASSWORD=contraseña -d mysql:5.7 > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor PADDING_DB_V2${endColour}"
            exit 1;
        fi                                                             
        sudo docker run --name padding_v2 --link padding_db_v2:db -p 8007:80 -v $pwd/paddingOracleAttack/src:/var/www/html/ -d padding_v2 > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor PADDING_V2${endColour}"
            exit 1;
        fi 
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker PADDING_V2 iniciado correctamente${endColour}"

        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del TYPEJUGGLING_V2${endColour}"
        sudo docker build -t typejuggling_v2 $pwd/typeJuggling > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen TYPEJUGGLING_V22${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker TYPEJUGGLING_V2${endColour}"
        sudo docker run --name typejuggling_v2 -d -v $pwd/typeJuggling/src:/var/www/html -p 8008:80 typejuggling_v2 > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor TYPEJUGGLING_V2${endColour}"
            exit 1;
        fi    
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker TYPEJUGGLING_V2 iniciado correctamente${endColour}"

        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del RFI_V2${endColour}"
        sudo docker build -t rfi_v2 $pwd/rfi > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen RFI_V22${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker RFI_V2${endColour}"
        sudo docker run --name rfi_v2 -d -v $pwd/rfi/src:/var/www/html -p 8009:80 rfi_v2 > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor RFI_V2${endColour}"
            exit 1;
        fi 
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker RFI_V2 iniciado correctamente${endColour}"

        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del XSS_V2${endColour}"
        sudo docker build -t xss_v2 $pwd/xss > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen XSS_V22${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker XSS_V2${endColour}"
        sudo docker run --name xss_v2 -d -v $pwd/xss/src:/var/www/html -p 8004:80 xss_v2 > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor XSS_V2${endColour}"
            exit 1;
        fi 
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker XSS_V2 iniciado correctamente${endColour}"

        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del XXE_V2${endColour}"
        sudo docker build -t xxe_v2 $pwd/xxe > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen XXE_V22${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker XXE_V2${endColour}"
        sudo docker run --name xxe_v2 -d -v $pwd/xxe/src:/var/www/html -p 8003:80 xxe_v2 > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor XXE_V2${endColour}"
            exit 1;
        fi 
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker XXE_V2 iniciado correctamente${endColour}"

        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del BLIND_XXE_V2${endColour}"
        sudo docker build -t blindxxe_v2 $pwd/blindxxe > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen BLINDXXE_V2${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker BLIND_XXE_V2${endColour}"
        sudo docker run --name blindxxe_v2 -d -v $pwd/blindxxe/src:/var/www/html -p 8002:80 blindxxe_v2 > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor BLINDXXE_V2${endColour}"
            exit 1;
        fi 
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker BLIND_XXE_V2 iniciado correctamente${endColour}"

        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del CSRF_V2${endColour}"
        sudo docker build -t csrf_v2 $pwd/csrf > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen CSRF_V2${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker CSRF_V2${endColour}"
        sudo docker run --name csrf_v2 -d -v $pwd/csrf/src:/var/www/html -p 8001:80 csrf_v2 > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor CSRF_V2${endColour}"
            exit 1;
        fi 
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker CSRF_V2 iniciado correctamente${endColour}"

        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del DOMAIN_ZONE_TRANSFER_V2${endColour}"
        sudo docker build -t domainzonetransfer_v2 $pwd/domainZoneTransfer > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen DOMAIN_ZONE_TRANSFER_V2${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker DOMAIN_ZONE_TRANSFER_V2${endColour}"
        sudo docker run --name domainzonetransfer_v2 -d -p 53:53/udp -p 53:53/tcp domainzonetransfer_v2 > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor DOMAIN_ZONE_TRANSFER_V2${endColour}"
            exit 1;
        fi 
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker DOMAIN_ZONE_TRANSFER_V2 iniciado correctamente${endColour}"

        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del LATEX_INJECTION_V2${endColour}"
        sudo docker build -t latexinjection_v2 $pwd/LaTeXInjection > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen LATEX_INJECTION_V2${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker LATEX_INJECTION_V2${endColour}"
        sudo docker run --name latexinjection_v2 -d -v $pwd/LaTeXInjection/src:/var/www/html -p 8011:80 latexinjection_v2 > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor LATEX_INJECTION_V2${endColour}"
            exit 1;
        fi 
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker LATEX_INJECTION_V2 iniciado correctamente${endColour}"

        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del XPATH_INJECTION_V2${endColour}"
        sudo docker build -t xpathinjection_v2 $pwd/xpathinjection > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen XPATH_INJECTION_V2${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker XPATH_INJECTION_V2${endColour}"
        sudo docker run --name xpathinjection_v2 -d -v $pwd/xpathinjection/src:/var/www/html -p 8012:80 xpathinjection_v2 > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor XPATH_INJECTION_V2${endColour}"
            exit 1;
        fi 
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker XPATH_INJECTION_V2 iniciado correctamente${endColour}"

        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del SHELLSHOCK_V2${endColour}"
        sudo docker build -t shellshock_v2 $pwd/shellshock > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen SHELLSHOCK_V2${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker SHELLSHOCK_V2${endColour}"
        sudo docker run --name shellshock_v2 -d -v $pwd/shellshock/src:/var/www/html/website -p 8013:80 shellshock_v2 > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor SHELLSHOCK_V2${endColour}"
            exit 1;
        fi 
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker SHELLSHOCK_V2 iniciado correctamente${endColour}"

    fi
else

    if [ "$ignore_errors" = "s" ]; then
    
        # CONFIGURANDO CONTENEDORES (ignorando errores y mostrando output)

        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del LFI_V2${endColour}"
        sudo docker build -t lfi_v2 $pwd/lfi
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen LFI_V2${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        fi
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker LFI_V2${endColour}"
        sudo docker run --name lfi_v2 -d -v $pwd/lfi/src:/var/www/html -p 8000:80 lfi_v2
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor LFI_V2${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker LFI_V2 iniciado correctamente${endColour}"
        fi
    #--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------    
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del MENU_V2${endColour}"
        sudo docker build -t menu_v2 $pwd/menu
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen MENU_V2${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        fi
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker MENU_V2${endColour}"
        sudo docker run --name menu_v2 -d -v $pwd/menu/src:/var/www/html -p 8080:80 menu_v2
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor MENU_V2${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker MENU_V2 iniciado correctamente${endColour}"
        fi
    #-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- 
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del SQLI_V2${endColour}"
        sudo docker build -t sqli_v2 $pwd/sqli
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen SQLI_V2${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        fi
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker SQLI_V2 i SQLI_DB_V2${endColour}"
        sudo docker run --name sqli_db_v2 -e MYSQL_ROOT_PASSWORD=rootpassword -e MYSQL_DATABASE=database -e MYSQL_USER=usuario -e MYSQL_PASSWORD=contraseña -d mysql:5.7
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor SQLI_DB_V2${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker SQLI_DB_V2 iniciado correctamente${endColour}"
        fi                                                             
        sudo docker run --name sqli_v2 --link sqli_db_v2:db -p 8005:80 -v $pwd/sqli/src:/var/www/html/ -d sqli_v2
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor SQLI_V2${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker SQLI_V2 iniciado correctamente${endColour}"
        fi
    #--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del PADDING_V2${endColour}"
        sudo docker build -t padding_v2 $pwd/paddingOracleAttack
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen PADDING_V2${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        fi
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker PADDING_V2 i PADDING_DB_V2${endColour}"
        sudo docker run --name padding_db_v2 -e MYSQL_ROOT_PASSWORD=rootpassword -e MYSQL_DATABASE=database -e MYSQL_USER=usuario -e MYSQL_PASSWORD=contraseña -d mysql:5.7
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor PADDING_DB_V2${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker PADDING_DB_V2 iniciado correctamente${endColour}"
        fi                                                             
        sudo docker run --name padding_v2 --link padding_db_v2:db -p 8007:80 -v $pwd/paddingOracleAttack/src:/var/www/html/ -d padding_v2
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor PADDING_V2${endColour}"
        else 
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker PADDING_V2 iniciado correctamente${endColour}"
        fi
    #--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del TYPEJUGGLING_V2${endColour}"
        sudo docker build -t typejuggling_v2 $pwd/typeJuggling
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen TYPEJUGGLING_V22${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        fi
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker TYPEJUGGLING_V2${endColour}"
        sudo docker run --name typejuggling_v2 -d -v $pwd/typeJuggling/src:/var/www/html -p 8008:80 typejuggling_v2
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor TYPEJUGGLING_V2${endColour}"
        else    
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker TYPEJUGGLING_V2 iniciado correctamente${endColour}"
        fi
    #--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del RFI_V2${endColour}"
        sudo docker build -t rfi_v2 $pwd/rfi
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen RFI_V22${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        fi
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker RFI_V2${endColour}"
        sudo docker run --name rfi_v2 -d -v $pwd/rfi/src:/var/www/html -p 8009:80 rfi_v2
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor RFI_V2${endColour}"
        else 
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker RFI_V2 iniciado correctamente${endColour}"
        fi
    #--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del XSS_V2${endColour}"
        sudo docker build -t xss_v2 $pwd/xss
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen XSS_V22${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        fi
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker XSS_V2${endColour}"
        sudo docker run --name xss_v2 -d -v $pwd/xss/src:/var/www/html -p 8004:80 xss_v2
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor XSS_V2${endColour}"
        else 
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker XSS_V2 iniciado correctamente${endColour}"
        fi
    #--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del XXE_V2${endColour}"
        sudo docker build -t xxe_v2 $pwd/xxe
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen XXE_V22${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        fi
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker XXE_V2${endColour}"
        sudo docker run --name xxe_v2 -d -v $pwd/xxe/src:/var/www/html -p 8003:80 xxe_v2
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor XXE_V2${endColour}"
        else 
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker XXE_V2 iniciado correctamente${endColour}"
        fi
    #--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del BLIND_XXE_V2${endColour}"
        sudo docker build -t blindxxe_v2 $pwd/blindxxe
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen BLINDXXE_V2${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        fi
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker BLIND_XXE_V2${endColour}"
        sudo docker run --name blindxxe_v2 -d -v $pwd/blindxxe/src:/var/www/html -p 8002:80 blindxxe_v2
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor BLINDXXE_V2${endColour}"
        else 
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker BLIND_XXE_V2 iniciado correctamente${endColour}"
        fi
    #--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del CSRF_V2${endColour}"
        sudo docker build -t csrf_v2 $pwd/csrf
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen CSRF_V2${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        fi
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker CSRF_V2${endColour}"
        sudo docker run --name csrf_v2 -d -v $pwd/csrf/src:/var/www/html -p 8001:80 csrf_v2
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor CSRF_V2${endColour}"
        else 
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker CSRF_V2 iniciado correctamente${endColour}"
        fi
    #--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del DOMAIN_ZONE_TRANSFER_V2${endColour}"
        sudo docker build -t domainzonetransfer_v2 $pwd/domainZoneTransfer
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen DOMAIN_ZONE_TRANSFER_V2${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        fi
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker DOMAIN_ZONE_TRANSFER_V2${endColour}"
        sudo docker run --name domainzonetransfer_v2 -d -p 53:53/udp -p 53:53/tcp domainzonetransfer_v2
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor DOMAIN_ZONE_TRANSFER_V2${endColour}"
        else 
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker DOMAIN_ZONE_TRANSFER_V2 iniciado correctamente${endColour}"
        fi
    #--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del LATEX_INJECTION_V2${endColour}"
        sudo docker build -t latexinjection_v2 $pwd/LaTeXInjection
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen LATEX_INJECTION_V2${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        fi 
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker LATEX_INJECTION_V2${endColour}"
        sudo docker run --name latexinjection_v2 -d -v $pwd/LaTeXInjection/src:/var/www/html -p 8011:80 latexinjection_v2
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor LATEX_INJECTION_V2${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker LATEX_INJECTION_V2 iniciado correctamente${endColour}"
        fi
    #--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del XPATH_INJECTION_V2${endColour}"
        sudo docker build -t xpathinjection_v2 $pwd/xpathinjection
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen XPATH_INJECTION_V2${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        fi
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker XPATH_INJECTION_V2${endColour}"
        sudo docker run --name xpathinjection_v2 -d -v $pwd/xpathinjection/src:/var/www/html -p 8012:80 xpathinjection_v2
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor XPATH_INJECTION_V2${endColour}"
        else 
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker XPATH_INJECTION_V2 iniciado correctamente${endColour}"
        fi
    #--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del SHELLSHOCK_V2${endColour}"
        sudo docker build -t shellshock_v2 $pwd/shellshock
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen SHELLSHOCK_V2${endColour}"
        else
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        fi
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker SHELLSHOCK_V2${endColour}"
        sudo docker run --name shellshock_v2 -d -p 8013:80 shellshock_v2 #-v $pwd/shellshock/src:/var/www/html
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor SHELLSHOCK_V2${endColour}"
        else 
            echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker SHELLSHOCK_V2 iniciado correctamente${endColour}"
        fi
    else #--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

        # CONFIGURANDO CONTENEDORES (sin ignorar errores y mostrando output) -------------------------------------------------------------------------------------------------------------------------------------------------------

        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del LFI_V2${endColour}"
        sudo docker build -t lfi_v2 $pwd/lfi
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen LFI_V2${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker LFI_V2${endColour}"
        sudo docker run --name lfi_v2 -d -v $pwd/lfi/src:/var/www/html -p 8000:80 lfi_v2
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor LFI_V2${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker LFI_V2 iniciado correctamente${endColour}"

        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del MENU_V2${endColour}"
        sudo docker build -t menu_v2 $pwd/menu
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen MENU_V2${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker MENU_V2${endColour}"
        sudo docker run --name menu_v2 -d -v $pwd/menu/src:/var/www/html -p 8080:80 menu_v2
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor MENU_V2${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker MENU_V2 iniciado correctamente${endColour}"

        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del SQLI_V2${endColour}"
        sudo docker build -t sqli_v2 $pwd/sqli
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen SQLI_V2${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker SQLI_V2 i SQLI_DB_V2${endColour}"
        sudo docker run --name sqli_db_v2 -e MYSQL_ROOT_PASSWORD=rootpassword -e MYSQL_DATABASE=database -e MYSQL_USER=usuario -e MYSQL_PASSWORD=contraseña -d mysql:5.7
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor SQLI_DB_V2${endColour}"
            exit 1;
        fi                                                             
        sudo docker run --name sqli_v2 --link sqli_db_v2:db -p 8005:80 -v $pwd/sqli/src:/var/www/html/ -d sqli_v2
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor SQLI_V2${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker SQLI_V2 iniciado correctamente${endColour}"

        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del PADDING_V2${endColour}"
        sudo docker build -t padding_v2 $pwd/paddingOracleAttack
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen PADDING_V2${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker PADDING_V2 i PADDING_DB_V2${endColour}"
        sudo docker run --name padding_db_v2 -e MYSQL_ROOT_PASSWORD=rootpassword -e MYSQL_DATABASE=database -e MYSQL_USER=usuario -e MYSQL_PASSWORD=contraseña -d mysql:5.7
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor PADDING_DB_V2${endColour}"
            exit 1;
        fi                                                             
        sudo docker run --name padding_v2 --link padding_db_v2:db -p 8007:80 -v $pwd/paddingOracleAttack/src:/var/www/html/ -d padding_v2
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor PADDING_V2${endColour}"
            exit 1;
        fi 
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker PADDING_V2 iniciado correctamente${endColour}"

        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del TYPEJUGGLING_V2${endColour}"
        sudo docker build -t typejuggling_v2 $pwd/typeJuggling
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen TYPEJUGGLING_V22${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker TYPEJUGGLING_V2${endColour}"
        sudo docker run --name typejuggling_v2 -d -v $pwd/typeJuggling/src:/var/www/html -p 8008:80 typejuggling_v2
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor TYPEJUGGLING_V2${endColour}"
            exit 1;
        fi    
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker TYPEJUGGLING_V2 iniciado correctamente${endColour}"

        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del RFI_V2${endColour}"
        sudo docker build -t rfi_v2 $pwd/rfi
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen RFI_V22${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker RFI_V2${endColour}"
        sudo docker run --name rfi_v2 -d -v $pwd/rfi/src:/var/www/html -p 8009:80 rfi_v2
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor RFI_V2${endColour}"
            exit 1;
        fi 
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker RFI_V2 iniciado correctamente${endColour}"

        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del XSS_V2${endColour}"
        sudo docker build -t xss_v2 $pwd/xss
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen XSS_V22${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker XSS_V2${endColour}"
        sudo docker run --name xss_v2 -d -v $pwd/xss/src:/var/www/html -p 8004:80 xss_v2
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor XSS_V2${endColour}"
            exit 1;
        fi 
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker XSS_V2 iniciado correctamente${endColour}"

        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del XXE_V2${endColour}"
        sudo docker build -t xxe_v2 $pwd/xxe
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen XXE_V22${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker XXE_V2${endColour}"
        sudo docker run --name xxe_v2 -d -v $pwd/xxe/src:/var/www/html -p 8003:80 xxe_v2
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor XXE_V2${endColour}"
            exit 1;
        fi 
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker XXE_V2 iniciado correctamente${endColour}"

        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del BLIND_XXE_V2${endColour}"
        sudo docker build -t blindxxe_v2 $pwd/blindxxe
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen BLINDXXE_V2${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker BLIND_XXE_V2${endColour}"
        sudo docker run --name blindxxe_v2 -d -v $pwd/blindxxe/src:/var/www/html -p 8002:80 blindxxe_v2
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor BLINDXXE_V2${endColour}"
            exit 1;
        fi 
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker BLIND_XXE_V2 iniciado correctamente${endColour}"

        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del CSRF_V2${endColour}"
        sudo docker build -t csrf_v2 $pwd/csrf
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen CSRF_V2${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker CSRF_V2${endColour}"
        sudo docker run --name csrf_v2 -d -v $pwd/csrf/src:/var/www/html -p 8001:80 csrf_v2
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor CSRF_V2${endColour}"
            exit 1;
        fi 
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker CSRF_V2 iniciado correctamente${endColour}"

        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del DOMAIN_ZONE_TRANSFER_V2${endColour}"
        sudo docker build -t domainzonetransfer_v2 $pwd/domainZoneTransfer
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen DOMAIN_ZONE_TRANSFER_V2${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker DOMAIN_ZONE_TRANSFER_V2${endColour}"
        sudo docker run --name domainzonetransfer_v2 -d -p 53:53/udp -p 53:53/tcp domainzonetransfer_v2
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor DOMAIN_ZONE_TRANSFER_V2${endColour}"
            exit 1;
        fi 
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker DOMAIN_ZONE_TRANSFER_V2 iniciado correctamente${endColour}"

        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del LATEX_INJECTION_V2${endColour}"
        sudo docker build -t latexinjection_v2 $pwd/LaTeXInjection
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen LATEX_INJECTION_V2${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker LATEX_INJECTION_V2${endColour}"
        sudo docker run --name latexinjection_v2 -d -v $pwd/LaTeXInjection/src:/var/www/html -p 8011:80 latexinjection_v2
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor LATEX_INJECTION_V2${endColour}"
            exit 1;
        fi 
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker LATEX_INJECTION_V2 iniciado correctamente${endColour}"

        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del XPATH_INJECTION_V2${endColour}"
        sudo docker build -t xpathinjection_v2 $pwd/xpathinjection
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen XPATH_INJECTION_V2${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker XPATH_INJECTION_V2${endColour}"
        sudo docker run --name xpathinjection_v2 -d -v $pwd/xpathinjection/src:/var/www/html -p 8012:80 xpathinjection_v2
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor XPATH_INJECTION_V2${endColour}"
            exit 1;
        fi 
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker XPATH_INJECTION_V2 iniciado correctamente${endColour}"

        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del SHELLSHOCK_V2${endColour}"
        sudo docker build -t shellshock_v2 $pwd/shellshock
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al construir la imagen SHELLSHOCK_V2${endColour}"
            exit 1;
        fi
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
        echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker SHELLSHOCK_V2${endColour}"
        sudo docker run --name shellshock_v2 -d -v $pwd/shellshock/src:/var/www/html/website -p 8013:80 shellshock_v2
        if [ $? -ne 0 ]; then
            echo -e "\n${yellowColour}[${endColour}${redColour}+${endColour}${yellowColour}]${endColour} ${redColour}ERROR${endColour} ${grayColour}Error al iniciar el contenedor SHELLSHOCK_V2${endColour}"
            exit 1;
        fi 
        echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker SHELLSHOCK_V2 iniciado correctamente${endColour}"

    fi
fi