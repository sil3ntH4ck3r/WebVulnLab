# Usamos la última versión de Ubuntu como base
FROM ubuntu:latest

# Actualizamos e instalamos las dependencias necesarias
RUN apt-get update && apt-get install -y wget software-properties-common gnupg2

# Descargamos y registramos la clave de Oracle
RUN wget -O- https://www.virtualbox.org/download/oracle_vbox_2016.asc | gpg --yes --output /usr/share/keyrings/oracle-virtualbox-2016.gpg --dearmor

# Añadimos el repositorio de VirtualBox
RUN echo "deb [arch=amd64 signed-by=/usr/share/keyrings/oracle-virtualbox-2016.gpg] https://download.virtualbox.org/virtualbox/debian $(lsb_release -cs) contrib" >> /etc/apt/sources.list.d/virtualbox.list

# Actualizamos e instalamos VirtualBox
RUN apt-get update && apt-get install -y virtualbox-6.1

# Descargamos la ISO de Windows Server 2016 Standard Evaluation
RUN mkdir /isos
ADD "https://go.microsoft.com/fwlink/p/?LinkID=2195174&clcid=0x40a&culture=es-es&country=ES" /isos/server2016.iso
