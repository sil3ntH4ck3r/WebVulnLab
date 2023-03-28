# Pentesting-Web-Lab

[Spanish Version](README.md)

**IMPORTANT NOTE:** Please note that this project is currently only available in Spanish, but efforts are being made to translate it into other languages.

Would you like to learn how to detect and exploit web vulnerabilities safely and enjoyably? Do you want to test your ethical hacking skills with real and varied challenges? Then don't miss the second version of the web vulnerability container tool, created by a passionate cybersecurity enthusiast.

In this new version, you will find a more attractive and user-friendly interface, as well as new features that will allow you to deploy and configure the containers to your liking. Although it is still in development, you can already download it and try out the first features. You will be surprised!

## Installation

To install and use the Pentesting-Web-Lab tool, follow these steps:

1. Download the GitHub repository using the following command in your terminal:

```
git clone -b dev https://github.com/sil3ntH4ck3r/Pentesting-Web-Lab.git
```
> If you don't have Git installed yet, download and install it from its [official website](https://git-scm.com/downloads).

> To install it on Ubuntu/Debian, you can use this command:
```bash
sudo apt-get install git
```

2. Navigate to the directory where you cloned the repository and give execution permissions to the installation script:

```bash
cd Pentesting-Web-Lab
chmod +x autoH4ck3nv.sh
```

3. Run the installation script:

```bash
./autoH4ck3nv.sh
```

This script will download and install all the necessary dependencies, create the Docker containers, and configure the tool so you can start using it.

4. Once the installation is complete, you can access the different containers through the following links:

| Docker               | Container Link                             |Status                                 |
|:---------------------|:-------------------------------------------|---------------------------------------|
| Dashboard            | http://localhost/tablero/tablero.php       |Functional                             |
| Main Server          | http://localhost:8080/                     |Functional                             |
| LFI                  | http://localhost:8000/                     |Functional                             |
| SQL Injection        | http://localhost:8005/                     |Under development (but can be tested)  |
| Padding Oracle Attack| http://localhost:8007/login.php            |Under development (but can be tested)  |

Note that it is still in development, and not all containers are working correctly. This repository is updated frequently.

## Contribute

If you want to contribute to the development of Pentesting-Web-Lab, you are welcome to do so! You can do it in several ways:

- Reporting bugs or issues you find in the tool through the "Issues" section in the GitHub repository.
- Proposing new features or improvements.
- Helping to solve problems or developing new vulnerabilities.

## Things for the next updates
Migrate everything to the second version (when everything is migrated, development of new vulnerabilities will be advanced).
- Add more containers and vulnerabilities.
- Improve documentation to make it easier to understand and follow.
- Refactor the code to make it more readable and maintainable.
