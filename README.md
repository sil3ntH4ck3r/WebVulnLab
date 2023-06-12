# Pentesting-Web-Lab (2.0v)

[English Version](README_en.md)

**IMPORTANTE:** Este proyecto actualmente sólo está disponible en español. Estamos trabajando en una traducción al inglés y otros idiomas.

¿Te gustaría aprender a detectar y explotar vulnerabilidades web de forma segura y divertida? ¿Quieres poner a prueba tus habilidades de hacking ético con retos reales y variados? Entonces no te pierdas la segunda versión de la herramienta de contenedores con vulnerabilidades web, creada por un apasionado de la seguridad informática.

En esta nueva versión, encontrarás una interfaz más atractiva y fácil de usar, así como nuevas funcionalidades que te permitirán desplegar y configurar los contenedores a tu gusto. Aunque todavía está en desarrollo, ya puedes descargarla y probar las primeras características. ¡Te sorprenderás!

## Requisitos

- docker (comando para instalar docker en Kali Linux: `sudo apt-get install docker.io`)
- php

## Instalación

**IMPORTANTE:** Ejecutar el script de instalación con permisos de superusuario.

Para instalar y utilizar la herramienta Pentesting-Web-Lab, sigue los siguientes pasos:

1. Descarga el repositorio de GitHub usando el siguiente comando en tu terminal:

```
git clone -b dev https://github.com/sil3ntH4ck3r/Pentesting-Web-Lab.git
```
> Si aún no tienes Git instalado, descárgalo e instálalo desde su [página oficial](https://git-scm.com/downloads)

> Para instalarlo en Ubuntu/Debian, puedes utilizar este comando:
```bash
sudo apt-get install git
```
Una vez descargado, el proyecto no requiere conexión a internet, ya que está diseñado para su uso en un entorno local.

2. Navega hasta el directorio donde clonaste el repositorio y da permisos de ejecución al script de instalación:

```bash
cd Pentesting-Web-Lab
chmod +x autoH4ck3nv.sh
```
> En caso que utilizes Arch Linux, el script de instalación es: autoH4ck3nvArchLinux.sh

3. Ejecuta el script de instalación:

```bash
sudo ./autoH4ck3nv.sh
```

Este script descargará e instalará todas las dependencias necesarias, creará los contenedores de Docker y configurará la herramienta para que puedas comenzar a utilizarla.

4. Una vez finalizada la instalación, podrás acceder a los diferentes contenedores a través de los siguientes enlaces:

| Docker               | Enlace                                     |Estatus                                |
|:---------------------|:-------------------------------------------|---------------------------------------|
| Tablero              | http://localhost/tablero/tablero.php       |Funcional                              |
| Servidor Principal   | http://menu.local/                         |Funcional                              |
| LFI                  | http://lfi.local/                          |Funcional                              |
| Padding Oracle Attack| http://paddingoracleattack.local/          |Funcional                              |
| Type Juggling        | http://typejuggling.local                  |Funcional                              |
| Remote File Inclusion| http://rfi.local                           |Funcional                              |
| XSS                  | http://xss.local/                          |Funcional                              |
| XXE                  | http://xxe.local/                          |Funcional                              |
| XPath Injection      | http://xpathinjection.local/               |Funcional                              |
| LaTeX Injection      | http://latexinjection.local/               |Funcional                              |
| ShellShock           | http://shellshock.local (archivo vulnerable -> /cgi-bin/system_info.cgi)|Desárrollandose (pero se puede probar)|
| Blind XXE            | http://blindxxe.local                      |Desárrollandose (pero se puede probar) |
| SQL Injection (Error)| http://sqli.local/                         |Desárrollandose (pero se puede probar) |
| Domain Zone Transfer | http://domainzonetransfer.local/           |Desárrollandose (pero se puede probar) |
| Insecure Deseralization | http://insecuredeseralization.local/    |Desárrollandose (pero se puede probar) |
| CSRF                 | http://csrf.local                          |Desárrollandose (pero se puede probar) |
| Blind XSS            |                    -                       |Desárrollandose                        |
| HTML Injection       |                    -                       |                   -                   |
| SSRF                 |                    -                       |                   -                   |
| SQL Injection (Time) |                    -                       |                   -                   |

Cabe destacar que aún está en desarrollo, y que no todos los contenedores están funcionando correctamente. Este repositorio se actualiza a menudo.

## Contribuir

Si deseas contribuir al desarrollo de Pentesting-Web-Lab, ¡eres bienvenido! Puedes hacerlo de varias maneras:

- Informando de bugs o problemas que encuentres en la herramienta a través de la sección de "Issues" en el repositorio de GitHub.
- Proponiendo nuevas características o mejoras.
- Ayudando a solucionar problemas o desarrollando nuevas vulnerabilidades.

## Actualización del proyecto

Puedes utilizar el siguiente script para verificar y aplicar actualizaciones del proyecto desde la consola.

### Requisitos

- Git: Asegúrate de tener Git instalado en tu sistema. Puedes verificar si Git está instalado ejecutando el siguiente comando en la terminal:

  ```shell
  git --version
  ```
  Si Git no está instalado, puedes seguir las instrucciones de instalación en [este enlace](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git).
### Pasos para actualizar el proyecto

1. Abre la terminal y navega hasta el directorio del proyecto:

```shell
cd ruta/al/proyecto
```
2. Ejecuta el script ***update.sh*** para verificar si hay actualizaciones disponibles:

```shell
./update.sh
```
3. Si hay actualizaciones disponibles y deseas instalarlas, sigue las instrucciones proporcionadas por el script. Por ejemplo, puedes ingresar ***s*** y presionar Enter para realizar la actualización. Si decides no instalar las actualizaciones, puedes ingresar ***n*** y presionar Enter.

***NOTA***: Asegúrate de leer atentamente las instrucciones y los mensajes que muestra el script antes de tomar cualquier acción.
El script verificará si hay nuevas versiones disponibles y te mostrará un mensaje indicando si hay actualizaciones.

4. Si la actualización se completa con éxito, el script mostrará un mensaje indicando que la actualización ha sido exitosa. En caso de algún problema durante la actualización, se mostrará un mensaje de error correspondiente.

## Cosas para las siguientes actualizaciones
Migrar todo a la segunda versión (cuando ya esté todo migrado, se avanzará en el desarrollo de nuevas vulnerabilidades).
- Añadir más contenedores y vulnerabilidades.
- Mejorar la documentación para que sea más fácil de entender y seguir.
- Refactorizar el código para hacerlo más legible y mantenible.
