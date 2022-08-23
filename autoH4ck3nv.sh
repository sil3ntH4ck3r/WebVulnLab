#!/bin/bash

pwd=$(pwd)

cd CSRF

sudo docker build -t csrf .

docker run --name csrf -d -v $pwd/CSRF/src:/var/www/html -p 8003:80 csrf

cd ../HTMLInjection

sudo docker build -t html_injection .

docker run --name html_injection -d -v $pwd/HTMLInjection/src:/var/www/html -p 8001:80 html_injection

cd ../LFI

sudo docker build -t lfi .

docker run --name lfi -d -v $pwd/LFI/src:/var/www/html -p 8000:80 lfi

cd ../Server

sudo docker build -t server .

docker run --name server -d -v $pwd/Server/src:/var/www/html -p 8080:80 server

cd ../SQLI

sudo docker build -t sqli .

docker run --name sqli -d -v $pwd/SQLI/src:/var/www/html -p 8005:80 sqli

cd ../SQLIBlind

sudo docker build -t sqli_blind .

docker run --name sqli_blind -d -v $pwd/SQLIBlind/src:/var/www/html -p 8006:80 sqli_blind

cd ../SSRF

sudo docker build -t sqli_blind .

docker run --name ssrf -d -v $pwd/SSRF/src:/var/www/html -p 8004:80 ssrf

# CONSTRUYENDO SERVIDOR LOCAL

sudo mv Tablero /var/www/html