FROM ubuntu:latest

ENV DEBIAN_FRONTEND=noninteractive

# Actualizar repositorios e instalar paquetes necesarios
RUN apt-get update && \
    apt-get install -y apache2 php libapache2-mod-php php-snmp snmp snmpd snmp-mibs-downloader bind9 bind9utils bind9-doc python3 procps iptables

# Habilitar los MIBs
RUN sed -i 's/mibs :/# mibs :/' /etc/snmp/snmp.conf && \
    download-mibs

# Copiar archivos de configuración
COPY snmpd.conf /etc/snmp/snmpd.conf
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
COPY src/index.php /var/www/html/index.php

# Habilitar IPv6 en el contenedor
RUN echo "net.ipv6.conf.all.disable_ipv6=0" >> /etc/sysctl.conf && \
    echo "net.ipv6.conf.default.disable_ipv6=0" >> /etc/sysctl.conf && \
    sysctl -p

# Copiar el script Python
COPY simple_http_auth_server.py /usr/local/bin/simple_http_auth_server.py

# Exponer los puertos necesarios
EXPOSE 80 161/udp 8080

# Iniciar los servicios
COPY start_services.sh /start_services.sh
RUN chmod +x /start_services.sh
CMD ["/start_services.sh"]