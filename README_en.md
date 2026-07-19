<div align="center">
  
# 🛡️ WebVulnLab (v2.0)

[![Ask DeepWiki](https://deepwiki.com/badge.svg)](https://deepwiki.com/sil3ntH4ck3r/WebVulnLab)
![GitHub stars](https://img.shields.io/github/stars/sil3ntH4ck3r/WebVulnLab?style=social)
![GitHub forks](https://img.shields.io/github/forks/sil3ntH4ck3r/WebVulnLab?style=social)
![Contributors](https://img.shields.io/github/contributors/sil3ntH4ck3r/WebVulnLab?color=dark-green)
![License](https://img.shields.io/github/license/sil3ntH4ck3r/WebVulnLab?color=blue)
![Issues](https://img.shields.io/github/issues/sil3ntH4ck3r/WebVulnLab?color=red)

**Learn cybersecurity in a safe and controlled environment**

[🇺🇸 English](README_en.md) | [🇪🇸 Spanish](README.md)

<a href="https://www.buymeacoffee.com/sil3nth4ck3r" target="_blank"><img src="https://cdn.buymeacoffee.com/buttons/default-orange.png" alt="Buy Me A Coffee" height="41" width="174"></a>

</div>

## 📋 Table of Contents
- [📝 Description](#-description)
- [✨ Features](#-features)
- [🚀 Installation](#-installation)
- [🌐 Available Labs](#-available-labs)
- [⬆️ Project Updates](#-project-updates)
- [👥 Contributing](#-contributing)

## 📝 Description

**WebVulnLab** is a project designed for you to **learn how to detect and exploit web vulnerabilities** in a safe and fun way. With this second version, you'll find a more attractive and user-friendly interface, as well as new features that allow you to deploy and configure containers to your liking.

> **IMPORTANT NOTE:** This project is currently only available in Spanish, but efforts are being made to translate it into other languages.

## ✨ Features

- 🛠️ **More than 30 types of vulnerabilities** to practice
- 🔄 **Container system** easy to deploy and manage
- 🎯 **Controlled environment** for ethical testing
- 🎨 **Improved interface** that's more user-friendly
- 📊 **Control panel** to manage active containers
- 🔒 **Constantly updated vulnerabilities**

## 🚀 Installation

> ⚠️ **IMPORTANT**: Run with superuser privileges on a Debian/Kali system with a graphical environment (Tk). If Tk is missing, install it with python3-tk.

0. **Prerequisites**

- Debian/Kali (bookworm or similar), root/sudo, Internet connection.

- Python 3 + Tkinter (sudo apt-get install -y python3-tk if missing).

1. **Clone the repository:**
   ```bash
   git clone -b dev https://github.com/sil3ntH4ck3r/WebVulnLab.git
   ```
2. **Run the installer (graphical interface):**
   ```bash
   sudo python3 install.py
   ```
   > **Note**: The installer is designed for Debian/Kali; it may not work on other distros.

3. **Automatic installation and configuration**

In the UI, click Install from scratch.
This will install Docker (if missing), set up IPv6 for Docker, create the `WebVulnLab-Network`, generate certificates (`http3.local` and `menu.local` with mkcert), compile `ttyd`, copy the dashboard to Apache, and configure `tablero.local`.

4. **Customize the labs**

In the Containers tab, select the ones you want and click (De)activate to enable/disable them.

- For “Dockerfile” containers: use Build + Run selected.
- For docker-compose labs (tab Labs docker-compose): use Run Compose selected.

4. **All set!** Now you can access http://tablero.local from your browser.

## 🌐 Upcoming Labs

| Lab         | Status |
|:------------|:------:|
**WASM Out Of Bounds** | ⏳ |
**WASM Overflow** | ⏳ |
**CRLF Injection** | ⏳ |
**HTTP smuggling** | ⏳ |
**Active Directory** | ⏳ |
**And much more!** |

<details>
<summary>View all available labs</summary>

| Lab | Status |
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
**[Java Deserelization](http://javadeserelization.local/)** | ✅ |
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

## ⬆️ Project Updates

To keep WebVulnLab updated, follow these steps:

1. **Navigate to the project directory**:
   ```bash
   cd path/to/project
   ```

2. **Run the update script**:
   ```bash
   ./update.sh
   ```

3. **Follow the instructions** that appear on screen to install available updates.

## 👥 Contributing

Your contribution is welcome! You can help in several ways:

- 🐛 **Report bugs** or issues you find
- 💡 **Propose new features** or improvements
- 🔧 **Help solve problems** or develop new vulnerabilities
- 📚 **Improve documentation** or translate it to other languages

## 🔮 Upcoming Updates

- [ ] Add more vulnerabilities
- [x] Display in the dashboard which machines are on and which are off
- [x] Implement Traefik to configure subdomains instead of using IP addresses
- [x] Improve aesthetics
- [x] Add the ability to restart containers
- [ ] Include a CTF machine
- [ ] Add a learning option on each machine
- [ ] Create a version of this project for Windows
- [x] Improve documentation

---

<div align="center">
  
### 💖 Support this project

If you found this lab useful, consider [buying me a coffee ☕](https://www.buymeacoffee.com/sil3nth4ck3r)

**Made with ❤️ by [sil3ntH4ck3r](https://github.com/sil3ntH4ck3r)**

</div>
