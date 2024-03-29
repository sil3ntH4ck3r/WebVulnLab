# Pentesting-Web-Lab (DESACTUALIZADO) [Versión 2.0](https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev)

**Este proyecto ya no está recibiendo actualizaciones y se considera en estado de mantenimiento.** Sin embargo, se ha lanzado una versión 2.0 que incluye mejoras y correcciones adicionales. Si deseas utilizar la versión más reciente, puedes encontrarla [aqui](https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev).


Pentesting-Web-Lab es una herramienta que te permite desplegar dockers con diferentes vulnerabilidades web, para que puedas practicar en un sitio centralizado.
Una herramienta, que ha sido creada con las diferentes vulnerabilidades de DVWA, XVWA... con el fin de tener las máximas vulnerabilidades web en un mismo sitio, y acceder a ellas sin la necesidad de conexión a internet.

## Instalación

## Script de instalación

> ACLARACIONES
>> Esta herramienta cuenta con un página web, que facilita el encendido y apagado de los docker mediante un botón. Para el funcionamiento correcto de dicha web, es necesario añadir esta línea: 'www-data ALL=(ALL) NOPASSWD: ALL', al final de todo del archvo '/etc/sudoers'. De lo contrario, toda la herramienta va a funcionar correctamente, pero tendrás que encender y apagar las máquinas que desees manualmente.

Primero darle permisos de ejecución al script:

```
chmod +x <nombre_del_script
```
Una vez echo, es tan fácil como ejecutarlo:

```
./<nombre_del_script
```

## Uso

Ahora que ya está todo instalado, se nos habrán desplegado todos los dockers.

| Docker | Enlace |
|:-------------------|:-------------------------------------------|
| Tablero | http://localhost/Tablero/?show=include.php |
| Servidor Principal | http://localhost:8080/?show=include.php |
| LFI | http://localhost:8000/?show=include.php |
| HTML Injection | http://localhost:8001/?show=include.php |
| CSRF | http://localhost:8003/?show=include.php |
| SSRF | http://localhost:8004/?show=include.php |
| SQL Injection | http://localhost:8005/?show=include.php |
| Blind SQL Injection| http://localhost:8006/?show=include.php |
| Padding Oracle Attack| http://localhost:8007/?show=include.php |

En el Servidor principal, cuando le das a 'Empezar a Aprender' te llevara un menú donde tienes enlaces directos a cada docker, para poder practicar.
También en el Tablero, puedes encender y apagar vía web.

## Cosas para las siguientes actualizaciones (Segunda versión, se puede acceder [aquí](https://github.com/sil3ntH4ck3r/Pentesting-Web-Lab/tree/dev))

- Añadir más vulnerabilidades.
- ~~Ver en el tablero que máquinas están encendidas y que máquinas están apagadas.~~
- ~~Uso de Traefik para poner subdominios, y que no sea por IP~~
    - Al final no se ha echo con Traefik, sino que se ha aplicado Virtual Hosting. Ahora los enlaces a los contenedores son más fáciles de recordar.
- Añadir alguna máquina CTF
- En cada máquina, añadir la opción de aprender, donde podrás descubrir más sobre dicha vulnerabilidad.
- Mejorar la estética (estoy trabajando en ello).
- ~~Añadir la posibilidad de reiniciar los contenedores.~~
- Hacer este mismo proyecto (Pentesting-Web-Lab), pero para Windows.
