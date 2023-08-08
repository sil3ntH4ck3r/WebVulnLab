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

# Verificar si hay campos de inicio de sesión en la página por su atributo 'id'
username_input = driver.find_element(By.ID, 'nombre')       # Cambia 'nombre' por el id real del campo de nombre de usuario
password_input = driver.find_element(By.ID, 'contraseña')  # Cambia 'contraseña' por el id real del campo de contraseña
login_button = driver.find_element(By.XPATH, '//button[contains(text(), "Iniciar sesión")]')  # Encuentra el botón por su texto

# Llenar campos de inicio de sesión
username_input.send_keys('admin')
password_input.send_keys('P@$$w0rd!')

# Hacer clic en el botón de inicio de sesión
login_button.click()

# Esperar 50 segundos (o el tiempo necesario para realizar alguna actividad después de iniciar sesión)
time.sleep(50)

# Hacer clic en el botón de logout
logout_button = driver.find_element(By.XPATH, '//a[contains(text(), "Logout")]')
logout_button.click()

# Esperar 2 segundos para asegurarse de que la página de logout se cargue completamente
time.sleep(2)

# Cierra el navegador cuando hayas terminado
driver.quit()

# Detener el servidor X virtual
vdisplay.stop()
