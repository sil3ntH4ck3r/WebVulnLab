#!/usr/bin/env python3
# -*- coding: utf-8 -*-
import json
import os
import queue
import re as _re
import shutil
import subprocess
import sys
import threading
import time
from dataclasses import dataclass, asdict, field
from pathlib import Path
from typing import Callable, List, Dict, Optional
from collections import deque

import tkinter as tk
from tkinter import ttk, messagebox, filedialog

APP_NAME = "WebVulnLab Installer"
CONFIG_PATH = Path.home() / ".webvulnlab_ui.json"
LOG_DIR = Path.home() / ".webvulnlab"
LOG_PATH = LOG_DIR / "install.log"
DEFAULT_NETWORK_NAME = "WebVulnLab-Network"
DEFAULT_SUBNET_V4 = "172.18.0.0/16"
DOCKER_DAEMON_FILE = Path("/etc/docker/daemon.json")
SUDOERS_FILE = Path("/etc/sudoers.d/www-data-docker-exec")
WRAPPER_SCRIPT_DEST = Path("/usr/local/bin/docker_exec_wrapper.sh")

# ----------------------------- UTILIDAD -----------------------------

def require_root_or_exit():
    if os.geteuid() != 0:
        messagebox.showerror(APP_NAME, "Debes ejecutar este programa como root (sudo).")
        sys.exit(1)


def which(cmd: str) -> Optional[str]:
    return shutil.which(cmd)


def _sink_call(sink: Optional[Callable[[str], None]], text: str):
    """Llama al sink de forma segura. Si es None, no hace nada."""
    if sink is None:
        return
    try:
        sink(text)
    except Exception:
        pass


def run_cmd(cmd: List[str], hide_output: bool, log_sink: Optional[Callable[[str], None]] = None, cwd: Optional[str] = None) -> subprocess.CompletedProcess:
    """Ejecuta un comando devolviendo CompletedProcess, logueando en tiempo real si no se oculta salida.

    `log_sink` debe ser un callable thread-safe (por ejemplo, un `queue.Queue.put_nowait`).
    NO pasar nunca un widget de Tkinter: las llamadas se hacen desde un hilo secundario
    y provocarían un segmentation fault en macOS.
    """
    if not hide_output and log_sink is not None:
        _sink_call(log_sink, f"$ {' '.join(cmd)}\n")
    try:
        if hide_output:
            proc = subprocess.run(cmd, stdout=subprocess.PIPE, stderr=subprocess.STDOUT, text=True, cwd=cwd)
            append_log(proc.stdout)
            if not hide_output and log_sink is not None:
                _sink_call(log_sink, proc.stdout)
            return proc
        else:
            # Importante: NO usar bufsize=1 con text=True. En macOS con Tcl/Tk de Apple
            # esa combinación puede provocar SIGSEGV al leer el stdout del subproceso.
            with subprocess.Popen(cmd, stdout=subprocess.PIPE, stderr=subprocess.STDOUT, text=True, cwd=cwd) as p:
                out_lines = []
                for line in p.stdout:
                    out_lines.append(line)
                    _sink_call(log_sink, line)
                p.wait()
                out = ''.join(out_lines)
                append_log(out)
                return subprocess.CompletedProcess(cmd, p.returncode, out, None)
    except FileNotFoundError:
        msg = f"Comando no encontrado: {cmd[0]}\n"
        append_log(msg)
        _sink_call(log_sink, msg)
        return subprocess.CompletedProcess(cmd, 127, msg, None)


def append_log(text: str):
    try:
        LOG_PATH.parent.mkdir(parents=True, exist_ok=True)
        with open(LOG_PATH, 'a', encoding='utf-8') as f:
            ts = time.strftime('%Y-%m-%d %H:%M:%S')
            for line in text.splitlines():
                f.write(f"[{ts}] {line}\n")
    except Exception:
        pass

class ToolTip:
    def __init__(self, widget, text: str):
        self.widget = widget
        self.text = text
        self.tip = None
        widget.bind("<Enter>", self._show)
        widget.bind("<Leave>", self._hide)

    def _show(self, _evt=None):
        if self.tip:
            return
        x = self.widget.winfo_rootx() + 20
        y = self.widget.winfo_rooty() + self.widget.winfo_height() + 5
        self.tip = tw = tk.Toplevel(self.widget)
        tw.wm_overrideredirect(True)
        tw.wm_geometry(f"+{x}+{y}")
        lbl = tk.Label(tw, text=self.text, justify='left',
                       relief='solid', borderwidth=1, background="#ffffe0")
        lbl.pack(ipadx=4, ipady=2)

    def _hide(self, _evt=None):
        if self.tip:
            self.tip.destroy()
            self.tip = None

def add_tooltip(widget, text: str):
    try:
        ToolTip(widget, text)
    except Exception:
        pass


def log_widget_insert(widget: tk.Text, text: str):
    widget.configure(state='normal')
    widget.insert('end', text)
    widget.see('end')
    widget.configure(state='disabled')


# ---------------------- DATOS DE CONTENEDORES ----------------------

@dataclass
class ComposeDef:
    vuln: str
    compose_path: str
    enabled: bool = False

    def to_row(self):
        return (self.vuln, self.compose_path, 'sí' if self.enabled else 'no')

@dataclass
class ContainerDef:
    name: str
    path: str
    ports: str = ""
    options: str = ""
    static_ip: str = ""
    enabled: bool = False

    def to_row(self):
        return (self.name, self.path, self.ports, self.options, self.static_ip, 'sí' if self.enabled else 'no')

def default_compose(base_dir: Path) -> List[ComposeDef]:
    P = str(base_dir)
    return [
        ComposeDef("AWS Abuse", f"{P}/aws/docker-compose.yml", False),
        ComposeDef("LDAP Injection", f"{P}/ldapinjection/docker-compose.yml", False),
        ComposeDef("API Abuse", f"{P}/apiabuse/docker-compose.yml", False),
        ComposeDef("GraphQL", f"{P}/graphql/docker-compose.yml", False),
        ComposeDef("OAuth", f"{P}/oauth/docker-compose.yml", False)
    ]

def default_containers(base_dir: Path) -> List[ContainerDef]:
    P = str(base_dir)
    items = [
        ContainerDef("menu_v2", f"{P}/menu", "8080:80", "--add-host=menu.local:172.18.0.2", "172.18.0.2", False),
        ContainerDef("lfi_v2", f"{P}/lfi", "8000:80", "", "172.18.0.3", False),
        ContainerDef("csrf_v2", f"{P}/csrf", "8001:80", "", "172.18.0.4", False),
        ContainerDef("blindxxe_v2", f"{P}/blindxxe", "8002:80", "", "172.18.0.5", False),
        ContainerDef("xxe_v2", f"{P}/xxe", "8003:80", "", "172.18.0.6", False),
        ContainerDef("xss_v2", f"{P}/xss", "8004:80", "", "172.18.0.7", False),
        ContainerDef("sqli_v2", f"{P}/sqli", "8005:80", "", "172.18.0.8", False),
        ContainerDef("domainzonetransfer_v2", f"{P}/domainzonetransfer", "8039:80", "", "172.18.0.9", False),
        ContainerDef("ssrf_v2", f"{P}/ssrf", "8006:80", "", "172.18.0.10", False),
        ContainerDef("paddingoracleattack_v2", f"{P}/paddingoracleattack", "8007:80", "", "172.18.0.11", False),
        ContainerDef("typejuggling_v2", f"{P}/typejuggling", "8008:80", "", "172.18.0.12", False),
        ContainerDef("rfi_v2", f"{P}/rfi", "8009:80", "", "172.18.0.13", False),
        ContainerDef("insecuredeseralizationphp_v2", f"{P}/insecuredeseralizationphp", "8010:80", "", "172.18.0.14", False),
        ContainerDef("latexinjection_v2", f"{P}/latexinjection", "8011:80", "", "172.18.0.15", False),
        ContainerDef("xpathinjection_v2", f"{P}/xpathinjection", "8012:80", "", "172.18.0.16", False),
        ContainerDef("shellshock_v2", f"{P}/shellshock", "8013:80", "", "172.18.0.17", False),
        ContainerDef("blindsqli_v2", f"{P}/blindsqli", "8014:80", "", "172.18.0.18", False),
        ContainerDef("blindxss_v2", f"{P}/blindxss", "8015:80", "", "172.18.0.19", False),
        ContainerDef("htmlinjection_v2", f"{P}/htmlinjection", "8016:80", "", "172.18.0.20", False),
        ContainerDef("idor_v2", f"{P}/idor", "8017:80", "", "172.18.0.21", False),
        ContainerDef("ssti_v2", f"{P}/ssti", "8018:80", "", "172.18.0.22", False),
        ContainerDef("csti_v2", f"{P}/csti", "8019:80", "", "172.18.0.23", False),
        ContainerDef("nosqlinjection_v2", f"{P}/nosqlinjection", "8020:80", "", "172.18.0.24", False),
        ContainerDef("fileuploadabuse_v2", f"{P}/fileuploadabuse", "8024:80", "", "172.18.0.25", False),
        ContainerDef("prototypepollution_v2", f"{P}/prototypepollution", "8025:80", "", "172.18.0.26", False),
        ContainerDef("openredirect_v2", f"{P}/openredirect", "8026:80", "", "172.18.0.27", False),
        ContainerDef("webdav_v2", f"{P}/webdav", "8027:80", "", "172.18.0.28", False),
        ContainerDef("squidproxy_v2", f"{P}/squidproxy", "8028:80", "--cap-add=NET_ADMIN", "172.18.0.29", False),
        ContainerDef("cors_v2", f"{P}/cors", "8029:80", "", "172.18.0.30", False),
        ContainerDef("sqltruncation_v2", f"{P}/sqltruncation", "8030:80", "", "172.18.0.31", False),
        ContainerDef("jwt_v2", f"{P}/jwt", "8032:80", "", "172.18.0.32", False),
        ContainerDef("racecondition_v2", f"{P}/racecondition", "8033:80", "", "172.18.0.33", False),
        ContainerDef("cssi_v2", f"{P}/cssi", "8034:80", "", "172.18.0.34", False),
        ContainerDef("yamldeseralization_v2", f"{P}/yamldeseralization", "8042:80", "", "172.18.0.35", False),
        ContainerDef("pickledeseralization_v2", f"{P}/pickledeseralization", "8038:80", "", "172.18.0.36", False),
        ContainerDef("snmp_v2", f"{P}/snmp", "8040:80 161:161/udp", "--sysctl net.ipv6.conf.all.disable_ipv6=0 --sysctl net.ipv6.conf.default.disable_ipv6=0", "172.18.0.37", False),
        ContainerDef("http3_v2", f"{P}/http3", "", "", "172.18.0.38", False),
        ContainerDef("httpsmuggling_v2", f"{P}/httpsmuggling", "8043:80", "", "172.18.0.39", False),
        ContainerDef("sessionpuzzling_v2", f"{P}/sessionpuzzling", "8031:80", "", "172.18.0.40", False),
        ContainerDef("redis_v2", f"{P}/redis", "8044:80", "", "172.18.0.54", False),
        ContainerDef("nodejsdeserelization_v2", f"{P}/nodejsdeserelization", "8045:80", "", "172.18.0.55", False),
        ContainerDef("esiinjection_v2", f"{P}/esiinjection", "8046:8080", "", "172.18.0.56", False),
        ContainerDef("cypherinjection_v2", f"{P}/cypherinjection", "8047:80", "", "172.18.0.57", False),
        ContainerDef("javadeserelization_v2", f"{P}/javadeserelization", "8048:80", "", "172.18.0.58", False),
        ContainerDef("jndiinjection_v2", f"{P}/jndiinjection", "8049:80", "", "172.18.0.59", False),
        ContainerDef("webcachepoisoning_v2", f"{P}/webcachepoisoning", "8050:80", "", "172.18.0.60", False),
        # Escalada de privilegios
        ContainerDef("sudoers_v2", f"{P}/sudoers", "8051:22", "", "172.18.0.61", False),
        ContainerDef("suid_v2", f"{P}/suid", "8052:22", "", "172.18.0.62", False),
        ContainerDef("cronjob_v2", f"{P}/cronjob", "8053:22", "", "172.18.0.63", False),
        ContainerDef("pathhijacking_v2", f"{P}/pathhijacking", "8054:22", "", "172.18.0.64", False),
        ContainerDef("pythonlibraryhijacking_v2", f"{P}/pythonlibraryhijacking", "8055:22", "", "172.18.0.65", False),
        ContainerDef("capabilities_v2", f"{P}/capabilities", "8056:22", "", "172.18.0.66", False),
        ContainerDef("specialgroups_v2", f"{P}/specialgroups", "8057:22", "--privileged --cgroupns=host --security-opt apparmor=unconfined --security-opt seccomp=unconfined --device /dev/fuse", "172.18.0.67", False),
        ContainerDef("serviceabuse_v2", f"{P}/serviceabuse", "8058:22", "--privileged", "172.18.0.68", False),
        ContainerDef("specificbinaries_v2", f"{P}/specificbinaries", "8059:22", "--privileged", "172.18.0.69", False),
        ContainerDef("uuid_v2", f"{P}/uuid", "8060:80", "", "172.18.0.70", False),
        ContainerDef("rsqli_v2", f"{P}/rsqli", "8061:80", "", "172.18.0.71", False),
	#ContainerDef("wasmoob_v2", f"{P}/wasmoob", "8062:80", "", "172.18.0.72", False),
        #ContainerDef("wasoverflow_v2", f"{P}/wasoverflow", "8063:80", "", "172.18.0.73", False),
	#ContainerDef("crlfinjection_v2", f"{P}/crlfinjection", "8064:80", "", "172.18.0.74", False),
    ]
    return items


