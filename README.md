# Pentesting-Web-Lab

¿Te gustaría aprender a detectar y explotar vulnerabilidades web de forma segura y divertida? ¿Quieres poner a prueba tus habilidades de hacking ético con retos reales y variados? Entonces no te pierdas la segunda versión de la herramienta de contenedores con vulnerabilidades web, creada por un apasionado de la seguridad informática. En esta nueva versión, encontrarás una interfaz más atractiva y fácil de usar, así como nuevas funcionalidades que te permitirán desplegar y configurar los contenedores a tu gusto. Aunque todavía está en desarrollo, ya puedes descargarla y probar las primeras características. No esperes más y descubre lo que esta herramienta puede hacer por ti y por tu aprendizaje. ¡Te sorprenderás!

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
