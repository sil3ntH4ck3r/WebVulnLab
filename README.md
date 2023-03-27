# Pentesting-Web-Lab

Pentesting-Web-Lab es una herramienta que te permite desplegar dockers con diferentes vulnerabilidades web, para que puedas practicar en un sitio centralizado.
Una herramienta, que ha sido creada con las diferentes vulnerabilidades de DVWA, XVWA... con el fin de tener las máximas vulnerabilidades web en un mismo sitio, y acceder a ellas sin la necesidad de conexión a internet.

## Instalación

## Script de instalación

Primero darle permisos de ejecución al script:

```
chmod +x <nombre_del_script>
```
Una vez echo, es tan fácil como ejecutarlo:

```
./<nombre_del_script>
```

## Uso

Ahora que ya está todo instalado, se nos habrán desplegado todos los dockers.

| Docker               | Enlace                                     |Estatus                                |
|:---------------------|:-------------------------------------------|---------------------------------------|
| Tablero              | http://localhost/tablero/tablero.php       |Funcional                              |
| Servidor Principal   | http://localhost:8080/                     |Funcional                              |
| LFI                  | http://localhost:8000/                     |Funcional                              |
| SQL Injection        | http://localhost:8005/                     |Desárrollandose (pero se puede probar) |
| Padding Oracle Attack| http://localhost:8007/login.php            |Desárrollandose (pero se puede probar) |

Cabe destacar que aún está en desarrollo, y que no todos los dockers están funcionado correctamente. Este repositorio se actualiza cada día.

## Cosas para las siguientes actualizaciones

- Migrarlo todo a la segunda versión (cunado ya este todo migrado, se avanzará en el desarrollo de nuevas vulnerabilidades) 
