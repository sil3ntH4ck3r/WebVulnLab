#!/bin/bash

echo "Installing Windows Server 2016..."

# Create a blank disk image in the appropriate directory
qemu-img create -f vpc /diskimages/server2016.vhd 10G

# Boot QEMU with the ISO and blank disk image to install Windows Server
qemu-system-x86_64 -m 2G -smp 2 -boot d -drive file=/isos/server2016.iso,index=1,media=cdrom -drive file=/diskimages/server2016.vhd,index=0,media=disk,format=vpc -k en-us -rtc base=localtime -net nic,model=virtio -net user -drive file=Autounattend.xml,format=raw -serial mon:stdio

echo "Installation completed."