# --------------------------- CONFIGURACIÓN ---------------------------

@dataclass
class AppConfig:
    hide_output: bool = False
    ignore_errors: bool = False
    stop_after_start: bool = True
    auto_kill_ports: bool = False
    network_name: str = DEFAULT_NETWORK_NAME
    subnet_v4: str = DEFAULT_SUBNET_V4
    enable_ipv6: bool = True
    generate_ula_if_no_global: bool = True
    max_subnets: int = 100
    container_defs: List[ContainerDef] = field(default_factory=list)
    compose_defs: List[ComposeDef] = field(default_factory=list)

    extra_hosts: List[Dict[str, str]] = field(default_factory=lambda: [
        {"ip": "172.18.0.46", "domain": "aws.local"},
        {"ip": "172.18.0.47", "domain": "ldapinjection.local"},
        {"ip": "172.18.0.44", "domain": "mail.local"},
        {"ip": "172.18.0.43", "domain": "apiabuse.local"},
        {"ip": "172.18.0.49", "domain": "graphql.local"},
        {"ip": "172.18.0.53", "domain": "oauth_gallery.local"},
        {"ip": "172.18.0.52", "domain": "oauth_printing.local"},
    ])

    def to_json(self) -> dict:
        d = asdict(self)
        d['container_defs'] = [asdict(c) for c in self.container_defs]
        d['compose_defs'] = [asdict(c) for c in self.compose_defs]
        return d

    @classmethod
    def from_json(cls, data: dict) -> 'AppConfig':
        cfg = cls()
        cfg.hide_output = data.get('hide_output', True)
        cfg.ignore_errors = data.get('ignore_errors', False)
        cfg.stop_after_start = data.get('stop_after_start', False)
        cfg.auto_kill_ports = data.get('auto_kill_ports', False)
        cfg.network_name = data.get('network_name', DEFAULT_NETWORK_NAME)
        cfg.subnet_v4 = data.get('subnet_v4', DEFAULT_SUBNET_V4)
        cfg.enable_ipv6 = data.get('enable_ipv6', True)
        cfg.generate_ula_if_no_global = data.get('generate_ula_if_no_global', True)
        cfg.max_subnets = int(data.get('max_subnets', 100))
        cfg.extra_hosts = data.get('extra_hosts', [])
        cfg.container_defs = [ContainerDef(**c) for c in data.get('container_defs', [])]
        cfg.compose_defs = [ComposeDef(**c) for c in data.get('compose_defs', [])]
        return cfg


# ---------------------------- LÓGICA CORE ----------------------------

