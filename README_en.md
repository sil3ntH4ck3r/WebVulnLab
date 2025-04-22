<div align="center">
  
# 🛡️ WebVulnLab (v2.0)

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
- [🔧 Requirements](#-requirements)
- [🔄 Customizing the Installation](#-customizing-the-installation)
- [🚀 Installation](#-installation)
- [🌐 Available Labs](#-available-labs)
- [⬆️ Project Updates](#-project-updates)
- [👥 Contributing](#-contributing)
- [🔮 Upcoming Updates](#-upcoming-updates)

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

## 🔧 Requirements

Before starting, make sure you have the following installed:

```bash
# For Kali Linux
sudo apt-get install docker.io docker-compose php git
```

You can verify if Git is installed by running:
```bash
git --version
```

## 🔄 Customizing the Installation

You can customize which containers to install by modifying the `install.sh` file. Each item follows this format:

<details>
<summary>View customization details</summary>

- **Containers**:
```
container_name;$DIRECTORY_PATH;PUBLISHED_PORT:CONTAINER_PORT;additionalParameters;staticIPAddress
```

- **Others**:
```
Command Description;command_to_execute
```

To exclude specific containers, simply comment out the corresponding lines:
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

## 🚀 Installation

> ⚠️ **IMPORTANT**: Run the installation script with superuser privileges.

1. **Clone the repository**:
   ```bash
   git clone -b dev https://github.com/sil3ntH4ck3r/WebVulnLab.git
   ```

2. **Prepare the installation script**:
   ```bash
   cd WebVulnLab
   chmod +x install.sh
   ```
   > **Note**: The `install.sh` file is not compatible with Arch Linux (a new installation script is being developed).

3. **Run the script**:
   ```bash
   sudo ./install.sh
   ```

4. **All set!** Now you can access it through your browser.

## 🌐 Upcoming Labs

| Lab         | Status |
|:------------|:------:|
**Java Deserelization** | ⏳ |
**JNDI Injection** | ⏳ |
**Memcache** | ⏳ |
**Buffer Overflow** | ⏳ |
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
