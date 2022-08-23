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

cd CSRF

echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del CSRF${endColour}"
sudo docker build -t csrf .
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker CSRF${endColour}"
docker run --name csrf -d -v $pwd/CSRF/src:/var/www/html -p 8003:80 csrf
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker CSRF iniciado correctamente${endColour}"

cd ../HTMLInjection

echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del HTML Injection${endColour}"
sudo docker build -t html_injection .
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker HTML Injection${endColour}"
docker run --name html_injection -d -v $pwd/HTMLInjection/src:/var/www/html -p 8001:80 html_injection
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker HTML Injection iniciado correctamente${endColour}"

cd ../LFI

echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del LFI${endColour}"
sudo docker build -t lfi .
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker LFI${endColour}"
docker run --name lfi -d -v $pwd/LFI/src:/var/www/html -p 8000:80 lfi
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker LFI iniciado correctamente${endColour}"

cd ../Server

echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del Servidor Principal${endColour}"
sudo docker build -t main_server .
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker Servidor Principal${endColour}"
docker run --name server -d -v $pwd/Server/src:/var/www/html -p 8080:80 main_server
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker Servidor Principal iniciado correctamente${endColour}"

cd ../SQLI

echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del SQL Injection${endColour}"
sudo docker build -t sqli .
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker SQL Injection${endColour}"
docker run --name sqli -d -v $pwd/SQLI/src:/var/www/html -p 8005:80 sqli
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker SQL Injection iniciado correctamente${endColour}"

cd ../SQLIBlind

echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del Blind SQL Injection${endColour}"
sudo docker build -t sqli_blind .
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker Blind SQL Injection${endColour}"
docker run --name sqli_blind -d -v $pwd/SQLIBlind/src:/var/www/html -p 8006:80 sqli_blind
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker Blind SQL Injection iniciado correctamente${endColour}"

cd ../SSRF
echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo imagen del SSRF${endColour}"
sudo docker build -t ssrf .
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Imagen construida correctamente${endColour}"
echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando docker SSRF${endColour}"
docker run --name ssrf -d -v $pwd/SSRF/src:/var/www/html -p 8004:80 ssrf
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Docker SSRF iniciado correctamente${endColour}"

# CONSTRUYENDO SERVIDOR LOCAL

echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Construyendo Tablero${endColour}"
sudo mv Tablero /var/www/html
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Tablero constuido correctamente${endColour}"
echo -e "\n${yellowColour}[${endColour}${blueColour}+${endColour}${yellowColour}]${endColour} ${blueColour}INFO${endColour} ${grayColour}Iniciando Tablero${endColour}"
service apache2 start
echo -e "\n${yellowColour}[${endColour}${greenColour}+${endColour}${yellowColour}]${endColour} ${greenColour}CORRECTO${endColour} ${grayColour}Tablero iniciado correctamente${endColour}"