class Core:
    def __init__(self, cfg: AppConfig, log_sink: Optional[Callable[[str], None]] = None):
        self.cfg = cfg
        # `log_sink` es un callable thread-safe (queue.put_nowait o similar).
        # NUNCA un widget Tk: los métodos de Core se ejecutan en un hilo
        # secundario y acceder a Tk desde ahí provoca segfaults en macOS.
        self.log_sink = log_sink

    def install_docker_official(self):
        append_log("Instalando Docker (repositorio oficial)...")
        cmds = [
            ["bash", "-lc", "for pkg in docker.io docker-doc docker-compose podman-docker containerd runc; do apt-get remove -y \"$pkg\" || true; done"],
            ["apt-get", "update"],
            ["apt-get", "install", "-y", "ca-certificates", "curl"],
            ["bash", "-lc", "install -m 0755 -d /etc/apt/keyrings"],
            ["bash", "-lc", "curl -fsSL https://download.docker.com/linux/debian/gpg -o /etc/apt/keyrings/docker.asc"],
            ["bash", "-lc", "chmod a+r /etc/apt/keyrings/docker.asc"],
            ["bash", "-lc", "codename=$( . /etc/os-release; if [ \"$VERSION_CODENAME\" = \"kali-rolling\" ] || [ -z \"$VERSION_CODENAME\" ]; then echo bookworm; else echo \"$VERSION_CODENAME\"; fi ); " "echo \"deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/debian ${codename} stable\" | tee /etc/apt/sources.list.d/docker.list > /dev/null"],
            ["apt-get", "update"],
            ["apt-get", "install", "-y", "docker-ce", "docker-ce-cli", "containerd.io", "docker-buildx-plugin", "docker-compose-plugin"],
        ]
        for c in cmds:
            cp = run_cmd(c, self.cfg.hide_output, self.log_sink)
            if cp.returncode != 0:
                raise RuntimeError("Fallo instalando Docker")
        run_cmd(["systemctl", "enable", "--now", "docker"], self.cfg.hide_output, self.log_sink)

    def install_packages(self, packages: List[str]):
        if not packages:
            return
        cmd = ["apt-get", "update"]
        run_cmd(cmd, self.cfg.hide_output, self.log_sink)
        run_cmd(["apt-get", "install", "-y"] + packages, self.cfg.hide_output, self.log_sink)

    def ensure_ttyd(self):
        if which("ttyd"):
            append_log("ttyd ya instalado.\n")
            return
        append_log("Compilando e instalando ttyd...")
        run_cmd(["apt-get", "update"], self.cfg.hide_output, self.log_sink)
        self.install_packages(["build-essential", "cmake", "git", "libjson-c-dev", "libwebsockets-dev"]) 
        tmpdir = "/tmp/ttyd-build"
        shutil.rmtree(tmpdir, ignore_errors=True)
        os.makedirs(tmpdir, exist_ok=True)
        run_cmd(["git", "clone", "https://github.com/tsl0922/ttyd.git", tmpdir], self.cfg.hide_output, self.log_sink)
        os.makedirs(f"{tmpdir}/build", exist_ok=True)
        run_cmd(["cmake", ".."], self.cfg.hide_output, self.log_sink, cwd=f"{tmpdir}/build")
        cp = run_cmd(["make"], self.cfg.hide_output, self.log_sink, cwd=f"{tmpdir}/build")
        if cp.returncode != 0:
            raise RuntimeError("Error compilando ttyd")
        run_cmd(["make", "install"], self.cfg.hide_output, self.log_sink, cwd=f"{tmpdir}/build")

    def start_docker_service(self):
        run_cmd(["systemctl", "start", "docker"], self.cfg.hide_output, self.log_sink)

    def _detect_global_prefix(self) -> Optional[str]:
        cp = run_cmd(["bash", "-lc", "ip -6 addr show scope global | grep -oP 'inet6 \\K[^/]+(?=/)' || true"], True, self.log_sink)
        addrs = [a.strip() for a in cp.stdout.splitlines() if a.strip()]
        for addr in addrs:
            parts = addr.split(":")
            if len(parts) >= 3:
                return f"{parts[0]}:{parts[1]}:{parts[2]}:"
        return None

    def _generate_ula_prefix(self) -> str:
        cp = run_cmd(["bash", "-lc", "openssl rand -hex 5"], True, self.log_sink)
        gid = re.sub(r"[^0-9a-fA-F]", "", (cp.stdout or "")).lower()[:10].ljust(10, "0")
        return f"fd{gid[0:2]}:{gid[2:6]}:{gid[6:10]}:"

    def _is_subnet_in_use(self, subnet: str) -> bool:
        needle = subnet.split("/")[0]
        cp = run_cmd(["bash", "-lc", f"ip -6 addr show | grep -q '{needle}' && echo USED || echo FREE"], True, self.log_sink)
        return "USED" in cp.stdout

    def _find_free_subnet(self, base_prefix: str) -> Optional[str]:
        for i in range(1, self.cfg.max_subnets + 1):
            subnet = f"{base_prefix}{i}::/64"
            if not self._is_subnet_in_use(subnet):
                return subnet
        return None

    def configure_docker_ipv6(self):
        append_log("Configurando IPv6 en /etc/docker/daemon.json ...\n")
        ipv6_exists = False
        fixed_exists = False
        if DOCKER_DAEMON_FILE.exists() and DOCKER_DAEMON_FILE.stat().st_size > 0:
            try:
                with open(DOCKER_DAEMON_FILE, 'r', encoding='utf-8') as f:
                    data = json.load(f)
                ipv6_exists = 'ipv6' in data
                fixed_exists = 'fixed-cidr-v6' in data
            except Exception:
                raise RuntimeError("daemon.json contiene JSON inválido; corrígelo manualmente.")
        if ipv6_exists and fixed_exists:
            append_log("IPv6 ya configurado; no se realizan cambios.\n")
            return
        base = self._detect_global_prefix()
        if not base and self.cfg.generate_ula_if_no_global:
            base = self._generate_ula_prefix()
            append_log(f"Prefijo ULA generado: {base}:/48\n")
        if not base:
            raise RuntimeError("No hay prefijo IPv6 global y la generación de ULA está desactivada.")
        free_subnet = self._find_free_subnet(base)
        if not free_subnet:
            raise RuntimeError(f"No hay subredes libres dentro de {base}")
        append_log(f"Subred IPv6 elegida: {free_subnet}\n")
        backup = None
        if DOCKER_DAEMON_FILE.exists() and DOCKER_DAEMON_FILE.stat().st_size > 0:
            backup = Path(str(DOCKER_DAEMON_FILE) + ".bak_" + time.strftime('%F_%T'))
            shutil.copy2(DOCKER_DAEMON_FILE, backup)
            append_log(f"Backup creado: {backup}\n")
            with open(DOCKER_DAEMON_FILE, 'r', encoding='utf-8') as f:
                data = json.load(f)
        else:
            data = {}
        data["ipv6"] = True
        data["fixed-cidr-v6"] = free_subnet
        tmp = Path("/tmp/daemon.json.tmp")
        with open(tmp, 'w', encoding='utf-8') as f:
            json.dump(data, f, indent=2)
        shutil.move(tmp, DOCKER_DAEMON_FILE)
        cp = run_cmd(["systemctl", "restart", "docker"], self.cfg.hide_output, self.log_sink)
        if cp.returncode != 0 and backup:
            shutil.copy2(backup, DOCKER_DAEMON_FILE)
            run_cmd(["systemctl", "restart", "docker"], self.cfg.hide_output, self.log_sink)
            raise RuntimeError("No se pudo reiniciar Docker; restaurado backup.")
        append_log("Docker reiniciado; IPv6 aplicado.\n")

    def ensure_network(self):
        append_log(f"Creando red {self.cfg.network_name} con subnet {self.cfg.subnet_v4} (si no existe)\n")
        run_cmd(["bash", "-lc", f"docker network inspect {self.cfg.network_name} >/dev/null 2>&1 || docker network create --subnet={self.cfg.subnet_v4} {self.cfg.network_name}"], self.cfg.hide_output, self.log_sink)

    def generate_http3_certs(self):
        cert = "/etc/ssl/certs/http3.local.crt"
        key = "/etc/ssl/private/http3.local.key"
        if os.path.exists(cert) and os.path.exists(key):
            append_log("Certificados http3.local ya existen.\n")
            return
        os.makedirs("/etc/ssl/certs", exist_ok=True)
        os.makedirs("/etc/ssl/private", exist_ok=True)
        cp = run_cmd(["bash", "-lc", f"openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout {key} -out {cert} -subj '/CN=http3.local'"], self.cfg.hide_output, self.log_sink)
        if cp.returncode == 0:
            append_log(f"Certificados generados en {cert} y {key}.\n")
        else:
            raise RuntimeError("Error generando certificados http3.local")

    def generate_menu_local_certs(self):
        cert = "/etc/ssl/certs/menu.local.crt"
        key = "/etc/ssl/private/menu.local.key"
        if not which("mkcert"):
            append_log("Instalando mkcert...\n")
            self.install_packages(["libnss3-tools", "wget", "ca-certificates", "curl"])
            install_script = (
                "set -e;"
                "ver=$(curl -fsSL https://api.github.com/repos/FiloSottile/mkcert/releases/latest "
                "| grep -Po '\"tag_name\"\\s*:\\s*\"\\K[^\"]+' || echo v1.4.4);"
                "url=https://github.com/FiloSottile/mkcert/releases/download/$ver/mkcert-$ver-linux-amd64;"
                "rm -f /usr/local/bin/mkcert;"
                "curl -fL -o /usr/local/bin/mkcert \"$url\" || wget -qO /usr/local/bin/mkcert \"$url\";"
                "chmod +x /usr/local/bin/mkcert;"
                "hash -r; command -v mkcert && mkcert -version"
            )
            run_cmd(["bash", "-lc", install_script], self.cfg.hide_output, self.log_sink)
        run_cmd(["bash", "-lc", "mkcert -install"], self.cfg.hide_output, self.log_sink)
        if os.path.exists(cert):
            cp = run_cmd(["bash", "-lc", f"openssl x509 -enddate -noout -in {cert} | cut -d= -f2"], True, self.log_sink)
            exp = cp.stdout.strip()
            if exp:
                cp2 = run_cmd(["bash", "-lc", f"date --date='{exp}' +%s"], True, self.log_sink)
                exp_ts = int((cp2.stdout or "0").strip() or 0)
                now_ts = int(time.time())
                if exp_ts > now_ts:
                    append_log(f"Certificado válido hasta {exp}. Nada que hacer.\n")
                    return
                else:
                    append_log(f"Certificado caducado ({exp}). Regenerando...\n")
        run_cmd(["mkcert", "-install"], self.cfg.hide_output, self.log_sink)
        cp = run_cmd(["bash", "-lc", "mkcert -CAROOT"], True, self.log_sink)
        caroot = cp.stdout.strip()
        run_cmd(["bash", "-lc", f"cp '{caroot}/rootCA.pem' /usr/local/share/ca-certificates/mkcert-rootCA.crt && update-ca-certificates -q"], self.cfg.hide_output, self.log_sink)
        run_cmd(["bash", "-lc", "mkdir -p /etc/pki/nssdb && [[ -f /etc/pki/nssdb/key4.db ]] || certutil -N -d sql:/etc/pki/nssdb -f /dev/null"], self.cfg.hide_output, self.log_sink)
        run_cmd(["bash", "-lc", f"certutil -A -d sql:/etc/pki/nssdb -n 'mkcert development CA' -t 'CT,,' -i '{caroot}/rootCA.pem' -f /dev/null || true"], self.cfg.hide_output, self.log_sink)
        sudo_user = os.environ.get("SUDO_USER")
        if sudo_user and sudo_user != "root":
            cp2 = run_cmd(["bash", "-lc", f"getent passwd {sudo_user} | cut -d: -f6"], True, self.log_sink)
            userhome = cp2.stdout.strip()
            userdb = f"sql:{userhome}/.pki/nssdb"
            run_cmd(["bash", "-lc", f"mkdir -p '{userhome}/.pki/nssdb' && certutil -N -d '{userdb}' -f /dev/null 2>/dev/null || true"], self.cfg.hide_output, self.log_sink)
            run_cmd(["bash", "-lc", f"certutil -A -d '{userdb}' -n 'mkcert development CA' -t 'CT,,' -i '{caroot}/rootCA.pem' -f /dev/null || true"], self.cfg.hide_output, self.log_sink)
            run_cmd(["chown", "-R", f"{sudo_user}:{sudo_user}", f"{userhome}/.pki/nssdb"], self.cfg.hide_output, self.log_sink)
        os.makedirs(os.path.dirname(cert), exist_ok=True)
        os.makedirs(os.path.dirname(key), exist_ok=True)
        run_cmd(["mkcert", "-cert-file", cert, "-key-file", key, "menu.local", "127.0.0.1", "::1"], self.cfg.hide_output, self.log_sink)
        append_log(f"Certificado menu.local listo en {cert} y {key}\n")

    def remove_menu_local_certs(self):
        cert = "/etc/ssl/certs/menu.local.crt"
        key = "/etc/ssl/private/menu.local.key"
        for f in [cert, key]:
            try:
                os.remove(f)
                append_log(f"Eliminado {f}\n")
            except FileNotFoundError:
                pass
        if which("mkcert"):
            run_cmd(["bash", "-lc", "rm -f /usr/local/share/ca-certificates/mkcert-rootCA.crt && update-ca-certificates -q"], self.cfg.hide_output, self.log_sink)
            run_cmd(["bash", "-lc", "certutil -d sql:/etc/pki/nssdb -D -n 'mkcert development CA' -f /dev/null || true"], self.cfg.hide_output, self.log_sink)
            sudo_user = os.environ.get("SUDO_USER")
            if sudo_user and sudo_user != "root":
                cp2 = run_cmd(["bash", "-lc", f"getent passwd {sudo_user} | cut -d: -f6"], True, self.log_sink)
                userhome = cp2.stdout.strip()
                userdb = f"sql:{userhome}/.pki/nssdb"
                run_cmd(["bash", "-lc", f"certutil -d '{userdb}' -D -n 'mkcert development CA' -f /dev/null || true"], self.cfg.hide_output, self.log_sink)

    def configure_terminal_wrapper(self):
        src = Path.cwd() / "docker_exec_wrapper.sh"
        if not src.exists():
            append_log("docker_exec_wrapper.sh no existe en el directorio actual.\n")
            raise RuntimeError("Falta docker_exec_wrapper.sh")
        if (not WRAPPER_SCRIPT_DEST.exists()) or not filecmp_safe(src, WRAPPER_SCRIPT_DEST):
            shutil.copy2(src, WRAPPER_SCRIPT_DEST)
            append_log(f"Copiado wrapper a {WRAPPER_SCRIPT_DEST}\n")
        os.chmod(WRAPPER_SCRIPT_DEST, 0o755)
        run_cmd(["chown", "root:root", str(WRAPPER_SCRIPT_DEST)], self.cfg.hide_output, self.log_sink)
        if not SUDOERS_FILE.exists():
            SUDOERS_FILE.write_text(f"www-data ALL=(ALL) NOPASSWD: {WRAPPER_SCRIPT_DEST}\n", encoding='utf-8')
            os.chmod(SUDOERS_FILE, 0o440)
            append_log(f"Sudoers creado en {SUDOERS_FILE}\n")
        else:
            append_log("Sudoers ya presente.\n")

    def build_local_server(self, tablero_src: Path):
        if not tablero_src.exists():
            raise RuntimeError(f"No existe {tablero_src}")
        dst = Path("/var/www/html") / tablero_src.name
        run_cmd(["bash", "-lc", f"rm -rf '{dst}' && cp -R '{tablero_src}' '/var/www/html/'"], self.cfg.hide_output, self.log_sink)
        run_cmd(["bash", "-lc", r"sed -i 's/\-\-containerd=\/run\/containerd\/containerd.sock/\-H=tcp:\/\/0.0.0.0:2375/' /lib/systemd/system/docker.service"], self.cfg.hide_output, self.log_sink)
        run_cmd(["systemctl", "daemon-reload"], self.cfg.hide_output, self.log_sink)
        run_cmd(["systemctl", "restart", "docker"], self.cfg.hide_output, self.log_sink)
        cp = run_cmd(["bash", "-lc", "php -v | sed -nr 's/PHP[[:space:]]+([0-9]+\\.[0-9]+).*/\\1/p'"], True, self.log_sink)
        version = (cp.stdout.strip() or "8.2")
        run_cmd(["apt-get", "install", f"php{version}-curl", "-y"], self.cfg.hide_output, self.log_sink)
        run_cmd(["systemctl", "restart", "apache2"], self.cfg.hide_output, self.log_sink)

    def setup_tablero_vhost(self):
        vhost_path = Path("/etc/apache2/sites-available/tablero.local.conf")
        vhost_content = """
<VirtualHost *:80>
    ServerName tablero.local
    ProxyPass / http://localhost/tablero/
    ProxyPassReverse / http://localhost/tablero/
</VirtualHost>
""".strip() + "\n"
        vhost_path.write_text(vhost_content, encoding='utf-8')
        run_cmd(["a2ensite", "tablero.local.conf"], self.cfg.hide_output, self.log_sink)
        run_cmd(["a2enmod", "proxy", "proxy_http"], self.cfg.hide_output, self.log_sink)
        cp = run_cmd(["systemctl", "reload", "apache2"], self.cfg.hide_output, self.log_sink)
        if cp.returncode != 0:
            append_log("apache2 no estaba activo; intentando arrancarlo...\n")
            run_cmd(["systemctl", "start", "apache2"], self.cfg.hide_output, self.log_sink)
        ensure_host_entry("127.0.0.1", "tablero.local", self.cfg.hide_output, self.log_sink)

    def reset_apache_to_defaults(self):
        append_log("Restaurando Apache a configuración por defecto...\n")
        run_cmd(["bash", "-lc", "a2dissite tablero.local.conf >/dev/null 2>&1 || true"], self.cfg.hide_output, self.log_sink)
        run_cmd(["bash", "-lc", "rm -f /etc/apache2/sites-available/tablero.local.conf"], self.cfg.hide_output, self.log_sink)
        run_cmd(["bash", "-lc", "a2ensite 000-default.conf >/dev/null 2>&1 || true"], self.cfg.hide_output, self.log_sink)
        cp = run_cmd(["systemctl", "reload", "apache2"], self.cfg.hide_output, self.log_sink)
        if cp.returncode != 0:
            append_log("apache2 no estaba activo; intentando arrancarlo...\n")
            run_cmd(["systemctl", "start", "apache2"], self.cfg.hide_output, self.log_sink)
        append_log("Apache restaurado (sitio por defecto habilitado).\n")

    def delete_network(self):
        append_log(f"Eliminando red {self.cfg.network_name} (si existe)...\n")
        run_cmd(["bash", "-lc", f"docker network rm {self.cfg.network_name} >/dev/null 2>&1 || true"], self.cfg.hide_output, self.log_sink)

    def revert_docker_ipv6(self):
        append_log("Revirtiendo ajustes IPv6 en /etc/docker/daemon.json ...\n")
        try:
            data = {}
            if DOCKER_DAEMON_FILE.exists() and DOCKER_DAEMON_FILE.stat().st_size > 0:
                with open(DOCKER_DAEMON_FILE, 'r', encoding='utf-8') as f:
                    data = json.load(f)
            if "ipv6" in data:
                data.pop("ipv6", None)
            if "fixed-cidr-v6" in data:
                data.pop("fixed-cidr-v6", None)
            tmp = Path("/tmp/daemon.json.tmp")
            with open(tmp, 'w', encoding='utf-8') as f:
                json.dump(data, f, indent=2)
            shutil.move(tmp, DOCKER_DAEMON_FILE)
            run_cmd(["systemctl", "restart", "docker"], self.cfg.hide_output, self.log_sink)
            append_log("IPv6 revertido y Docker reiniciado.\n")
        except Exception as e:
            raise RuntimeError(f"No se pudo revertir IPv6: {e}")

    def remove_network_and_ipv6(self):
        self.delete_network()
        self.revert_docker_ipv6()

    def deep_cleanup(self):
        append_log("Iniciando limpieza exhaustiva de Docker...\n")
        cmds = [
            ["bash", "-lc", "docker ps -aq | xargs -r docker rm -f"],
            ["bash", "-lc", "docker volume prune -f"],
            ["bash", "-lc", "docker network prune -f"],
            ["bash", "-lc", "docker image prune -a -f"],
            ["bash", "-lc", "docker builder prune -a -f"],
            ["bash", "-lc", "docker system prune -a -f"],
        ]
        for c in cmds:
            run_cmd(c, self.cfg.hide_output, self.log_sink)
        append_log("Limpieza completada.\n")

    def apply_hosts(self):
        for item in self.cfg.extra_hosts:
            ensure_host_entry(item["ip"], item["domain"], self.cfg.hide_output, self.log_sink)

    def is_port_in_use(self, port: str) -> bool:
        cp = run_cmd(["bash", "-lc", f"lsof -i :{port} -sTCP:LISTEN >/dev/null 2>&1 && echo BUSY || echo OK"], True, self.log_sink)
        return "BUSY" in cp.stdout

    def show_port_details(self, port: str):
        run_cmd(["bash", "-lc", f"echo 'Proceso(s) en {port}:'; lsof -i :{port} -sTCP:LISTEN || true"], False, self.log_sink)
    
    def get_port_process_info(self, port: str) -> str:
        cp = run_cmd(["bash", "-lc", f"LC_ALL=C lsof -nP -i :{port} -sTCP:LISTEN || true"], True, self.log_sink)
        return _format_lsof_columns(cp.stdout.strip())

    def kill_process_on_port(self, port: str):
        run_cmd(["bash", "-lc", f"pid=$(lsof -t -i :{port} -sTCP:LISTEN 2>/dev/null) && [[ -n $pid ]] && kill -9 $pid || true"], self.cfg.hide_output, self.log_sink)

    def build_image(self, c: ContainerDef):
        append_log(f"Construyendo imagen {c.name}\n")
        cp = run_cmd(["docker", "build", "-t", c.name, c.path], self.cfg.hide_output, self.log_sink)
        if cp.returncode != 0:
            if not self.cfg.ignore_errors:
                raise RuntimeError(f"Falló build de {c.name}")

    def run_container(self, c: ContainerDef):
        append_log(f"Iniciando contenedor {c.name} ({c.static_ip})\n")
        ports = [p for p in c.ports.split() if p]
        for pm in ports:
            host_port = pm.split(":")[0]
            if self.is_port_in_use(host_port):
                append_log(f"Puerto {host_port} en uso.\n")
                if self.cfg.auto_kill_ports:
                    self.kill_process_on_port(host_port)
                    append_log(f"Auto-matado proceso en puerto {host_port}.\n")
                    continue
                else:
                    info = self.get_port_process_info(host_port)
                    try:
                        answer = messagebox.askyesno(
                            APP_NAME,
                            f"El puerto {host_port} está en uso.\n\n{info}\n\n¿Quieres matar ese proceso y continuar?"
                        )
                    except Exception:
                        answer = False
                    if answer:
                        self.kill_process_on_port(host_port)
                        append_log(f"Proceso en {host_port} eliminado a petición del usuario.\n")
                    else:
                        append_log(f"Continuando SIN matar el proceso en el puerto {host_port}.\n")

        port_args: List[str] = []
        for pm in ports:
            port_args += ["-p", pm]
        add_args: List[str] = c.options.split() if c.options else []
        mounts: List[str] = []
        if os.path.isdir(os.path.join(c.path, "src")):
            mounts = ["-v", f"{c.path}/src:/var/www/html"]
        run_cmd(["bash", "-lc", f"docker rm -f {c.name} >/dev/null 2>&1 || true"], True, self.log_sink)
        cmd = ["docker", "run", "--name", c.name, "--network", self.cfg.network_name, "--ip", c.static_ip, "-d"] + port_args + add_args + mounts + [c.name]
        cp = run_cmd(cmd, self.cfg.hide_output, self.log_sink)
        if cp.returncode != 0 and not self.cfg.ignore_errors:
            raise RuntimeError(f"Falló run de {c.name}")
        domain = (c.name.split("_v2")[0] + ".local")
        if c.name == "domainzonetransfer_v2":
            ensure_host_entry(c.static_ip, "domainzonetransfer.local", self.cfg.hide_output, self.log_sink)
            ensure_host_entry(c.static_ip, "codefusiondev.domainzonetransfer.local", self.cfg.hide_output, self.log_sink)
        else:
            ensure_host_entry(c.static_ip, domain, self.cfg.hide_output, self.log_sink)
        if self.cfg.stop_after_start:
            run_cmd(["docker", "stop", c.name], self.cfg.hide_output, self.log_sink)

    def build_and_run_selected(self, containers: List[ContainerDef]):
        self.ensure_network()
        for c in containers:
            if not c.enabled:
                continue
            self.build_image(c)
            self.run_container(c)

    def run_compose(self, d: 'ComposeDef'):
        append_log(f"Iniciando stack docker compose: {d.vuln} ({d.compose_path})\n")
        cp = run_cmd(["docker", "compose", "-f", d.compose_path, "up", "-d"], self.cfg.hide_output, self.log_sink)
        if cp.returncode == 0:
            self.apply_hosts()
        if cp.returncode != 0 and not self.cfg.ignore_errors:
            raise RuntimeError(f"Falló docker compose up de {d.vuln}")


