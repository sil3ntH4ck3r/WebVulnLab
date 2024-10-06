#!/usr/bin/env python3
import argparse
import base64
from http.server import HTTPServer, SimpleHTTPRequestHandler
import socket
import sys

class AuthHTTPRequestHandler(SimpleHTTPRequestHandler):
    def __init__(self, *args, directory=None, username=None, password=None, **kwargs):
        self.username = username
        self.password = password
        super().__init__(*args, directory=directory, **kwargs)

    def do_HEAD(self):
        if not self._authenticate():
            return
        super().do_HEAD()

    def do_GET(self):
        if not self._authenticate():
            return
        super().do_GET()

    def _authenticate(self):
        auth_header = self.headers.get('Authorization')
        if auth_header is None:
            self.send_auth_headers()
            self.wfile.write(b'No authentication headers received')
            return False

        try:
            auth_type, encoded_credentials = auth_header.split(' ', 1)
        except ValueError:
            self.send_auth_headers()
            self.wfile.write(b'Invalid authentication header format')
            return False

        if auth_type.lower() != 'basic':
            self.send_auth_headers()
            self.wfile.write(b'Unsupported authentication type')
            return False

        try:
            decoded_credentials = base64.b64decode(encoded_credentials).decode('utf-8')
            username, password = decoded_credentials.split(':', 1)
        except (base64.binascii.Error, ValueError):
            self.send_auth_headers()
            self.wfile.write(b'Invalid credentials format')
            return False

        if username == self.username and password == self.password:
            return True  # Autenticación exitosa
        else:
            self.send_auth_headers()
            self.wfile.write(b'Invalid username or password')
            return False

    def send_auth_headers(self):
        self.send_response(401)
        self.send_header('WWW-Authenticate', 'Basic realm="Protected Area"')
        self.end_headers()

def run_server(bind_address, port, username, password):
    handler_class = lambda *args, **kwargs: AuthHTTPRequestHandler(
        *args, username=username, password=password, **kwargs)

    # Crear el socket manualmente con AF_INET6
    server_address = (bind_address, port)
    try:
        sock = socket.socket(socket.AF_INET6, socket.SOCK_STREAM)
    except socket.error as e:
        print(f"Error al crear el socket IPv6: {e}", file=sys.stderr)
        sys.exit(1)

    # Establecer la opción IPV6_V6ONLY para evitar conexiones IPv4
    try:
        sock.setsockopt(socket.IPPROTO_IPV6, socket.IPV6_V6ONLY, 1)
    except socket.error as e:
        print(f"Error al establecer IPV6_V6ONLY: {e}", file=sys.stderr)
        sock.close()
        sys.exit(1)

    try:
        sock.bind(server_address)
    except socket.error as e:
        print(f"Error al vincular el socket a {server_address}: {e}", file=sys.stderr)
        sock.close()
        sys.exit(1)

    sock.listen(5)

    # Crear el servidor HTTP utilizando el socket existente
    httpd = HTTPServer(server_address, handler_class, False)

    # Reemplazar el socket del servidor con el nuestro
    httpd.socket = sock
    httpd.server_bind = self_server_bind  # Evitar que vuelva a vincular
    httpd.server_activate()

    print(f"Serviendo en [{bind_address}]:{port} (IPv6 únicamente)")
    try:
        httpd.serve_forever()
    except KeyboardInterrupt:
        print("\nServidor detenido por el usuario.")
    finally:
        httpd.server_close()

def self_server_bind():
    pass  # No hacer nada, ya que el socket ya está vinculado

def main():
    parser = argparse.ArgumentParser(description='Servidor HTTP Simple con Autenticación Básica y Bloqueo de IPv4')
    parser.add_argument('--bind', default='::', help='Dirección de enlace (default: :: para todas las interfaces IPv6)')
    parser.add_argument('--port', type=int, default=8080, help='Número de puerto (default: 8080)')
    parser.add_argument('--username', required=True, help='Nombre de usuario para autenticación')
    parser.add_argument('--password', required=True, help='Contraseña para autenticación')

    args = parser.parse_args()

    run_server(args.bind, args.port, args.username, args.password)

if __name__ == '__main__':
    main()
