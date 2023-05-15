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

echo "127.0.0.1 lfi.local menu.local sqli.local paddingoracleattack.local typejuggling.local rfi.local xss.local xxe.local blindxxe.local insecuredeserializationphp.local domainzonetransfer.local csrf.local" >> /etc/hosts

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
sudo docker run --name padding_v2 --link padding_db_v2:db -p 8007:80 -v $pwd/paddingOracleAttack/src:/var/www/html/ -d padding_v2
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker PADDING_V2 iniciado correctamente${endColour}"

echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del TYPEJUGGLING_V2${endColour}"
sudo docker build -t typejuggling_v2 $pwd/typeJuggling
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker TYPEJUGGLING_V2${endColour}"
sudo docker run --name typejuggling_v2 -d -v $pwd/typeJuggling/src:/var/www/html -p 8008:80 typejuggling_v2
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker TYPEJUGGLING_V2 iniciado correctamente${endColour}"

echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del RFI_V2${endColour}"
sudo docker build -t rfi_v2 $pwd/rfi
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker RFI_V2${endColour}"
sudo docker run --name rfi_v2 -d -v $pwd/rfi/src:/var/www/html -p 8009:80 rfi_v2
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker RFI_V2 iniciado correctamente${endColour}"

echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del RFI_V2${endColour}"
sudo docker build -t xss_v2 $pwd/xss
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker XSS_V2${endColour}"
sudo docker run --name xss_v2 -d -v $pwd/xss/src:/var/www/html -p 8004:80 xss_v2
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker XSS_V2 iniciado correctamente${endColour}"

echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del XXE_V2${endColour}"
sudo docker build -t xxe_v2 $pwd/xxe
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker XXE_V2${endColour}"
sudo docker run --name xxe_v2 -d -v $pwd/xxe/src:/var/www/html -p 8003:80 xxe_v2
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker XXE_V2 iniciado correctamente${endColour}"

echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del BLIND_XXE_V2${endColour}"
sudo docker build -t blindxxe_v2 $pwd/blindxxe
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker BLIND_XXE_V2${endColour}"
sudo docker run --name blindxxe_v2 -d -v $pwd/blindxxe/src:/var/www/html -p 8002:80 blindxxe_v2
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker BLIND_XXE_V2 iniciado correctamente${endColour}"

echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del CSRF_V2${endColour}"
sudo docker build -t csrf_v2 $pwd/csrf
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker CSRF_V2${endColour}"
sudo docker run --name csrf_v2 -d -v $pwd/csrf/src:/var/www/html -p 8001:80 csrf_v2
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker CSRF_V2 iniciado correctamente${endColour}"

echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del DOMAIN_ZONE_TRANSFER_V2${endColour}"
sudo docker build -t domainzonetransfer_v2 $pwd/domainZoneTransfer
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker DOMAIN_ZONE_TRANSFER_V2${endColour}"
sudo docker run --name domainzonetransfer_v2 -d -p 53:53/udp -p 53:53/tcp domainzonetransfer_v2
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker DOMAIN_ZONE_TRANSFER_V2 iniciado correctamente${endColour}"

echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del INSECURE_DESERIALIZATION_PHP_V2${endColour}"
sudo docker build -t insecuredeserializationphp_v2 $pwd/insecureDeserializationPhp
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker INSECURE_DESERIALIZATION_PHP_V2${endColour}"
sudo docker run --name insecuredeserializationphp_v2 -d -v $pwd/insecureDeserializationPhp/src:/var/www/html -p 8011:80 insecuredeserializationphp_v2
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker INSECURE_DESERIALIZATION_PHP_V2 iniciado correctamente${endColour}"