# ---------------------------- HELPERS OS ----------------------------

def ensure_host_entry(ip: str, domains: str, hide_output: bool, log_sink: Optional[Callable[[str], None]] = None):
    first_domain = domains.split()[0]
    cp = run_cmd(["bash", "-lc", f"grep -qE '\\b{first_domain}\\b' /etc/hosts && echo YES || echo NO"], True, log_sink)
    if "YES" in cp.stdout:
        pattern = rf'^\s*{ip}\s+.*\b{first_domain}\b'
        cp2 = run_cmd(["bash", "-lc", f"grep -qE '{pattern}' /etc/hosts && echo OK || echo FIX"], True, log_sink)
        if "FIX" in cp2.stdout:
            run_cmd(["bash", "-lc", fr"sed -i.bak '/\<{first_domain}\>/ s/^[[:space:]]*[0-9.]\+/{ip}/' /etc/hosts"], hide_output, log_sink)
            append_log(f"Actualizada entrada hosts para {domains} -> {ip}\n")
        else:
            append_log(f"Entrada hosts correcta para {domains}\n")
    else:
        run_cmd(["bash", "-lc", f"echo '{ip} {domains}' >> /etc/hosts"], hide_output, log_sink)
        append_log(f"Añadida entrada hosts: {ip} {domains}\n")

def make_treeview_sortable(tree: ttk.Treeview):
    def _sort(col, reverse):
        data = [(tree.set(k, col), k) for k in tree.get_children('')]
        def _key(v):
            s = v[0]
            if s in ('sí', 'no'):
                return 1 if s == 'sí' else 0
            try:
                return int(s)
            except Exception:
                return s.lower()
        data.sort(key=_key, reverse=reverse)
        for idx, (_, k) in enumerate(data):
            tree.move(k, '', idx)
        tree.heading(col, command=lambda: _sort(col, not reverse))
    for col in tree['columns']:
        tree.heading(col, command=lambda c=col: _sort(c, False))

def _format_lsof_columns(text: str) -> str:
    lines = [l for l in (text or "").splitlines() if l.strip()]
    if not lines:
        return "(sin información)"
    if lines[0].startswith("COMMAND"):
        rows = lines[1:]
    else:
        rows = lines
    widths = [10, 8, 12, 4, 6, 8, 8, 6, 30]
    headers = ["COMMAND", "PID", "USER", "FD", "TYPE", "DEVICE", "SIZE/OFF", "NODE", "NAME"]
    def fmt(cols):
        cols = (cols + [""] * 9)[:9]
        return " ".join(str(cols[i])[:widths[i]].ljust(widths[i]) for i in range(9))
    out = [fmt(headers)]
    for line in rows:
        parts = _re.split(r"\s+", line.strip(), maxsplit=8)
        out.append(fmt(parts))
    return "\n".join(out)

def filecmp_safe(a: Path, b: Path) -> bool:
    try:
        return a.read_bytes() == b.read_bytes()
    except Exception:
        return False


# ------------------------------- UI -------------------------------

