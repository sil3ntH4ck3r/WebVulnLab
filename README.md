# WebVulnLab (2.0v)

[English Version](README_en.md)

**IMPORTANTE:** Este proyecto actualmente sólo está disponible en español. Estamos trabajando en una traducción al inglés y otros idiomas.

¿Te gustaría aprender a detectar y explotar vulnerabilidades web de forma segura y divertida? ¿Quieres poner a prueba tus habilidades de hacking ético con retos reales y variados? Entonces no te pierdas la segunda versión de la herramienta de contenedores con vulnerabilidades web, creada por un apasionado de la seguridad informática.

En esta nueva versión, encontrarás una interfaz más atractiva y fácil de usar, así como nuevas funcionalidades que te permitirán desplegar y configurar los contenedores a tu gusto. Aunque todavía está en desarrollo, ya puedes descargarla y probar las primeras características. ¡Te sorprenderás!

## Contenido
- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Actualización del proyecto](#actualización)
- [Errores comunes](#errores)
- [Contribuir](#contribuir)
- [Cosas para las siguientes actualizaciones](#cosasparalassiguientesactulizaciones)

## Requisitos <a name="requisitos"></a>

- docker (comando para instalar docker en Kali Linux: `sudo apt-get install docker.io`)
- php
- Git: Asegúrate de tener Git instalado en tu sistema. Puedes verificar si Git está instalado ejecutando el siguiente comando en la terminal:

  ```shell
  git --version
  ```
  Si Git no está instalado, puedes seguir las instrucciones de instalación en [este enlace](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git).

## Instalación <a name="instalación"></a>

**IMPORTANTE:** Ejecutar el script de instalación con permisos de superusuario.

Para instalar y utilizar la herramienta WebVulnLab, sigue los siguientes pasos:

1. Descarga el repositorio de GitHub usando el siguiente comando en tu terminal:

```
git clone -b dev https://github.com/sil3ntH4ck3r/WebVulnLab.git
```
> Si aún no tienes Git instalado, descárgalo e instálalo desde su [página oficial](https://git-scm.com/downloads)

> Para instalarlo en Ubuntu/Debian, puedes utilizar este comando:
```bash
sudo apt-get install git
```
Una vez descargado, el proyecto no requiere conexión a internet, ya que está diseñado para su uso en un entorno local.

2. Navega hasta el directorio donde clonaste el repositorio y da permisos de ejecución al script de instalación:

```bash
cd WebVulnLab
chmod +x install.sh
```
> En caso que utilizes Arch Linux, el script de instalación es: installArchLinux.sh

3. Ejecuta el script de instalación:

```bash
sudo ./install.sh
```

Este script descargará e instalará todas las dependencias necesarias, creará los contenedores de Docker y configurará la herramienta para que puedas comenzar a utilizarla.

4. Una vez finalizada la instalación, podrás acceder a los diferentes contenedores a través de los siguientes enlaces:

| Docker               | Enlace                                     |Estatus                                |
|:---------------------|:-------------------------------------------|---------------------------------------|
| Tablero              | http://tablero.loca/                       |Funcional                              |
| Servidor Principal   | http://menu.local/                         |Funcional                              |
| LFI                  | http://lfi.local/                          |Funcional                              |
| Padding Oracle Attack| http://paddingoracleattack.local/          |Funcional                              |
| Type Juggling        | http://typejuggling.local/                 |Funcional                              |
| Remote File Inclusion| http://rfi.local/                          |Funcional                              |
| XSS                  | http://xss.local/                          |Funcional                              |
| XXE                  | http://xxe.local/                          |Funcional                              |
| XPath Injection      | http://xpathinjection.local/               |Funcional                              |
| LaTeX Injection      | http://latexinjection.local/               |Funcional                              |
| ShellShock           | http://shellshock.local/                   |Funcional                              |
| SQL Injection (Error)| http://sqli.local/                         |Funcional                              |
| Blind SQL Injection (Time)| http://blindsqli.local/               |Funcional                              |
| Domain Zone Transfer | http://domainzonetransfer.local/           |Funcional                              |
| CSRF                 | http://csrf.local/                         |Funcional                              |
| SSRF                 | http://ssrf.local/                         |Funcional                              |
| Blind XXE            | http://blindxxe.local/                     |Funcional                              |
| Blind XSS            | http://blindxss.local/                     |Funcional                              |
| HTML Injection       | http://htmlinjection.local/                |Funcional                              |
| PHP Insecure Deseralization | http://insecuredeseralizationphp.local/    |Funcional                       |
| Insecure Direct Object Reference (iDOR) | http://idor.local/      |Funcional                              |
| Server-Side Template Injection (SSTI) | http://ssti.local/        |Funcional                              |
| Client-Side Template Injection (CSTI)| http://csti.local/         |Funcional                              |
| NoSQL Injections     | http://nosqlinjection.local/               |Funcional                              |
| LDAP Injections      | http://ldapinjection.local/                |Functional                             |
| API's Abuse y Mass-Asignament Attack| http://apiabuse.local/      |Funcional                              |
| File Upload Abuse    | http://fileuploadabuse.local/              |Funcional                              |
| Prototype Pollution  | http://prototypepollution.local/           |Funcional                              |
| Open Redirect| - | En desarrollo |
| WebDAV| - | En desarrollo |
| SquidProxies| http://squidproxy.local/| Funcional |
| Intercambio de recursos de origen cruzado (CORS) | http://localhost:8029 | Semifuncional |
|                                                | Nota: la vulnerabilidad CORS se debe practicar utilizando `localhost:8029`, ya que no hemos logrado que funcione a través de `cors.local`. |
| SQL Truncation| http://sqltruncation.local/ | Funcional |
|Session Puzzling / Session Fixation / Session Variable Overloading| http://sessionpuzzling.local/ | Funcional |
| Json Web Token| - | - |
| Race Condition| - | - |
| CSS Injection| - | - |
| Python Deserelization (DES-Yaml)| - | - |
| Python Deserelization (DES-Pickle)| - | - |
| GraphQL Introspection, Mutations e IDORs| - | - |

Cabe destacar que aún está en desarrollo, y que no todos los contenedores están funcionando correctamente. Este repositorio se actualiza a menudo.

## Actualización del proyecto <a name="actualización"></a>

Puedes utilizar el siguiente script para verificar y aplicar actualizaciones del proyecto desde la consola.

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

## Errores comunes  <a name="errores"></a>

### Error al resolver la URL de la vulnerabilidad

Si no puedes resolver la URL de la vulnerabilidad, pueden existir varios factores que contribuyan a este problema. Aquí hay algunas posibles soluciones:

1. **Contenedores no iniciados correctamente:**

    - Comprueba si los contenedores están activos ejecutando el comando `docker ps` en la línea de comandos. Si los contenedores están en funcionamiento, es posible que el problema esté relacionado con la configuración de Apache.
    - Si los contenedores no aparecen en la lista o se muestra un estado de "exited" al ejecutar el comando `docker ps -a`, es probable que haya un problema con el inicio de los contenedores. Asegúrate de seguir las instrucciones de configuración y los comandos de inicio adecuados para los contenedores.

2. **Configuración de Apache inactiva:**

    - Verifica que la configuración de Apache esté activa y correctamente configurada. Revisa los archivos de configuración relevantes, como el archivo de configuración principal de Apache (/etc/apache2/sites-available/WebVulnLab.conf), para asegurarte de que todos los ajustes necesarios estén presentes y sean correctos.
    - Asegúrate de haber reiniciado Apache después de realizar cambios en la configuración o despues de encender el equipo. Puedes hacerlo ejecutando el comando adecuado según tu sistema operativo (sudo service apache2 restart en Linux, por ejemplo).

3. **Solicitar ayuda a través de la sección "Issues" del proyecto:**

    - Si has intentado las soluciones anteriores y aún no puedes resolver el problema, puedes pedir ayuda a través de la sección de "Issues" en el repositorio de GitHub del proyecto. Describe detalladamente el problema que estás enfrentando, incluyendo cualquier mensaje de error relevante, y proporciona información sobre tu entorno de ejecución (sistema operativo, versiones de software, etc.).

### Servicio no disponible al ingresar el dominio

Si al ingresar el dominio te muestra el mensaje "Service Unavailable", es probable que el contenedor correspondiente no esté encendido. Aquí hay algunas posibles soluciones:

1. **Verificar estado del contenedor:**

    - Utiliza el comando `docker ps` en la línea de comandos para verificar si el contenedor necesario está en ejecución.
    - Si el contenedor no aparece en la lista o muestra un estado de "exited" al ejecutar el comando `docker ps -a`, es posible que haya ocurrido un problema durante el inicio del contenedor. Asegúrate de seguir las instrucciones adecuadas para iniciar correctamente el contenedor.

Si el contenedor no está en ejecución, puedes seguir estos pasos adicionales:

2. **Reiniciar el contenedor a través de la API REST de Docker:**

    - Accede al dominio tablero.local en tu navegador. Este dominio está habilitado para interactuar con la API REST de Docker y te permite controlar los contenedores.
    - Utiliza las funcionalidades provistas por el tablero para reiniciar el contenedor específico que no está en funcionamiento.
    - Verifica si el reinicio del contenedor a través de tablero.local resuelve el problema y permite el acceso al dominio.

    **NOTA:** Si no puedes acceder al dominio tablero.local, te recomiendo seguir los pasos mencionados en la sección anterior de "Errores Comunes" del README, puede clicar [aquí](#errores). Puedes encontrar información sobre cómo solucionar si la configuración de Apache esta inactiva, entre otros.

Si después de reiniciar el contenedor aún enfrentas el error "Service Unavailable", considera estas posibles soluciones adicionales:

3. **Revisar los registros del contenedor:**

    - Utiliza el comando `docker logs <nombre_del_contenedor>` para ver los registros del contenedor y buscar posibles errores o problemas durante la ejecución.
    - Examina los registros en busca de mensajes de error o advertencias que puedan proporcionar información sobre el motivo detrás del servicio no disponible.

4. **Solicitar ayuda a través de la sección "Issues" del proyecto:**

Si has intentado las soluciones anteriores y aún no puedes resolver el problema, puedes pedir ayuda a través de la sección de "Issues" en el repositorio de GitHub del proyecto. Describe detalladamente el problema que estás enfrentando, incluyendo cualquier mensaje de error relevante, y proporciona información sobre tu entorno de ejecución (sistema operativo, versiones de software, etc.).

### Error de Proxy

Si al ingresar el dominio te muestra el mensaje "Proxy Error", es probable que el contenedor correspondiente no tenga el servicio Apahce encendido. Aquí hay algunas posibles soluciones:

1. **Encender servicio Apache**

    - Dentro del contenedor, ejecuta el siguiente comando para verificar si el servicio Apache está en funcionamiento:

        ```
        service apache2 status
        ```

    - Si el servicio está detenido, puedes iniciarlo ejecutando:

        ```
        service apache2 start
        ```
    **NOTA:** Para ejecutar comandos dentro de un contenedor y obtener una consola interactiva, puedes utilizar el comando `docker exec -it <nombre_del_contenedor> /bin/bash`. Cuando hayas terminado de ejecutar los comandos en el contenedor, puedes salir de la consola interactiva escribiendo `exit`.



## Contribuir <a name="contribuir"></a>

Si deseas contribuir al desarrollo de WebVulnLab, ¡eres bienvenido! Puedes hacerlo de varias maneras:

- Informando de bugs o problemas que encuentres en la herramienta a través de la sección de "Issues" en el repositorio de GitHub.
- Proponiendo nuevas características o mejoras.
- Ayudando a solucionar problemas o desarrollando nuevas vulnerabilidades.

## Cosas para las siguientes actualizaciones <a name="cosasparalassiguientesactulizaciones"></a>

- Añadir más vulnerabilidades.
- ~~Ver en el tablero que máquinas están encendidas y que máquinas están apagadas.~~
- ~~Uso de Traefik para poner subdominios, y que no sea por IP~~
  - Al final no se ha echo con Traefik, sino que se ha aplicado Virtual Hosting. Ahora los enlaces a los contenedores son más fáciles de recordar.
- ~~Mejorar la estética~~.
- ~~Añadir la posibilidad de reiniciar los contenedores.~~
- Añadir alguna máquina CTF
- En cada máquina, añadir la opción de aprender, donde podrás descubrir más sobre dicha vulnerabilidad.
- Hacer este mismo proyecto (WebVulnLab), pero para Windows.
- Mejorar la documentación para que sea más fácil de entender y seguir.
- Refactorizar el código para hacerlo más legible y mantenible.
