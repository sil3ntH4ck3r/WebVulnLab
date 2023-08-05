#!/bin/bash

# Agregar la entrada al archivo /etc/hosts
echo "127.0.0.1 squidproxy.local" >> /etc/hosts

iptables -A INPUT -i lo -p tcp --dport 80 -j ACCEPT
iptables -A INPUT -p tcp --dport 80 -j DROP

# Iniciar Squid
service squid start

# Iniciar Apache en primer plano
exec apache2-foreground