class App(tk.Tk):
    def __init__(self):
        super().__init__()
        self.title(APP_NAME)
        self.geometry("1200x800")
        self.minsize(1100, 700)

        require_root_or_exit()

        if CONFIG_PATH.exists():
            try:
                cfg = AppConfig.from_json(json.loads(CONFIG_PATH.read_text(encoding='utf-8')))
            except Exception:
                cfg = AppConfig()
        else:
            cfg = AppConfig()
        if not cfg.container_defs:
            cfg.container_defs = default_containers(Path.cwd())
        if not cfg.compose_defs:
            cfg.compose_defs = default_compose(Path.cwd())
        self.cfg = cfg

        self._build_menu()
        self._build_main()

        # ------------------------------------------------------------------
        # Canalización thread-safe para los logs.
        # `Core` se ejecuta en hilos secundarios; nunca debe tocar widgets Tk.
        # El worker encola texto con `self._enqueue_log`; el hilo principal
        # lo drena con `_drain_log` y actualiza el widget. Esto evita el
        # segfault que aparecía al pulsar "Run compose" en macOS.
        # ------------------------------------------------------------------
        self.log_queue: "queue.Queue[str]" = queue.Queue()
        self._log_buffer = []
        self._LOG_BUFFER_MAX = 5000  # líneas; evita OOM con compose verbosos
        self.core = Core(self.cfg, log_sink=self._enqueue_log)
        self.after(50, self._drain_log)

    # --- logging thread-safe --------------------------------------------

    def _enqueue_log(self, text: str) -> None:
        """Sink thread-safe: lo llama Core (worker thread) sin tocar Tk."""
        if not text:
            return
        try:
            self.log_queue.put_nowait(text)
        except queue.Full:
            # Si la cola se desborda, descarta lo más antiguo en memoria
            if self._log_buffer:
                self._log_buffer.pop(0)
            self._log_buffer.append(text)
            if len(self._log_buffer) > self._LOG_BUFFER_MAX:
                self._log_buffer = self._log_buffer[-self._LOG_BUFFER_MAX:]

    def _drain_log(self) -> None:
        """Drena la cola y actualiza el widget desde el hilo principal."""
        try:
            while True:
                text = self.log_queue.get_nowait()
                self._append_log_safe(text)
        except queue.Empty:
            pass
        # Vuelca cualquier overflow que hayamos retenido en memoria
        if self._log_buffer:
            buf, self._log_buffer = self._log_buffer, []
            for line in buf:
                self._append_log_safe(line)
        self.after(50, self._drain_log)

    def _append_log_safe(self, text: str) -> None:
        """Inserta texto en el log widget. SOLO desde el hilo principal."""
        self.log_text.configure(state='normal')
        self.log_text.insert('end', text)
        self.log_text.see('end')
        self.log_text.configure(state='disabled')

    def _post(self, callback) -> None:
        """Programa la ejecución de un callback en el hilo principal de Tk."""
        try:
            self.after(0, callback)
        except Exception:
            pass

    def _build_menu(self):
        menubar = tk.Menu(self)
        self.config(menu=menubar)

        file_menu = tk.Menu(menubar, tearoff=0)
        file_menu.add_command(label="Guardar configuración", command=self.save_config)
        file_menu.add_command(label="Cargar configuración", command=self.load_config)
        file_menu.add_separator()
        file_menu.add_command(label="Salir", command=self.on_exit)
        menubar.add_cascade(label="Archivo", menu=file_menu)

        actions = tk.Menu(menubar, tearoff=0)
        actions.add_command(label="Instalar Docker", command=lambda: self.run_thread(self.core.install_docker_official, "Instalar Docker"))
        actions.add_command(label="Instalar ttyd", command=lambda: self.run_thread(self.core.ensure_ttyd, "Instalar ttyd"))
        actions.add_separator()
        actions.add_command(label="Configurar IPv6 Docker", command=lambda: self.run_thread(self.core.configure_docker_ipv6, "Configurar IPv6 Docker"))
        actions.add_command(label="Crear red Docker", command=lambda: self.run_thread(self.core.ensure_network, "Crear red Docker"))
        actions.add_separator()
        actions.add_command(label="Limpieza exhaustiva (PURGE)", command=self.deep_cleanup_confirm)
        menubar.add_cascade(label="Acciones", menu=actions)

        certs = tk.Menu(menubar, tearoff=0)
        certs.add_command(label="Generar cert http3.local", command=lambda: self.run_thread(self.core.generate_http3_certs, "Generar cert http3.local"))
        certs.add_command(label="Generar cert menu.local (mkcert)", command=lambda: self.run_thread(self.core.generate_menu_local_certs, "Generar cert menu.local (mkcert)"))
        certs.add_command(label="Eliminar cert menu.local y CA", command=lambda: self.run_thread(self.core.remove_menu_local_certs, "Eliminar cert menu.local y CA"))
        menubar.add_cascade(label="Certificados", menu=certs)

    def _build_main(self):
        container = ttk.Frame(self)
        container.pack(fill='both', expand=True)

        self.tabs = ttk.Notebook(container)
        self.tabs.pack(fill='both', expand=True)

        self.tab_dashboard = ttk.Frame(self.tabs)
        self.tab_containers = ttk.Frame(self.tabs)
        self.tab_network = ttk.Frame(self.tabs)
        self.tab_server = ttk.Frame(self.tabs)
        self.tab_hosts = ttk.Frame(self.tabs)
        self.tab_settings = ttk.Frame(self.tabs)
        self.tab_logs = ttk.Frame(self.tabs)

        self.tabs.add(self.tab_dashboard, text="Dashboard")
        self.tabs.add(self.tab_containers, text="Contenedores")
        self.tabs.add(self.tab_network, text="Red & IPv6")
        self.tabs.add(self.tab_server, text="Servidor local")
        self.tabs.add(self.tab_hosts, text="/etc/hosts")
        self.tabs.add(self.tab_settings, text="Opciones avanzadas")
        self.tabs.add(self.tab_logs, text="Logs")

        self._build_dashboard()
        self._build_containers_tab()
        self._build_network_tab()
        self._build_server_tab()
        self._build_hosts_tab()
        self._build_settings_tab()
        self._build_logs_tab()
        self._attach_tooltips()

        self.status_var = tk.StringVar(value="Listo.")
        statusbar = tk.Label(self, textvariable=self.status_var, bd=1, relief=tk.SUNKEN, anchor='w')
        statusbar.pack(fill='x', side='bottom')


    def _build_dashboard(self):
        f = self.tab_dashboard

        quick = ttk.LabelFrame(f, text="Inicio rápido")
        quick.pack(fill='x', padx=10, pady=10)

        ttk.Button(quick, text="Instalar desde cero", command=self.install_from_scratch).grid(row=0, column=0, padx=6, pady=6)
        ttk.Button(quick, text="Instalar dependencias esenciales", command=lambda: self.run_thread(self.install_essentials, "Instalar dependencias esenciales")).grid(row=0, column=1, padx=6, pady=6)
        ttk.Button(quick, text="Configurar IPv6 Docker", command=lambda: self.run_thread(self.core.configure_docker_ipv6, "Configurar IPv6 Docker")).grid(row=0, column=2, padx=6, pady=6)
        ttk.Button(quick, text="Crear red WebVulnLab", command=lambda: self.run_thread(self.core.ensure_network, "Crear red WebVulnLab")).grid(row=0, column=3, padx=6, pady=6)

        ttk.Button(quick, text="Generar cert http3.local", command=lambda: self.run_thread(self.core.generate_http3_certs, "Generar cert http3.local")).grid(row=1, column=0, padx=6, pady=6)
        ttk.Button(quick, text="Generar cert menu.local", command=lambda: self.run_thread(self.core.generate_menu_local_certs, "Generar cert menu.local")).grid(row=1, column=1, padx=6, pady=6)
        ttk.Button(quick, text="Compilar ttyd", command=lambda: self.run_thread(self.core.ensure_ttyd, "Compilar ttyd")).grid(row=1, column=2, padx=6, pady=6)

        labs = ttk.LabelFrame(f, text="Operaciones sobre labs (según selección en pestaña Contenedores)")
        labs.pack(fill='x', padx=10, pady=10)

        ttk.Button(labs, text="Build + Run seleccionados", command=lambda: self.run_thread(lambda: self.core.build_and_run_selected(self.selected_containers()), "Build + Run (seleccionados)")).grid(row=0, column=0, padx=6, pady=6)
        ttk.Button(labs, text="Solo Build", command=lambda: self.run_thread(lambda: [self.core.build_image(c) for c in self.selected_containers()], "Build (seleccionados)")).grid(row=0, column=1, padx=6, pady=6)
        ttk.Button(labs, text="Solo Run", command=lambda: self.run_thread(lambda: [self.core.ensure_network()] + [self.core.run_container(c) for c in self.selected_containers()], "Run (seleccionados)")).grid(row=0, column=2, padx=6, pady=6)

        maint = ttk.LabelFrame(f, text="Mantenimiento")
        maint.pack(fill='x', padx=10, pady=10)

        ttk.Button(maint, text="Limpieza exhaustiva (PURGE)", command=self.deep_cleanup_confirm).grid(row=0, column=0, padx=6, pady=6)
        ttk.Button(maint, text="Restaurar Apache a defaults", command=lambda: self.run_thread(self.core.reset_apache_to_defaults, "Restaurar Apache a defaults")).grid(row=0, column=1, padx=6, pady=6)
        ttk.Button(maint, text="Eliminar red & revertir IPv6", command=lambda: self.run_thread(self.core.remove_network_and_ipv6, "Eliminar red & revertir IPv6")).grid(row=0, column=2, padx=6, pady=6)

    def _build_containers_tab(self):
        f = self.tab_containers
        cols = ("name", "path", "ports", "options", "static_ip", "enabled")
        sf = ttk.Frame(f)
        sf.pack(fill='x', padx=10, pady=(8, 0))
        ttk.Label(sf, text="Buscar:").pack(side='left')
        self.container_filter = tk.StringVar(value="")
        ent_c = ttk.Entry(sf, textvariable=self.container_filter, width=40)
        ent_c.pack(side='left', padx=6)
        ent_c.bind("<KeyRelease>", lambda e: self._refresh_tree())
        self.tree = ttk.Treeview(f, columns=cols, show='headings', height=18)
        for c, w in zip(cols, (180, 340, 160, 220, 120, 70)):
            self.tree.heading(c, text=c)
            self.tree.column(c, width=w, anchor='w')
        self.tree.pack(fill='both', expand=True, padx=10, pady=10)
        self._refresh_tree()

        make_treeview_sortable(self.tree)

        btns = ttk.Frame(f)
        btns.pack(fill='x', padx=10, pady=5)
        ttk.Button(btns, text="Añadir", command=self.add_container_dialog).pack(side='left', padx=5)
        ttk.Button(btns, text="Editar", command=self.edit_container_dialog).pack(side='left', padx=5)
        ttk.Button(btns, text="Eliminar", command=self.delete_container).pack(side='left', padx=5)
        ttk.Button(btns, text="(Des)activar", command=self.toggle_container).pack(side='left', padx=5)
        ttk.Button(btns, text="Copiar defaults", command=self.reset_default_containers).pack(side='left', padx=5)
        ttk.Button(btns, text="Build + Run seleccionados", command=self.build_run_selected).pack(side='right', padx=5)

        ttk.Separator(f).pack(fill='x', padx=10, pady=10)
        ttk.Label(f, text="Labs docker-compose").pack(anchor='w', padx=10)

        sfc = ttk.Frame(f)
        sfc.pack(fill='x', padx=10, pady=(8, 0))
        ttk.Label(sfc, text="Buscar compose:").pack(side='left')
        self.compose_filter = tk.StringVar(value="")
        ent_comp = ttk.Entry(sfc, textvariable=self.compose_filter, width=40)
        ent_comp.pack(side='left', padx=6)
        ent_comp.bind("<KeyRelease>", lambda e: self._refresh_compose_tree())

        cols_c = ("vuln", "compose_path", "enabled")
        self.compose_tree = ttk.Treeview(f, columns=cols_c, show='headings', height=10)
        for c, w in zip(cols_c, (220, 520, 90)):
            self.compose_tree.heading(c, text=c)
            self.compose_tree.column(c, width=w, anchor='w')
        self.compose_tree.pack(fill='both', expand=True, padx=10, pady=6)
        self._refresh_compose_tree()
        make_treeview_sortable(self.compose_tree)

        btns2 = ttk.Frame(f)
        btns2.pack(fill='x', padx=10, pady=5)
        ttk.Button(btns2, text="Añadir Compose", command=self.add_compose_dialog).pack(side='left', padx=5)
        ttk.Button(btns2, text="Editar docker-compose.yml", command=self.edit_compose_dialog).pack(side='left', padx=5)
        ttk.Button(btns2, text="Eliminar Compose", command=self.delete_compose).pack(side='left', padx=5)
        ttk.Button(btns2, text="(Des)activar", command=self.toggle_compose).pack(side='left', padx=5)
        ttk.Button(btns2, text="Run Compose seleccionados", command=self.run_compose_selected).pack(side='right', padx=5)

    def _build_network_tab(self):
        f = self.tab_network
        frm = ttk.Frame(f)
        frm.pack(fill='x', padx=10, pady=10)
        self.net_name_var = tk.StringVar(value=self.cfg.network_name)
        self.net_subnet_var = tk.StringVar(value=self.cfg.subnet_v4)
        ttk.Label(frm, text="Nombre red:").grid(row=0, column=0, sticky='w')
        ttk.Entry(frm, textvariable=self.net_name_var, width=30).grid(row=0, column=1, sticky='w')
        ttk.Label(frm, text="Subnet IPv4:").grid(row=1, column=0, sticky='w')
        ttk.Entry(frm, textvariable=self.net_subnet_var, width=30).grid(row=1, column=1, sticky='w')
        ttk.Button(frm, text="Crear/asegurar red", command=lambda: self.run_thread(self.core.ensure_network, "Crear/asegurar red")).grid(row=0, column=2, rowspan=2, padx=10)

        ttk.Separator(f).pack(fill='x', padx=10, pady=10)

        ipv6f = ttk.LabelFrame(f, text="IPv6 Docker daemon.json")
        ipv6f.pack(fill='x', padx=10, pady=10)
        self.enable_ipv6_var = tk.BooleanVar(value=self.cfg.enable_ipv6)
        self.ula_var = tk.BooleanVar(value=self.cfg.generate_ula_if_no_global)
        self.maxsub_var = tk.IntVar(value=self.cfg.max_subnets)
        ttk.Checkbutton(ipv6f, text="Habilitar IPv6 y fixed-cidr-v6 (si falta)", variable=self.enable_ipv6_var).grid(row=0, column=0, sticky='w')
        ttk.Checkbutton(ipv6f, text="Generar ULA si no hay prefijo global", variable=self.ula_var).grid(row=1, column=0, sticky='w')
        ttk.Label(ipv6f, text="Máx. subredes a probar:").grid(row=2, column=0, sticky='w')
        ttk.Entry(ipv6f, textvariable=self.maxsub_var, width=10).grid(row=2, column=1, sticky='w')
        ttk.Button(ipv6f, text="Aplicar", command=self.apply_ipv6_settings).grid(row=0, column=2, rowspan=3, padx=10)

        ttk.Separator(f).pack(fill='x', padx=10, pady=10)
        danger = ttk.LabelFrame(f, text="Limpieza")
        danger.pack(fill='x', padx=10, pady=10)
        ttk.Button(danger, text="Eliminar red & revertir IPv6", command=lambda: self.run_thread(self.core.remove_network_and_ipv6)).grid(row=0, column=0, padx=10, pady=6)

    def _build_server_tab(self):
        f = self.tab_server
        frm = ttk.LabelFrame(f, text="Tablero local")
        frm.pack(fill='x', padx=10, pady=10)
        self.tablero_path_var = tk.StringVar(value=str(Path.cwd() / "tablero"))
        ttk.Label(frm, text="Ruta del directorio 'tablero':").grid(row=0, column=0, sticky='w')
        ttk.Entry(frm, textvariable=self.tablero_path_var, width=60).grid(row=0, column=1, sticky='w')
        ttk.Button(frm, text="Examinar", command=self.browse_tablero).grid(row=0, column=2, padx=5)
        ttk.Button(frm, text="Copiar y preparar (API Docker + php-curl)", command=self.build_local_server).grid(row=0, column=3, padx=10)

        vhostf = ttk.LabelFrame(f, text="VirtualHost tablero.local")
        vhostf.pack(fill='x', padx=10, pady=10)
        ttk.Button(vhostf, text="Crear/activar vhost y recargar Apache", command=lambda: self.run_thread(self.core.setup_tablero_vhost, "Crear/activar vhost tablero.local")).grid(row=0, column=0, padx=10, pady=5)

        wrapf = ttk.LabelFrame(f, text="Terminal web wrapper (sudoers)")
        wrapf.pack(fill='x', padx=10, pady=10)
        ttk.Button(wrapf, text="Instalar wrapper y sudoers", command=lambda: self.run_thread(self.core.configure_terminal_wrapper, "Instalar wrapper y sudoers")).grid(row=0, column=0, padx=10, pady=5)

        restf = ttk.LabelFrame(f, text="Mantenimiento Apache")
        restf.pack(fill='x', padx=10, pady=10)
        ttk.Button(restf, text="Restaurar Apache a defaults", command=lambda: self.run_thread(self.core.reset_apache_to_defaults)).grid(row=0, column=0, padx=10, pady=6)

        certf = ttk.LabelFrame(f, text="Certificados")
        certf.pack(fill='x', padx=10, pady=10)
        ttk.Button(certf, text="Generar http3.local", command=lambda: self.run_thread(self.core.generate_http3_certs, "Generar http3.local")).grid(row=0, column=0, padx=5, pady=5)
        ttk.Button(certf, text="Generar menu.local (mkcert)", command=lambda: self.run_thread(self.core.generate_menu_local_certs, "Generar menu.local (mkcert)")).grid(row=0, column=1, padx=5, pady=5)
        ttk.Button(certf, text="Eliminar menu.local + CA", command=lambda: self.run_thread(self.core.remove_menu_local_certs, "Eliminar menu.local + CA")).grid(row=0, column=2, padx=5, pady=5)

    def _build_hosts_tab(self):
        f = self.tab_hosts
        cols = ("ip", "domain")
        self.hosts_tree = ttk.Treeview(f, columns=cols, show='headings', height=12)
        for c, w in zip(cols, (200, 500)):
            self.hosts_tree.heading(c, text=c)
            self.hosts_tree.column(c, width=w, anchor='w')
        self.hosts_tree.pack(fill='both', expand=True, padx=10, pady=10)
        self._refresh_hosts_tree()
        make_treeview_sortable(self.hosts_tree)

        btns = ttk.Frame(f)
        btns.pack(fill='x', padx=10, pady=5)
        ttk.Button(btns, text="Añadir", command=self.add_host_entry).pack(side='left', padx=5)
        ttk.Button(btns, text="Editar", command=self.edit_host_entry).pack(side='left', padx=5)
        ttk.Button(btns, text="Eliminar", command=self.delete_host_entry).pack(side='left', padx=5)
        ttk.Button(btns, text="Aplicar /etc/hosts", command=lambda: self.run_thread(self.core.apply_hosts, "Aplicar /etc/hosts")).pack(side='right', padx=5)

    def _build_settings_tab(self):
        f = self.tab_settings
        self.hide_output_var = tk.BooleanVar(value=self.cfg.hide_output)
        self.ignore_errors_var = tk.BooleanVar(value=self.cfg.ignore_errors)
        self.stop_after_start_var = tk.BooleanVar(value=self.cfg.stop_after_start)
        self.auto_kill_var = tk.BooleanVar(value=self.cfg.auto_kill_ports)

        ttk.Checkbutton(f, text="Ocultar salida de comandos (se sigue guardando en log)", variable=self.hide_output_var).pack(anchor='w', padx=10, pady=5)
        ttk.Checkbutton(f, text="Ignorar errores (continuar en fallos)", variable=self.ignore_errors_var).pack(anchor='w', padx=10, pady=5)
        ttk.Checkbutton(f, text="Detener contenedores tras iniciar", variable=self.stop_after_start_var).pack(anchor='w', padx=10, pady=5)
        ttk.Checkbutton(f, text="Auto-matar procesos que usan puertos en conflicto", variable=self.auto_kill_var).pack(anchor='w', padx=10, pady=5)

        ttk.Button(f, text="Guardar opciones", command=self.apply_settings).pack(anchor='w', padx=10, pady=10)

    def _build_logs_tab(self):
        f = self.tab_logs
        self.log_text = tk.Text(f, wrap='word', state='disabled')
        self.log_text.pack(fill='both', expand=True, padx=10, pady=10)
        btns = ttk.Frame(f)
        btns.pack(fill='x', padx=10, pady=5)
        ttk.Button(btns, text="Abrir log en editor", command=self.open_log_file).pack(side='left', padx=5)
        ttk.Button(btns, text="Limpiar", command=self.clear_log_widget).pack(side='left', padx=5)
    
    def _attach_tooltips(self):
        help_map = {
            "Instalar dependencias esenciales": "Instala Docker, docker-compose y paquetes comunes si faltan.",
            "Configurar IPv6 Docker": "Escribe ipv6 y fixed-cidr-v6 en daemon.json y reinicia Docker.",
            "Crear red WebVulnLab": "Crea la red fija para los labs con la subred configurada.",
            "Generar cert http3.local": "Genera un certificado autofirmado para http3.local.",
            "Generar cert menu.local": "Genera certificado con mkcert y añade la CA al sistema.",
            "Compilar ttyd": "Compila e instala ttyd (terminal web).",
            "Build + Run seleccionados": "Construye imágenes Docker y arráncalas con la IP estática.",
            "Solo Build": "Solo construye las imágenes seleccionadas.",
            "Solo Run": "Solo arranca los contenedores ya construidos.",
            "Añadir": "Añade un contenedor normal (Dockerfile).",
            "Editar": "Edita la definición del contenedor seleccionado.",
            "Eliminar": "Elimina la definición seleccionada.",
            "(Des)activar": "Activa/Desactiva (soporta selección múltiple).",
            "Copiar defaults": "Restaura la lista de contenedores por defecto.",
            "Crear/asegurar red": "Crea la red Docker configurada si no existe.",
            "Aplicar": "Aplica opciones IPv6 en daemon.json.",
            "Copiar y preparar (API Docker + php-curl)": "Copia el tablero a Apache y habilita API Docker y php-curl.",
            "Crear/activar vhost y recargar Apache": "Crea tablero.local y recarga Apache.",
            "Instalar wrapper y sudoers": "Instala wrapper para exec y entrada sudoers.",
            "Generar http3.local": "Atajo para generar el certificado http3.local.",
            "Generar menu.local (mkcert)": "Atajo para generar el certificado de menu.local.",
            "Eliminar menu.local + CA": "Elimina el cert y la CA agregada.",
            "Añadir Compose": "Añade un lab gestionado por docker-compose.",
            "Editar docker-compose.yml": "Abre y edita el YAML del compose seleccionado.",
            "Eliminar Compose": "Elimina la definición compose seleccionada.",
            "Run Compose seleccionados": "Ejecuta 'docker-compose up -d' en los seleccionados.",
            "Aplicar /etc/hosts": "Escribe/actualiza las entradas definidas en /etc/hosts.",
            "Guardar opciones": "Guarda las opciones de esta pestaña.",
            "Abrir log en editor": "Abre el fichero de log del instalador.",
            "Limpiar": "Limpia la vista de logs.",
            "Restaurar Apache a defaults": "Deshabilita tablero.local y vuelve a 000-default.",
            "Eliminar red & revertir IPv6": "Borra la red de labs y revierte la config IPv6 del daemon.",
        }
        def _walk(widget):
            for child in widget.winfo_children():
                if isinstance(child, ttk.Button):
                    txt = child.cget("text")
                    add_tooltip(child, help_map.get(txt, f"Acción: {txt}"))
                _walk(child)
        _walk(self)

    def open_log_file(self):
        self.tabs.select(self.tab_logs)
        try:
            tail_file = "/tmp/webvulnlab_tail.log"
            N = 1200
            text = ""
            if LOG_PATH.exists():
                from collections import deque
                with open(LOG_PATH, "r", encoding="utf-8", errors="ignore") as f:
                    text = "".join(deque(f, maxlen=N))
            Path(tail_file).write_text(text, encoding="utf-8")
            os.system(
                "x-terminal-emulator -e bash -lc 'less -S +G /tmp/webvulnlab_tail.log' "
                "|| gnome-terminal -- bash -lc 'less -S +G /tmp/webvulnlab_tail.log' "
                "|| konsole -e bash -lc 'less -S +G /tmp/webvulnlab_tail.log' "
                "|| xterm -e bash -lc 'less -S +G /tmp/webvulnlab_tail.log' "
                "|| less -S +G /tmp/webvulnlab_tail.log"
            )
        except Exception:
            os.system("less -S +G '/tmp/webvulnlab_tail.log'")

    def clear_log_widget(self):
        self.log_text.configure(state='normal')
        self.log_text.delete('1.0', 'end')
        self.log_text.configure(state='disabled')

    def apply_settings(self):
        self.cfg.hide_output = self.hide_output_var.get()
        self.cfg.ignore_errors = self.ignore_errors_var.get()
        self.cfg.stop_after_start = self.stop_after_start_var.get()
        self.cfg.auto_kill_ports = self.auto_kill_var.get()
        self.cfg.network_name = self.net_name_var.get().strip() or DEFAULT_NETWORK_NAME
        self.cfg.subnet_v4 = self.net_subnet_var.get().strip() or DEFAULT_SUBNET_V4
        messagebox.showinfo(APP_NAME, "Opciones guardadas (recuerda Guardar configuración si quieres persistir).")

    def apply_ipv6_settings(self):
        self.cfg.enable_ipv6 = self.enable_ipv6_var.get()
        self.cfg.generate_ula_if_no_global = self.ula_var.get()
        self.cfg.max_subnets = int(self.maxsub_var.get())
        if self.cfg.enable_ipv6:
            self.run_thread(self.core.configure_docker_ipv6)
        else:
            messagebox.showinfo(APP_NAME, "Has desactivado IPv6; no se aplican cambios.")

    def browse_tablero(self):
        d = filedialog.askdirectory(initialdir=str(Path.cwd()), title="Selecciona carpeta 'tablero'")
        if d:
            self.tablero_path_var.set(d)

    def build_local_server(self):
        path = Path(self.tablero_path_var.get())
        self.run_thread(lambda: self.core.build_local_server(path), "Preparar servidor local")

    def _refresh_tree(self):
        filt = getattr(self, 'container_filter', tk.StringVar(value="")).get().strip().lower() if hasattr(self, 'container_filter') else ""
        for i in self.tree.get_children():
            self.tree.delete(i)
        self.cont_iid_to_idx = {}
        for idx, c in enumerate(self.cfg.container_defs):
            if filt and (filt not in c.name.lower() and filt not in c.path.lower()):
                continue
            iid = f"cont::{idx}"
            self.tree.insert('', 'end', iid=iid, values=c.to_row())
            self.cont_iid_to_idx[iid] = idx

    def _refresh_hosts_tree(self):
        for i in self.hosts_tree.get_children():
            self.hosts_tree.delete(i)
        for e in self.cfg.extra_hosts:
            self.hosts_tree.insert('', 'end', values=(e['ip'], e['domain']))

    def _refresh_compose_tree(self):
        filt = getattr(self, 'compose_filter', tk.StringVar(value="")).get().strip().lower() if hasattr(self, 'compose_filter') else ""
        for i in self.compose_tree.get_children():
            self.compose_tree.delete(i)
        self.comp_iid_to_idx = {}
        for idx, d in enumerate(self.cfg.compose_defs):
            if filt and (filt not in d.vuln.lower() and filt not in d.compose_path.lower()):
                continue
            iid = f"comp::{idx}"
            self.compose_tree.insert('', 'end', iid=iid, values=d.to_row())
            self.comp_iid_to_idx[iid] = idx

    def selected_compose(self) -> List['ComposeDef']:
        return [self.cfg.compose_defs[self.compose_tree.index(i)] for i in self.compose_tree.selection()]

    def add_compose_dialog(self):
        ComposeDialog(self, None)

    def edit_compose_dialog(self):
        sel = self.compose_tree.selection()
        if not sel:
            return
        iid = sel[0]
        idx = self.comp_iid_to_idx.get(iid)
        if idx is None:
            return
        ComposeDialog(self, idx)

    def delete_compose(self):
        self.tabs.select(self.tab_logs)
        sel = self.compose_tree.selection()
        if not sel:
            return
        iid = sel[0]
        idx = self.comp_iid_to_idx.get(iid)
        if idx is None:
            return
        d = self.cfg.compose_defs[idx]
        if messagebox.askyesno(APP_NAME, f"¿Eliminar definición compose '{d.vuln}'?"):
            self.cfg.compose_defs.pop(idx)
            self._refresh_compose_tree()

    def toggle_compose(self):
        sel = self.compose_tree.selection()
        if not sel:
            return
        for iid in sel:
            idx = self.comp_iid_to_idx.get(iid)
            if idx is not None:
                self.cfg.compose_defs[idx].enabled = not self.cfg.compose_defs[idx].enabled
        self._refresh_compose_tree()

    def run_compose_selected(self):
        cds = [d for d in self.cfg.compose_defs if d.enabled]
        if not cds:
            messagebox.showwarning(APP_NAME, "No hay compose labs activados.")
            return
        self.apply_settings()
        def task():
            for d in cds:
                self.core.run_compose(d)
        self.run_thread(task, "docker compose up -d (seleccionados)")

    def reset_default_containers(self):
        self.tabs.select(self.tab_logs)
        if messagebox.askyesno(APP_NAME, "¿Sobrescribir la lista con los defaults?"):
            self.cfg.container_defs = default_containers(Path.cwd())
            self._refresh_tree()

    def add_container_dialog(self):
        ContainerDialog(self, None)

    def edit_container_dialog(self):
        sel = self.tree.selection()
        if not sel:
            return
        iid = sel[0]
        idx = self.cont_iid_to_idx.get(iid)
        if idx is None:
            return
        ContainerDialog(self, idx)

    def delete_container(self):
        self.tabs.select(self.tab_logs)
        sel = self.tree.selection()
        if not sel:
            return
        iid = sel[0]
        idx = self.cont_iid_to_idx.get(iid)
        if idx is None:
            return
        c = self.cfg.container_defs[idx]
        if messagebox.askyesno(APP_NAME, f"¿Eliminar {c.name}?"):
            self.cfg.container_defs.pop(idx)
            self._refresh_tree()

    def toggle_container(self):
        sel = self.tree.selection()
        if not sel:
            return
        for iid in sel:
            idx = self.cont_iid_to_idx.get(iid)
            if idx is not None:
                self.cfg.container_defs[idx].enabled = not self.cfg.container_defs[idx].enabled
        self._refresh_tree()

    def add_host_entry(self):
        HostDialog(self, None)

    def edit_host_entry(self):
        sel = self.hosts_tree.selection()
        if not sel:
            return
        idx = self.hosts_tree.index(sel[0])
        HostDialog(self, idx)

    def delete_host_entry(self):
        self.tabs.select(self.tab_logs)
        sel = self.hosts_tree.selection()
        if not sel:
            return
        idx = self.hosts_tree.index(sel[0])
        e = self.cfg.extra_hosts[idx]
        if messagebox.askyesno(APP_NAME, f"¿Eliminar {e['domain']}?"):
            self.cfg.extra_hosts.pop(idx)
            self._refresh_hosts_tree()

    def selected_containers(self) -> List[ContainerDef]:
        return [c for c in self.cfg.container_defs if c.enabled]

    def build_run_selected(self):
        containers = self.selected_containers()
        if not containers:
            messagebox.showwarning(APP_NAME, "No hay contenedores activados.")
            return
        self.apply_settings()
        self.run_thread(lambda: self.core.build_and_run_selected(containers), "Build + Run (seleccionados)")

    def build_selected_only(self):
        containers = self.selected_containers()
        if not containers:
            messagebox.showwarning(APP_NAME, "No hay contenedores activados.")
            return
        self.apply_settings()
        def task():
            for c in containers:
                self.core.build_image(c)
        self.run_thread(task, "Build (seleccionados)")

    def run_selected_only(self):
        containers = self.selected_containers()
        if not containers:
            messagebox.showwarning(APP_NAME, "No hay contenedores activados.")
            return
        self.apply_settings()
        def task():
            self.core.ensure_network()
            for c in containers:
                self.core.run_container(c)
        self.run_thread(task, "Run (seleccionados)")

    def save_config(self):
        self.apply_settings()
        CONFIG_PATH.write_text(json.dumps(self.cfg.to_json(), indent=2), encoding='utf-8')
        messagebox.showinfo(APP_NAME, f"Configuración guardada en {CONFIG_PATH}")

    def load_config(self):
        try:
            data = json.loads(CONFIG_PATH.read_text(encoding='utf-8'))
            self.cfg = AppConfig.from_json(data)
            self._refresh_tree()
            self._refresh_hosts_tree()
            self.hide_output_var.set(self.cfg.hide_output)
            self.ignore_errors_var.set(self.cfg.ignore_errors)
            self.stop_after_start_var.set(self.cfg.stop_after_start)
            self.auto_kill_var.set(self.cfg.auto_kill_ports)
            self.net_name_var.set(self.cfg.network_name)
            self.net_subnet_var.set(self.cfg.subnet_v4)
            self.enable_ipv6_var.set(self.cfg.enable_ipv6)
            self.ula_var.set(self.cfg.generate_ula_if_no_global)
            self.maxsub_var.set(self.cfg.max_subnets)
            messagebox.showinfo(APP_NAME, "Configuración cargada.")
        except Exception as e:
            messagebox.showerror(APP_NAME, f"No se pudo cargar configuración: {e}")

    def install_essentials(self):
        def task():
            if (shutil.which("docker") is None) or (subprocess.call("docker compose version >/dev/null 2>&1 || docker-compose --version >/dev/null 2>&1", shell=True) != 0):
                self.core.install_docker_official()
            pkgs = []
            for cmd, pkg in {"jq":"jq","openssl":"openssl","php":"php","apache2":"apache2","ldapadd":"ldap-utils","ip":"iproute2","stty":"coreutils","sed":"sed","a2enmod":"apache2","a2ensite":"apache2","lsof":"lsof","curl":"curl"}.items():
                if not which(cmd):
                    pkgs.append(pkg)
            if pkgs:
                self.core.install_packages(sorted(set(pkgs)))
            self.core.start_docker_service()
        self.run_thread(task, "Instalar dependencias esenciales")
    
    def install_from_scratch(self):
        def task():
            if (shutil.which("docker") is None) or (subprocess.call("docker compose version >/dev/null 2>&1 || docker-compose --version >/dev/null 2>&1", shell=True) != 0):
                self.core.install_docker_official()
            pkgs = []
            for cmd, pkg in {"jq":"jq","openssl":"openssl","php":"php","apache2":"apache2","ldapadd":"ldap-utils","ip":"iproute2","stty":"coreutils","sed":"sed","a2enmod":"apache2","a2ensite":"apache2","lsof":"lsof","curl":"curl"}.items():
                if not which(cmd):
                    pkgs.append(pkg)
            if pkgs:
                self.core.install_packages(sorted(set(pkgs)))
            self.core.start_docker_service()

            self.core.configure_docker_ipv6()
            self.core.ensure_network()

            self.core.generate_http3_certs()
            self.core.generate_menu_local_certs()

            self.core.ensure_ttyd()

            try:
                path = Path(getattr(self, "tablero_path_var", None).get()) if hasattr(self, "tablero_path_var") else (Path.cwd() / "tablero")
            except Exception:
                path = Path.cwd() / "tablero"
            if path.exists():
                self.core.build_local_server(path)
            self.core.setup_tablero_vhost()

            try:
                self.core.configure_terminal_wrapper()
            except Exception as e:
                append_log(f"Wrapper no instalado: {e}\n")
            self.core.apply_hosts()

        self.run_thread(task, "Instalar desde cero")
    
    def deep_cleanup_confirm(self):
        msg = ("⚠️ Esto eliminará TODOS los contenedores, imágenes, redes, volúmenes y cachés de compilación.\n"
               "Si tienes contenedores personales, se PERDERÁN.\n\n¿Quieres continuar?")
        if messagebox.askyesno(APP_NAME, msg):
            self.run_thread(self.core.deep_cleanup, "Limpieza exhaustiva (PURGE)")

    def run_thread(self, func, desc: Optional[str] = None):
        def wrapper():
            # Cualquier acceso a widgets Tk o a messagebox debe pasar por
            # `_post` para ejecutarse en el hilo principal. Lo contrario
            # provoca segmentation faults intermitentes en macOS.
            self._post(lambda: self.tabs.select(self.tab_logs))

            action = desc
            if not action:
                try:
                    n = getattr(func, "__name__", "")
                    if n and n != "<lambda>":
                        action = n.replace("_", " ")
                except Exception:
                    action = None
            action = action or "operación"

            self._post(lambda a=action: self.status_var.set(f"Ejecutando… {a}"))
            self._enqueue_log(f"\n▶️ Ejecutando… {action}\n")

            try:
                func()
                self._post(lambda a=action: self.status_var.set(f"Completado: {a}"))
                self._enqueue_log("\n✔️ Operación completada.\n\n")
            except Exception as e:
                append_log(f"ERROR: {e}\n")
                self._enqueue_log(f"\n❌ ERROR: {e}\n\n")
                err = str(e)
                self._post(lambda e=err: self.status_var.set(f"ERROR: {e}"))
                if not self.cfg.ignore_errors:
                    self._post(lambda e=err: messagebox.showerror(APP_NAME, e))

        t = threading.Thread(target=wrapper, daemon=True)
        t.start()

    def on_exit(self):
        self.destroy()


