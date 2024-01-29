FROM ubuntu:22.04

# User Settings for VNC
ENV USER=root
ENV PASSWORD=password1

# Variables for installation
ENV DEBIAN_FRONTEND=noninteractive
ENV DEBCONF_NONINTERACTIVE_SEEN=true

# Install dependencies and QEMU
RUN apt-get update && \
    apt-get install -y qemu-utils qemu-system-x86 wget

# Download Windows Server 2016 Standard Evaluation ISO
RUN mkdir /isos 

ADD "https://go.microsoft.com/fwlink/p/?LinkID=2195174&clcid=0x40a&culture=es-es&country=ES" /isos/server2016.iso

# Copy installation script
COPY install_windows.sh /
COPY Autounattend.xml /
RUN chmod +x /install_windows.sh

# Create a directory for disk images
RUN mkdir /diskimages

# Expose necessary ports
EXPOSE 80

# Start installation script
#ENTRYPOINT ["/install_windows.sh"]
