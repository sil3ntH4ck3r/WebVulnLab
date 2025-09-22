#!/usr/bin/env python3
import os
import time
from backup_utils import make_backup

def main():
    src = "/var/log"
    dst = "/var/backups/last"
    os.makedirs(dst, exist_ok=True)
    archive = make_backup(src, dst)
    print(f"[+] Backup creado: {archive}")

if __name__ == "__main__":
    main()