class ContainerDialog(tk.Toplevel):
    def __init__(self, app: App, index: Optional[int]):
        super().__init__(app)
        self.app = app
        self.index = index
        self.title("Contenedor")
        self.resizable(False, False)

        if index is None:
            self.c = ContainerDef("", str(Path.cwd()), "", "", "", True)
        else:
            self.c = app.cfg.container_defs[index]

        self.vars = {
            'name': tk.StringVar(value=self.c.name),
            'path': tk.StringVar(value=self.c.path),
            'ports': tk.StringVar(value=self.c.ports),
            'options': tk.StringVar(value=self.c.options),
            'static_ip': tk.StringVar(value=self.c.static_ip),
            'enabled': tk.BooleanVar(value=self.c.enabled),
        }

        frm = ttk.Frame(self, padding=10)
        frm.pack(fill='both', expand=True)
        ttk.Label(frm, text="Nombre").grid(row=0, column=0, sticky='e')
        ttk.Entry(frm, textvariable=self.vars['name'], width=40).grid(row=0, column=1, sticky='w')
        ttk.Label(frm, text="Ruta Dockerfile").grid(row=1, column=0, sticky='e')
        ttk.Entry(frm, textvariable=self.vars['path'], width=60).grid(row=1, column=1, sticky='w')
        ttk.Button(frm, text="...", command=self.browse).grid(row=1, column=2)
        ttk.Label(frm, text="Puertos (espacios)").grid(row=2, column=0, sticky='e')
        ttk.Entry(frm, textvariable=self.vars['ports'], width=60).grid(row=2, column=1, sticky='w')
        ttk.Label(frm, text="Opciones extra").grid(row=3, column=0, sticky='e')
        ttk.Entry(frm, textvariable=self.vars['options'], width=60).grid(row=3, column=1, sticky='w')
        ttk.Label(frm, text="IP estática").grid(row=4, column=0, sticky='e')
        ttk.Entry(frm, textvariable=self.vars['static_ip'], width=20).grid(row=4, column=1, sticky='w')
        ttk.Checkbutton(frm, text="Activo", variable=self.vars['enabled']).grid(row=5, column=1, sticky='w', pady=5)
        btns = ttk.Frame(frm)
        btns.grid(row=6, column=0, columnspan=3, pady=10)
        ttk.Button(btns, text="Guardar", command=self.save).pack(side='left', padx=5)
        ttk.Button(btns, text="Cancelar", command=self.destroy).pack(side='left', padx=5)

    def browse(self):
        d = filedialog.askdirectory(initialdir=self.vars['path'].get() or str(Path.cwd()))
        if d:
            self.vars['path'].set(d)

    def save(self):
        vals = {k: v.get() if not isinstance(v, tk.BooleanVar) else v.get() for k, v in self.vars.items()}
        if not vals['name'] or not vals['path'] or not vals['static_ip']:
            messagebox.showwarning(APP_NAME, "Nombre, ruta e IP son obligatorios")
            return
        newc = ContainerDef(**vals)
        if self.index is None:
            self.app.cfg.container_defs.append(newc)
        else:
            self.app.cfg.container_defs[self.index] = newc
        self.app._refresh_tree()
        self.destroy()

