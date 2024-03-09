#!/bin/bash

# Obtener la IP del contenedor gallery
GALLERY_IP=$(getent hosts gallery | awk '{ print $1 }')

# Agregar la entrada al archivo /etc/hosts
echo "$GALLERY_IP oauth.gallery.local" >> /etc/hosts
