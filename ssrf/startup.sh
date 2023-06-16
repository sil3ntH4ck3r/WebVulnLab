#!/bin/bash

# Inicia el servidor Apache
apache2-foreground &

# Espera a que el servidor Apache esté disponible
sleep 5

# Ejecuta el servidor Python en segundo plano
python3 -m http.server 8080 --bind 127.0.0.1 &

# Espera a que los servicios estén en ejecución
wait