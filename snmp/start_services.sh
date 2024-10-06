#!/bin/bash

# Iniciar Apache
service apache2 start

# Iniciar SNMPD
service snmpd start

# Iniciar servidor Python
python3 /usr/local/bin/simple_http_auth_server.py --bind :: --port 8080 --username admin --password P@ssw0rd &

# Iniciar BIND en primer plano
/usr/sbin/named -g