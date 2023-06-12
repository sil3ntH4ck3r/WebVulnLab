# WebVulnLab (2.0v)

[Spanish Version](README.md)

**IMPORTANT NOTE:** Please note that this project is currently only available in Spanish, but efforts are being made to translate it into other languages.

Would you like to learn how to detect and exploit web vulnerabilities safely and enjoyably? Do you want to test your ethical hacking skills with real and varied challenges? Then don't miss the second version of the web vulnerability container tool, created by a passionate cybersecurity enthusiast.

In this new version, you will find a more attractive and user-friendly interface, as well as new features that will allow you to deploy and configure the containers to your liking. Although it is still in development, you can already download it and try out the first features. You will be surprised!

## Requirements

- docker (command to install docker on Kali Linux: `sudo apt-get install docker.io`)
- php

## Installation

**IMPORTANT NOTE**: Run the installation script with superuser privileges.

To install and use the WebVulnLab tool, follow these steps:

1. Download the GitHub repository using the following command in your terminal:

```
git clone -b dev https://github.com/sil3ntH4ck3r/WebVulnLab.git
```
> If you don't have Git installed yet, download and install it from its [official website](https://git-scm.com/downloads).

> To install it on Ubuntu/Debian, you can use this command:
```bash
sudo apt-get install git
```

2. Navigate to the directory where you cloned the repository and give execution permissions to the installation script:

```bash
cd WebVulnLab
chmod +x install.sh
```

3. Run the installation script:

```bash
sudo ./install.sh
```

This script will download and install all the necessary dependencies, create the Docker containers, and configure the tool so you can start using it.

4. Once the installation is complete, you can access the different containers through the following links:

| Docker               | Container Link                             |Status                                |
|:---------------------|:-------------------------------------------|--------------------------------------|
| Dashboard            | http://tablero.local                       |Functional                            |
| Main Server          | http://menu.local/                         |Functional                            |
| LFI                  | http://lfi.local/                          |Functional                            |
| Padding Oracle Attack| http://paddingoracleattack.local/          |Functional                            |
| Type Juggling        | http://typejuggling.local                  |Functional                            |
| Remote File Inclusion|http://rfi.local                            |Functional                            |
| XSS                  | http://xss.local/                          |Functional                            |
| XXE                  | http://xxe.local/                          |Functional                            |
| XPath Injection      | http://xpathinjection.local/               |Functional                            |
| LaTeX Injection      | http://latexinjection.local/               |Functional                            |
| ShellShock           | http://shellshock.local                    |Functional                            |
| Blind XXE            | http://blindxxe.local                      |In development (but can be tested)    |
| SQL Injection (Error)| http://sqli.local/                         |In development (but can be tested)    |
| Domain Zone Transfer | http://domainzonetransfer.local/           |In development (but can be tested)    |
| CSRF                 | http://csrf.local                          |In development (but can be tested)    |
| Insecure Deseralization | http://insecuredeseralization.local/    |In development                        |
| Blind XSS            |                    -                       |In development                        |
| HTML Injection       |                    -                       |                   -                  |
| SSRF                 |                    -                       |                   -                  |
| SQL Injection (Time) |                    -                       |                   -                  |

Note that it is still in development, and not all containers are working correctly. This repository is updated frequently.

## Project Update

You can use the following script to check and apply updates to the project from the console.

### Requirements

- Git: Make sure you have Git installed on your system. You can check if Git is installed by running the following command in the terminal:
```shell
git --version
```
If Git is not installed, you can follow the installation instructions provided [here](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git).

### Steps to Update the Project

1. Open the terminal and navigate to the project directory:
```shell
cd path/to/project
```
2. Execute the script ***update.sh*** to check for available updates:
```shell
./update.sh
```
The script will check if there are new versions available and display a message indicating the presence of updates.
3. If updates are available and you wish to install them, follow the instructions provided by the script. For example, you can enter ***s*** and press Enter to perform the update. If you decide not to install the updates, you can enter ***n*** and press Enter.

***NOTE***: Make sure to carefully read the instructions and messages displayed by the script before taking any action.

4. If the update is successfully completed, the script will display a message indicating the successful update. In case of any issues during the update, an appropriate error message will be shown.

By following these steps, you will be able to check and apply updates to the project using the provided script.

## Contribute

If you want to contribute to the development of Pentesting-Web-Lab, you are welcome to do so! You can do it in several ways:

- Reporting bugs or issues you find in the tool through the "Issues" section in the GitHub repository.
- Proposing new features or improvements.
- Helping to solve problems or developing new vulnerabilities.

## Things for the upcoming updates:

- Add more vulnerabilities.
- ~~Display on the dashboard which machines are turned on and which machines are turned off.~~
- ~~Implement Traefik for setting up subdomains instead of using IP addresses.~~
  - (**Note**: Eventually, Traefik wasn't used, and Virtual Hosting was applied instead. Now, the links to the containers are easier to remember.)
- ~~Improve the aesthetics.~~
- ~~Add the ability to restart containers.~~
- Include a CTF machine.
- On each machine, add a learning option where users can discover more about the specific vulnerability.
- Create a version of this project (WebVulnLab) for Windows.
- Enhance the documentation to make it more user-friendly and easy to follow.
- Refactor the code to improve readability and maintainability.
