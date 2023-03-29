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
sudo cp hack3nv.conf /etc/apache2/sites-available 
sudo a2ensite hack3nv.conf
sudo systemctl reload apache2

echo "127.0.0.1 lfi.local menu.local sqli.local paddingoracleattack.local" >> /etc/hosts

# CONFIGURANDO CONTENEDORES

echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del LFI_V2${endColour}"
sudo docker build -t lfi_v2 $pwd/lfi
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker LFI_V2${endColour}"
sudo docker run --name lfi_v2 -d -v $pwd/lfi/src:/var/www/html -p 8000:80 lfi_v2
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker LFI_V2 iniciado correctamente${endColour}"

echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del MENU_V2${endColour}"
sudo docker build -t menu_v2 $pwd/menu
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker MENU_V2${endColour}"
sudo docker run --name menu_v2 -d -v $pwd/menu/src:/var/www/html -p 8080:80 menu_v2
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker MENU_V2 iniciado correctamente${endColour}"

echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del SQLI_V2${endColour}"
sudo docker build -t sqli_v2 $pwd/sqli
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker SQLI_V2 i SQLI_DB_V2${endColour}"
sudo docker run --name sqli_db_v2 -e MYSQL_ROOT_PASSWORD=rootpassword -e MYSQL_DATABASE=database -e MYSQL_USER=usuario -e MYSQL_PASSWORD=contraseña -d mysql:5.7                                                             
sudo docker run --name sqli_v2 --link sqli_db_v2:db -p 8005:80 -v $pwd/sqli/src:/var/www/html/ -d sqli_v2
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker SQLI_V2 iniciado correctamente${endColour}"

echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del PADDING_V2${endColour}"
sudo docker build -t padding_v2 $pwd/paddingOracleAttack
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker PADDING_V2 i PADDING_DB_V2${endColour}"
sudo docker run --name padding_db_v2 -e MYSQL_ROOT_PASSWORD=rootpassword -e MYSQL_DATABASE=database -e MYSQL_USER=usuario -e MYSQL_PASSWORD=contraseña -d mysql:5.7                                                             
sudo docker run --name padding_v2 --link sqli_db_v2:db -p 8007:80 -v $pwd/paddingOracleAttack/src:/var/www/html/ -d padding_v2
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker PADDING_V2 iniciado correctamente${endColour}"