class ComposeDialog(tk.Toplevel):
    def __init__(self, app: App, index: Optional[int]):
        super().__init__(app)
        self.app = app
        self.index = index
        self.title("Compose lab")
        self.resizable(True, True)

        if index is None:
            self.vuln = tk.StringVar(value="")
            self.compose_path = tk.StringVar(value=str(Path.cwd() / "docker-compose.yml"))
            self.enabled = tk.BooleanVar(value=True)
            self.orig_content = ""
        else:
            d = app.cfg.compose_defs[index]
            self.vuln = tk.StringVar(value=d.vuln)
            self.compose_path = tk.StringVar(value=d.compose_path)
            self.enabled = tk.BooleanVar(value=d.enabled)
            try:
                self.orig_content = Path(d.compose_path).read_text(encoding='utf-8')
            except Exception:
                self.orig_content = ""

        frm = ttk.Frame(self, padding=10)
        frm.pack(fill='both', expand=True)
        ttk.Label(frm, text="Vulnerabilidad").grid(row=0, column=0, sticky='e')
        ttk.Entry(frm, textvariable=self.vuln, width=40).grid(row=0, column=1, sticky='w')
        ttk.Label(frm, text="Ruta docker-compose.yml").grid(row=1, column=0, sticky='e')
        ttk.Entry(frm, textvariable=self.compose_path, width=60).grid(row=1, column=1, sticky='w')
        ttk.Button(frm, text="...", command=self._browse).grid(row=1, column=2)
        ttk.Checkbutton(frm, text="Activo", variable=self.enabled).grid(row=2, column=1, sticky='w')

        warn = ttk.Label(frm, foreground="#b45309",
                         text="⚠️ Si cambias la IP estática (ipv4_address) en el compose, recuerda actualizar también /etc/hosts.")
        warn.grid(row=3, column=0, columnspan=3, sticky='w', pady=(6, 2))

        self.editor = tk.Text(frm, wrap='none', height=22)
        self.editor.grid(row=4, column=0, columnspan=3, sticky='nsew', pady=5)
        frm.rowconfigure(4, weight=1)
        frm.columnconfigure(1, weight=1)

        try:
            if self.orig_content:
                self.editor.insert('1.0', self.orig_content)
            else:
                self.editor.insert('1.0', "version: '3'\nservices:\n  app:\n    image: nginx:alpine\n    networks:\n      default:\n        ipv4_address: 172.18.0.100\nnetworks:\n  default:\n    external: true\n    name: WebVulnLab-Network\n")
        except Exception:
            pass

        btns = ttk.Frame(frm)
        btns.grid(row=5, column=0, columnspan=3, pady=8)
        ttk.Button(btns, text="Guardar", command=self._save).pack(side='left', padx=5)
        ttk.Button(btns, text="Cancelar", command=self.destroy).pack(side='left', padx=5)

    def _browse(self):
        p = filedialog.askopenfilename(initialdir=str(Path.cwd()), title="Selecciona docker-compose.yml",
                                       filetypes=[("YAML", "*.yml *.yaml"), ("Todos", "*.*")])
        if p:
            self.compose_path.set(p)
            try:
                self.orig_content = Path(p).read_text(encoding='utf-8')
                self.editor.delete('1.0', 'end')
                self.editor.insert('1.0', self.orig_content)
            except Exception:
                pass

    def _save(self):
        vuln = self.vuln.get().strip()
        path = self.compose_path.get().strip()
        if not vuln or not path:
            messagebox.showwarning(APP_NAME, "Vulnerabilidad y ruta son obligatorias.")
            return
        new_content = self.editor.get('1.0', 'end')
        import re as _re
        old_ip = None
        new_ip = None
        if self.orig_content:
            m1 = _re.search(r'ipv4_address:\s*([0-9.]+)', self.orig_content)
            if m1:
                old_ip = m1.group(1)
        m2 = _re.search(r'ipv4_address:\s*([0-9.]+)', new_content)
        if m2:
            new_ip = m2.group(1)
        try:
            Path(path).write_text(new_content, encoding='utf-8')
        except Exception as e:
            messagebox.showerror(APP_NAME, f"No se pudo guardar el compose: {e}")
            return
        d = ComposeDef(vuln=vuln, compose_path=path, enabled=self.enabled.get())
        if self.index is None:
            self.app.cfg.compose_defs.append(d)
        else:
            self.app.cfg.compose_defs[self.index] = d
        self.app._refresh_compose_tree()
        if old_ip and new_ip and old_ip != new_ip:
            messagebox.showwarning(APP_NAME, f"Has cambiado la IP estática de {old_ip} → {new_ip}. Recuerda actualizar /etc/hosts.")
        self.destroy()

