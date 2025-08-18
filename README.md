<div align="center">
  
# 🛡️ WebVulnLab (v2.0)

![GitHub stars](https://img.shields.io/github/stars/sil3ntH4ck3r/WebVulnLab?style=social)
![GitHub forks](https://img.shields.io/github/forks/sil3ntH4ck3r/WebVulnLab?style=social)
![Contributors](https://img.shields.io/github/contributors/sil3ntH4ck3r/WebVulnLab?color=dark-green)
![License](https://img.shields.io/github/license/sil3ntH4ck3r/WebVulnLab?color=blue)
![Issues](https://img.shields.io/github/issues/sil3ntH4ck3r/WebVulnLab?color=red)

**Aprende ciberseguridad en un entorno seguro y controlado**

[🇺🇸 English](README_en.md) | [🇪🇸 Spanish](README.md)

<a href="https://www.buymeacoffee.com/sil3nth4ck3r" target="_blank"><img src="https://cdn.buymeacoffee.com/buttons/default-orange.png" alt="Buy Me A Coffee" height="41" width="174"></a>

</div>

## 📋 Índice
- [📝 Descripción](#-descripción)
- [✨ Características](#-características)
- [🔧 Requisitos](#-requisitos)
- [🔄 Personalización de la instalación](#-personalización-de-la-instalación)
- [🚀 Instalación](#-instalación)
- [🌐 Laboratorios Disponibles](#-laboratorios-disponibles)
- [⬆️ Actualización del Proyecto](#-actualización-del-proyecto)
- [👥 Contribuir](#-contribuir)
- [🔮 Próximas actualizaciones](#-próximas-actualizaciones)

## 📝 Descripción

**WebVulnLab** es un proyecto diseñado para que puedas **aprender a detectar y explotar vulnerabilidades web** de manera segura y divertida. Con esta segunda versión, encontrarás una interfaz más atractiva y amigable, así como nuevas características que te permitirán desplegar y configurar los contenedores a tu gusto.

> **NOTA IMPORTANTE:** Este proyecto actualmente solo está disponible en español, pero se están realizando esfuerzos para traducirlo a otros idiomas.

## ✨ Características

- 🛠️ **Más de 30 tipos de vulnerabilidades** para practicar
- 🔄 **Sistema de contenedores** fácil de desplegar y gestionar
- 🎯 **Entorno controlado** para pruebas éticas
- 🎨 **Interfaz mejorada** y más amigable
- 📊 **Panel de control** para gestionar los contenedores activos
- 🔒 **Vulnerabilidades actualizadas** constantemente

## 🔧 Requisitos

Antes de comenzar, asegúrate de tener instalados:

```bash
# Para Kali Linux
sudo apt-get install docker.io docker-compose php git
```

Puedes verificar si Git está instalado ejecutando:
```bash
git --version
```

## 🔄 Personalización de la instalación

Puedes personalizar qué contenedores instalar modificando el archivo `install.sh`. Cada elemento sigue este formato:

<details>
<summary>Ver detalles de personalización</summary>

- **Containers**:
```
container_name;$DIRECTORY_PATH;PUBLISHED_PORT:CONTAINER_PORT;additionalParameters;staticIPAddress
```

- **Otros**:
```
Command Description;command_to_execute
```

Para excluir contenedores específicos, simplemente comenta las líneas correspondientes:
```bash
containers=( 
  #"menu_v2;$PWD/menu;8080:80;;172.18.0.2"
  #"lfi_v2;$PWD/lfi;8000:80;;172.18.0.3"
  #"csrf_v2;$PWD/csrf;8001:80;;172.18.0.4"
  #"blindxxe_v2;$PWD/blindxxe;8002:80;;172.18.0.5"
  #"xxe_v2;$PWD/xxe;8003:80;;172.18.0.6"
  #...
)
```
</details>

## 🚀 Instalación

> ⚠️ **IMPORTANTE**: Ejecuta el script de instalación con privilegios de superusuario.

1. **Clona el repositorio**:
   ```bash
   git clone -b dev https://github.com/sil3ntH4ck3r/WebVulnLab.git
   ```

2. **Prepara el script de instalación**:
   ```bash
   cd WebVulnLab
   chmod +x install.sh
   ```
   > **Nota**: El archivo `install.sh` no es compatible con Arch Linux (se está trabajando en un nuevo script de instalación).

3. **Ejecuta el script**:
   ```bash
   sudo ./install.sh
   ```

4. **¡Todo listo!** Ahora puedes acceder a través de tu navegador.

## 🌐 Próximos Laboratorios

| Laboratorio | Estado |
|:------------|:------:|
**Inyección JNDI** | ⏳ |
**Memcache** | ⏳ |
**Buffer Overflow** | ⏳ |
**HTTP smuggling** | ⏳ |
**Active Directory** | ⏳ |
**Y mucho más!** |

<details>
<summary>Ver todos los laboratorios disponibles</summary>

| Laboratorio | Estado |
|:------------|:------:|
**[Dashboard](http://tablero.local/)** | ✅ |
**[Main Server](http://menu.local/)** | ✅ |
**[LFI](http://lfi.local/)** | ✅ |
**[Padding Oracle Attack](http://paddingoracleattack.local/)** | ✅ |
**[Type Juggling](http://typejuggling.local/)** | ✅ |
**[Remote File Inclusion](http://rfi.local/)** | ✅ |
**[XSS](http://xss.local/)** | ✅ |
|**[XXE](http://xxe.local/)** | ✅ |
**[XPath Injection](http://xpathinjection.local/)** | ✅ |
**[LaTeX Injection](http://latexinjection.local/)** | ✅ |
**[ShellShock](http://shellshock.local/)** | ✅ |
**[SQL Injection (Error)](http://sqli.local/)** | ✅ |
**[Blind SQL Injection (Time)](http://blindsqli.local/)** | ✅ |
**[Domain Zone Transfer](http://domainzonetransfer.local/)** | ✅ |
**[CSRF](http://csrf.local/)** | ✅ |
**[SSRF](http://ssrf.local/)** | ✅ |
**[Blind XXE](http://blindxxe.local/)** | ✅ |
**[Blind XSS](http://blindxss.local/)** | ✅ |
**[HTML Injection](http://htmlinjection.local/)** | ✅ |
**[PHP Insecure Deseralization](http://insecuredeseralizationphp.local/)** | ✅ |
**[Insecure Direct Object Reference (iDOR)](http://idor.local/)** | ✅ |
**[Server-Side Template Injection (SSTI)](http://ssti.local/)** | ✅ |
**[Client-Side Template Injection (CSTI)](http://csti.local/)** | ✅ |
**[NoSQL Injections](http://nosqlinjection.local/)** | ✅ |
**[LDAP Injections](http://ldapinjection.local/)** | ✅ |
**[API's Abuse and Mass-Asignament Attack](http://apiabuse.local/)** | ✅ |
**[File Upload Abuse](http://fileuploadabuse.local/)** | ✅ |
**[Prototype Pollution](http://prototypepollution.local/)** | ✅ |
**[Open Redirect](http://openredirect.local/)** | ✅ |
**[WebDAV](http://webdav.local/)** | ✅ |
**[SquidProxies](http://squidproxy.local/)** | ✅ |
**[CORS Vulnerability](http://cors.local)** | ✅ |
**[SQL Truncation](http://sqltruncation.local/)** | ✅ |
**[Session Puzzling/Fixation/Overloading](http://sessionpuzzling.local/)** | ✅ |
**[JSON Web Token](http://jwt.local/)** | ✅ |
**[Race Condition](http://racecondition.local/)** | ✅ |
**[CSS Injection](http://cssi.local/)** | ✅ |
**[Python Deserelization (DES-Yaml)](http://yamldeseralization.local/)** | ✅ |
**[Python Deserelization (DES-Pickle)](http://pickledeseralization.local/)** | ✅ |
**[GraphQL Introspection, Mutations](http://graphql.local/)** | ✅ |
**[OAuth / Werkzeug Debugger Console Abuse](http://oauth_gallery.local/)** | ✅ |
**[SNMP Abuse + IPv6](http://snmp.local/)** | ✅ |
**[AWS Lambda Abuse](http://aws.local/)** | ✅ |
**[HTTP/3](https://http3.local/)** | ✅ |
**[Redis](http://redis.local/)** | ✅ |
**[NodeJS IIFE Deserialization](http://nodejsdeserelization.local/)** | ✅ |
**[ESI Injection](http://esiinjection.local/)** | ✅ |
**[Cypher Injection](http://cypherinjection.local/)** | ✅ |
**[Deserialización insegura en Java](http://javadeserelization.local/)** | ✅ |

</details>

## ⬆️ Actualización del Proyecto

Para mantener WebVulnLab actualizado, sigue estos pasos:

1. **Navega al directorio del proyecto**:
   ```bash
   cd path/to/project
   ```

2. **Ejecuta el script de actualización**:
   ```bash
   ./update.sh
   ```

3. **Sigue las instrucciones** que aparecen en pantalla para instalar las actualizaciones disponibles.

## 👥 Contribuir

¡Tu contribución es bienvenida! Puedes ayudar de varias formas:

- 🐛 **Reportar bugs** o problemas que encuentres
- 💡 **Proponer nuevas características** o mejoras
- 🔧 **Ayudar a resolver problemas** o desarrollar nuevas vulnerabilidades
- 📚 **Mejorar la documentación** o traducirla a otros idiomas

## 🔮 Próximas actualizaciones

- [ ] Añadir más vulnerabilidades
- [x] Mostrar en el dashboard qué máquinas están encendidas y cuáles apagadas
- [x] Implementar Traefik para configurar subdominios en lugar de usar direcciones IP
- [x] Mejorar la estética
- [x] Añadir la capacidad de reiniciar contenedores
- [ ] Incluir una máquina CTF
- [ ] Añadir en cada máquina una opción de aprendizaje
- [ ] Crear una versión de este proyecto para Windows
- [x] Mejorar la documentación

---

<div align="center">
  
### 💖 Apoya este proyecto

Si te ha resultado útil este laboratorio, considera [invitarme a un café ☕](https://www.buymeacoffee.com/sil3nth4ck3r)

**Made with ❤️ by [sil3ntH4ck3r](https://github.com/sil3ntH4ck3r)**

</div>
