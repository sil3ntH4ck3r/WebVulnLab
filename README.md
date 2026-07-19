<div align="center">
  
# 🛡️ WebVulnLab (v2.0)

[![Ask DeepWiki](https://deepwiki.com/badge.svg)](https://deepwiki.com/sil3ntH4ck3r/WebVulnLab)
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
- [🚀 Instalación](#-instalación)
- [🌐 Laboratorios Disponibles](#-laboratorios-disponibles)
- [⬆️ Actualización del Proyecto](#-actualización-del-proyecto)
- [👥 Contribuir](#-contribuir)

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

## 🚀 Instalación

> ⚠️ **IMPORTANTE**: ejecútalo con privilegios de superusuario y en un sistema Debian/Kali con entorno gráfico (Tk). Si no tienes Tk, instala python3-tk.

0. **Requisitos previos**

- Debian/Kali (bookworm o similar), root/sudo, conexión a Internet.
- Python 3 + Tkinter (sudo apt-get install -y python3-tk si falta).

1. **Clona el repositorio**:
   ```bash
   git clone -b dev https://github.com/sil3ntH4ck3r/WebVulnLab.git
   ```

2. **Ejecuta el instalador (interfaz gráfica)**:
   ```bash
   sudo python3 install.py
   ```
   > **Nota**: El instalador está pensado para Debian/Kali; en otras distros puede no funcionar.

3. **Instala y configura automáticamente**

En la UI, haz clic en Instalar desde cero.
Esto instalará Docker (si falta), preparará IPv6 para Docker, creará la red `WebVulnLab-Network`, generará certificados (`http3.local` y `menu.local` con mkcert), compilará `ttyd`, copiará el tablero a Apache y configurará `tablero.local`.

4. **Personaliza los laboratiorios**

En la pestaña Contenedores, marca los que quieras y pulsa (Des)activar para activar/desactivar.

- Para contenedores “Dockerfile”: usa Build + Run seleccionados.
- Para labs docker-compose (pestaña “Labs docker-compose”): usa Run Compose seleccionados.

4. **¡Todo listo!** Ahora puedes acceder a http://tablero.local través de tu navegador.

## 🌐 Próximos Laboratorios

| Laboratorio | Estado |
|:------------|:------:|
**WASM Out Of Bounds** | ⏳ |
**WASM Overflow** | ⏳ |
**CRLF Injection** | ⏳ |
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
w**[File Upload Abuse](http://fileuploadabuse.local/)** | ✅ |
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
**[JNDI Injection](http://jndiinjection.local/)** | ✅ |
**[Web Cache Poisoning](http://webcachepoisoning.local/)** | ✅ |
**[UUID](http://uuid.local/)** | ✅ |
**[RSQL Injection](http://rsqli.local/)** | ✅ |
**Capabilities** | ✅ |
**Cronjob** | ✅ |
**Path Hijacking** | ✅ |
**Python Library Hijacking** | ✅ |
**Service Abuse** | ✅ |
**Special Groups** | ✅ |
**Specific Binaries** | ✅ |
**Sudoers** | ✅ |
**SUID** | ✅ |

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

---

<div align="center">
  
### 💖 Apoya este proyecto

Si te ha resultado útil este laboratorio, considera [invitarme a un café ☕](https://www.buymeacoffee.com/sil3nth4ck3r)

**Made with ❤️ by [sil3ntH4ck3r](https://github.com/sil3ntH4ck3r)**

</div>