class HostDialog(tk.Toplevel):
    def __init__(self, app: App, index: Optional[int]):
        super().__init__(app)
        self.app = app
        self.index = index
        self.title("Entrada /etc/hosts")
        self.resizable(False, False)
        if index is None:
            self.ip = tk.StringVar(value="172.18.0.1")
            self.domain = tk.StringVar(value="lab.local")
        else:
            item = app.cfg.extra_hosts[index]
            self.ip = tk.StringVar(value=item['ip'])
            self.domain = tk.StringVar(value=item['domain'])
        frm = ttk.Frame(self, padding=10)
        frm.pack(fill='both', expand=True)
        ttk.Label(frm, text="IP").grid(row=0, column=0, sticky='e')
        ttk.Entry(frm, textvariable=self.ip, width=20).grid(row=0, column=1, sticky='w')
        ttk.Label(frm, text="Dominio(s)").grid(row=1, column=0, sticky='e')
        ttk.Entry(frm, textvariable=self.domain, width=50).grid(row=1, column=1, sticky='w')
        btns = ttk.Frame(frm)
        btns.grid(row=2, column=0, columnspan=2, pady=10)
        ttk.Button(btns, text="Guardar", command=self.save).pack(side='left', padx=5)
        ttk.Button(btns, text="Cancelar", command=self.destroy).pack(side='left', padx=5)

    def save(self):
        ip, dom = self.ip.get().strip(), self.domain.get().strip()
        if not ip or not dom:
            messagebox.showwarning(APP_NAME, "IP y dominio son obligatorios")
            return
        entry = {"ip": ip, "domain": dom}
        if self.index is None:
            self.app.cfg.extra_hosts.append(entry)
        else:
            self.app.cfg.extra_hosts[self.index] = entry
        self.app._refresh_hosts_tree()
        self.destroy()


# ------------------------------ MAIN ------------------------------

def main():
    try:
        app = App()
        app.mainloop()
    except KeyboardInterrupt:
        pass


if __name__ == "__main__":
    main()
