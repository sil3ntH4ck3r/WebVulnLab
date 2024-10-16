# WebVulnLab (2.0v)

[Spanish Version](README.md)

**IMPORTANT NOTE:** Please note that this project is currently only available in Spanish, but efforts are being made to translate it into other languages.

Would you like to learn how to detect and exploit web vulnerabilities safely and enjoyably? Do you want to test your ethical hacking skills with real and varied challenges? Then don't miss the second version of the web vulnerability container tool, created by a passionate cybersecurity enthusiast.

In this new version, you will find a more attractive and user-friendly interface, as well as new features that will allow you to deploy and configure the containers to your liking. Although it is still in development, you can already download it and try out the first features. You will be surprised!


## Content
- [Requirements](#requirements)
- [Customization of the installation (recommended)](#customizinginstallation)
- [Installation](#installation)
- [Steps to Update the Project](#update)
- [Errores comunes](#errors)
- [Contribute](#contribute)
- [Things for the upcoming updates](#thingsfortheupcomingupdates)

## Requirements <a name="requirements"></a>

- docker (command to install docker on Kali Linux: `sudo apt-get install docker.io`)
- docker-compose (command to install docker on Kali Linux: `sudo apt-get install docker-compose`)
- php
- Git: Make sure you have Git installed on your system. You can check if Git is installed by running the following command in the terminal:
```shell
git --version
```
If Git is not installed, you can follow the installation instructions provided [here](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git).

## Customizing the Installation <a name="customizinginstallation"></a>
In the `install.sh` file, you will find three arrays containing information about the containers to be installed: `containers`, `database`, and `otros`. Each element of these arrays follows the following format:

- containers
```
container_name;$DIRECTORY_PATH;PUBLISHED_PORT:CONTAINER_PORT;container_image
```
- database
```
database_container_name;$DIRECTORY_PATH;PUBLISHED_PORT:CONTAINER_PORT;container_image
```
- otros
```
Command Description;command_to_execute
```
You can comment out the lines of containers that you do not wish to install. For example:
```
database=(
#   "sqli_db_v2;$PWD/sqli;8005:80;sqli_v2"
#   "blindsqli_db_v2;$PWD/blindsqli;8014:80;blindsqli_v2"
#   ...
)
```
This allows you to install only the containers that you need to avoid performance issues on your system. Once you have commented out the containers you do not want to install, you can proceed to the [next step](#installation).
After you have resolved and used the installed containers, you can uninstall them if desired. You can do so using commands such as docker-compose down or docker rm, as appropriate.
If you wish to install more containers, simply comment out the lines of unwanted containers in the install.sh file and run the script again.

This process allows you to selectively install and manage containers, which can be helpful for efficiently managing your system's resources.

## Installation <a name="installation"></a>

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

**Note:** The `install.sh` file is not compatible with Arch Linux (a new installation script is being worked on).

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
| Dashboard            | http://tablero.local/                      |Functional                            |
| Main Server          | http://menu.local/                         |Functional                            |
| LFI                  | http://lfi.local/                          |Functional                            |
| Padding Oracle Attack| http://paddingoracleattack.local/          |Functional                            |
| Type Juggling        | http://typejuggling.local/                 |Functional                            |
| Remote File Inclusion|http://rfi.local/                           |Functional                            |
| XSS                  | http://xss.local/                          |Functional                            |
| XXE                  | http://xxe.local/                          |Functional                            |
| XPath Injection      | http://xpathinjection.local/               |Functional                            |
| LaTeX Injection      | http://latexinjection.local/               |Functional                            |
| ShellShock           | http://shellshock.local/                   |Functional                            |
| SQL Injection (Error)| http://sqli.local/                         |Functional                            |
| Blind SQL Injection (Time)| http://blindsqli.local/               |Functional                            |
| Domain Zone Transfer | http://domainzonetransfer.local/           |Functional                            |
| CSRF                 | http://csrf.local/                         |Functional                            |
| SSRF                 | http://ssrf.local/                         |Functional                            |
| Blind XXE            | http://blindxxe.local/                     |Functional                            |
| Blind XSS            | http://blindxss.local/                     |Functional                            |
| HTML Injection       | http://htmlinjection.local/                |Functional                            |
| PHP Insecure Deseralization | http://insecuredeseralizationphp.local/    |Functional                     |
| Insecure Direct Object Reference (iDOR) | http://idor.local/      |Functional                            |
| Server-Side Template Injection (SSTI) | http://ssti.local/        |Functional                            |
| Client-Side Template Injection (CSTI)| http://csti.local/         |Functional                            |
| NoSQL Injections     | http://nosqlinjection.local/               |Functional                            |
| LDAP Injections      | http://ldapinjection.local/                |Functional                            |
| API's Abuse and Mass-Asignament Attack | http://apiabuse.local/   |Functional                            |
| File Upload Abuse    | http://fileuploadabuse.local/              |Functional                            |
| Prototype Pollution  | http://prototypepollution.local/           |Functional                            |
| Open Redirect| http://openredirect.local/ | Functional |
| WebDAV| http://webdav.local/ | Functional |
| SquidProxies| http://squidproxy.local/| Functional |
| Cross-Origin Resource Sharing (CORS) Vulnerability | http://localhost:8029 | Semi-Functional |
|                                                | Note: The CORS vulnerability should be tested using `localhost:8029`, as we haven't been able to make it work using `cors.local`. |
| SQL Truncation| http://sqltruncation.local/ | Functional |
|Session Puzzling / Session Fixation / Session Variable Overloading| http://sessionpuzzling.local/ | Functional |
| Json Web Token| http://jwt.local/ | Functional |
| Race Condition| http://racecondition.local/ | Functional |
| CSS Injection| http://cssi.local/ | Functional |
| Python Deserelization (DES-Yaml)| http://yamldeseralization.local/ | Functional |
| Python Deserelization (DES-Pickle)| http://pickledeseralization.local/ | Functional |
| GraphQL Introspection, Mutations| http://graphql.local/ | Functional |
| OAuth / Werkzeug Debugger Console Abuse| [http://oauth_gallery.local](http://oauth_gallery.local/) 
|                                        |[http://oauth_printing.local/](http://oauth_printing.local/) | Functional |
| SNMP Abuse + IPv6| http://snmp.local/ | Functional |
| AWS| http://aws.local/ | In development, but can be tested |
| Active Directory| - | Working on it |

Note that it is still in development, and not all containers are working correctly. This repository is updated frequently.

## Project Update <a name="update"></a>

You can use the following script to check and apply updates to the project from the console.

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


## Common Errors <a name="errors"></a>

### Error resolving vulnerability URL

If you are unable to resolve the vulnerability URL, there may be several factors contributing to this problem. Here are some possible solutions:

1. **Containers not started correctly:**

   - Check if the containers are active by running the `docker ps` command in the command line. If the containers are running, the issue might be related to the Apache configuration.
   - If the containers do not appear in the list or show an "exited" state when running the `docker ps -a` command, there may be a problem with container startup. Make sure to follow the appropriate configuration instructions and startup commands for the containers.

2. **Inactive Apache configuration:**

   - Verify that the Apache configuration is active and correctly set up. Review the relevant configuration files, such as the main Apache configuration file (`/etc/apache2/sites-available/WebVulnLab.conf`), to ensure that all necessary settings are present and correct.
   - Make sure to restart Apache after making configuration changes or after system startup. You can do this by executing the appropriate command based on your operating system (e.g., `sudo service apache2 restart` in Linux).

3. **Request assistance through the "Issues" section of the project:**

   - If you have tried the above solutions and still cannot resolve the issue, you can seek help through the "Issues" section in the project's GitHub repository. Provide a detailed description of the problem you are facing, including any relevant error messages, and provide information about your runtime environment (operating system, software versions, etc.).

### Service Unavailable when entering the domain

If the domain displays the "Service Unavailable" message when accessed, it is likely that the corresponding container is not running. Here are some possible solutions:

1. **Check container status:**

   - Use the `docker ps` command in the command line to verify if the required container is running.
   - If the container does not appear in the list or shows an "exited" state when running the `docker ps -a` command, there may have been an issue during container startup. Make sure to follow the appropriate instructions to start the container correctly.

If the container is not running, you can follow these additional steps:

2. **Restart the container through the Docker REST API:**

   - Access the `tablero.local` domain in your browser. This domain is enabled to interact with the Docker REST API and allows you to control the containers.
   - Use the functionalities provided by the "tablero" to restart the specific container that is not functioning.
   - Verify if restarting the container through `tablero.local` resolves the issue and allows access to the domain.

   **NOTE:** If you are unable to access the `tablero.local` domain, I recommend following the steps mentioned in the previous section of "Common Errors" in the README. You can click [here](#errors) to navigate to that section. You can find information on how to address issues such as inactive Apache configuration, among others.

If you still encounter the "Service Unavailable" error after restarting the container, consider these additional possible solutions:

3. **Review container logs:**

   - Use the `docker logs <container_name>` command to view the container logs and search for possible errors or issues during execution.
   - Examine the logs for error messages or warnings that may provide information about the reason behind the service being unavailable.

4. **Request assistance through the "Issues" section of the project:**

   - If you have tried the above solutions and still cannot resolve the issue, you can seek help through the "Issues" section in the project's GitHub repository. Provide a detailed description of the problem you are facing, including any relevant error messages, and provide information about your runtime environment (operating system, software versions, etc.).

### Proxy Error

If when entering the domain it shows the message "Proxy Error", it is likely that the corresponding container does not have the Apache service turned on. Here are some possible solutions:

1. **Turn on Apache service**

    - Inside the container, execute the following command to verify if the Apache service is running:

        ```
        service apache2 status
        ```
      This command will provide information about the status of the Apache service within the container.

    - If the service is stopped, you can start it by executing:

        ```
        service apache2 start
        ```
      This command will start the Apache service within the container.

    **NOTE:** To execute commands within a container and obtain an interactive console, you can use the command `docker exec -it <container_name> /bin/bash`. When you have finished executing commands in the container, you can exit the interactive console by typing `exit`.

## Contribute <a name="contribute"></a>

If you want to contribute to the development of Pentesting-Web-Lab, you are welcome to do so! You can do it in several ways:

- Reporting bugs or issues you find in the tool through the "Issues" section in the GitHub repository.
- Proposing new features or improvements.
- Helping to solve problems or developing new vulnerabilities.

## Things for the upcoming updates: <a name="thingsfortheupcomingupdates"></a>

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
