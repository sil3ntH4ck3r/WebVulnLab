import os
import tarfile
import time

def make_backup(src, dst):
    ts = int(time.time())
    archive = os.path.join(dst, f"logs-{ts}.tar.gz")
    with tarfile.open(archive, "w:gz") as tar:
        tar.add(src, arcname=os.path.basename(src))
    return archive
