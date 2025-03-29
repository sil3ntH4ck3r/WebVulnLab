#!/bin/bash

if [ "$#" -ne 2 ]; then
    echo "Uso: $0 <container_id> <command>"
    exit 1
fi

container_id=$1
command=$2

#if ! docker ps --format '{{.Names}} | grep -w "$container_id > /dev/null; then
#    echo "Contenedor no encontrado: $container_id
#    exit 1
#fi

if [ "$command" != "/bin/bash" ]; then
    echo "Comando no permitido."
    exit 1
fi

docker exec -it "$container_id" "$command"
