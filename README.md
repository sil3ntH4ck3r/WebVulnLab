# Pentesting-Web-Lab

Pentesting-Web-Lab es una herramienta que te permite desplegar dockers con diferentes vulnerabilidades web, para que puedas practicar en un sitio centralizado.
Una herramienta, que ha sido creada con las diferentes vulnerabilidades de DVWA, XVWA... con el fin de tener las máximas vulnerabilidades web en un mismo sitio, y acceder a ellas sin la necesidad de conexion a internet.

## Instalación

## Script de inslatación

> ACLARACIONES
> > Esta herramienta cuenta con un pagina web, que facilita el encendido y apagado de los docker mediante un boton. Para el funcionamiento correcto de dicha web, es necesario añadir esta linea: `www-data ALL=(ALL) NOPASSWD: ALL`, al final de todo del archvo `/etc/sudoers`. De lo contrario, toda la herramienta va a funcionar correctamente, pero tendras que encender y apagar las maquinas que desees manualmente.

Primero darle permisos de ejecuccion al script:

```
chmod +x <nombre_del_script>
```
Una vez echo, es tan facil como ejecutarlo:

```
./<nombre_del_script>
```

## Uso

Ahora que ya esta todo instalado, se nos habran desplegado todos los dockers.

| Docker             | Enlace                                     |
|:-------------------|:-------------------------------------------|
| Tablero            | http://localhost/Tablero/?show=include.php |
| Servidor Principal | http://localhost:8080/?show=include.php    |
| LFI                | http://localhost:8000/?show=include.php    |
| HTML Injection     | http://localhost:8001/?show=include.php    |
| CSRF               | http://localhost:8003/?show=include.php    |
| SSRF               | http://localhost:8004/?show=include.php    |
| SQL Injection      | http://localhost:8005/?show=include.php    |
| Blind SQL Injection| http://localhost:8006/?show=include.php    |


En el Servidor principal, cuando le das a `Empezar a Aprender` te llevara un menu donde tienes enlaces directos a cada docker, para poder practicar.
Tambien en el Tablero, puedes encender y apagar via web.

## Cosas para las siguientes actualizaciones

- Añadir mas vulnerabilidades.
- Ver en el tablero si que maquinas estan encendidas y que maquinas estan apagadas.
- Uso de Traefik para poner subdominios, y que no sea por IP
- Añadir alguna maquina CTF
- En cada maquina, añadir la opcion de aprender, donde podras descubrir mas sobre dicha vulnerabilidad.
- En el script de instalación, añadir que te instale el docker y las imagenes automaticamente.
- Mejorar la estetica.
- Añadir la posibilidad de reiniciar los contenedores.
- Hacer el proyecto de Hacking-Lab, pero para Windows.
