import re
import time
import requests
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.firefox.options import Options
from xvfbwrapper import Xvfb

# Iniciar el servidor X virtual
vdisplay = Xvfb()
vdisplay.start()

# Configurar las opciones de Firefox para que se ejecute en modo sin cabeza
options = Options()
options.add_argument("--headless")

# Crea una nueva instancia del navegador
driver = webdriver.Firefox(options=options)

# Navegar a la página de login
driver.get("http://csrf.local")

# Esperar 2 segundos para asegurarse de que la página de login se cargue completamente
time.sleep(2)

# Verificar si hay campos de inicio de sesión en la página por su atributo 'id'
username_input = driver.find_element(By.NAME, 'username')     # Cambia 'username' por el nombre real del campo de nombre de usuario
password_input = driver.find_element(By.NAME, 'password')     # Cambia 'password' por el nombre real del campo de contraseña
login_button = driver.find_element(By.XPATH, '//button[contains(text(), "Iniciar sesión")]')  # Encuentra el botón por su texto

# Llenar campos de inicio de sesión
username_input.send_keys('admin')
password_input.send_keys('P@$$w0rd!')

# Hacer clic en el botón de inicio de sesión
login_button.click()

# Esperar 2 segundos para asegurarse de que la página de inicio se cargue completamente
time.sleep(2)

# Leer el contenido del archivo comments.txt
with open('/var/www/html/comments.txt', 'r') as file:
    content = file.read()

# Buscar enlaces que comiencen con "http://"
links = re.findall(r'http://\S+', content)

# Seleccionar el último enlace encontrado
last_link = links[-1]

# Imprimir el valor de last_link por consola
print("Último enlace encontrado:", last_link)

# Navegar al último enlace encontrado
driver.get(last_link)

# Esperar 2 segundos para asegurarse de que la página se cargue completamente
time.sleep(2)

# Hacer clic en el enlace "Cerrar sesión" utilizando JavaScript
logout_link = driver.find_element(By.XPATH, '//a[contains(text(), "Cerrar sesión")]')
driver.execute_script("arguments[0].click();", logout_link)

# Esperar 2 segundos para asegurarse de que la página de logout se cargue completamente
time.sleep(2)

# Cierra el navegador cuando hayas terminado
driver.quit()

# Detener el servidor X virtual
vdisplay.stop()
