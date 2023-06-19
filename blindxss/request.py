# -*- coding: utf-8 -*-
from selenium import webdriver
from selenium.webdriver.firefox.options import Options
from xvfbwrapper import Xvfb

# Iniciar el servidor X virtual
vdisplay = Xvfb()
vdisplay.start()

# Configurar las opciones de Firefox para que se ejecute en modo sin cabeza
options = Options()
options.add_argument("--headless")

# Crea una nueva instancia del navegador
driver = webdriver.Firefox(executable_path='/usr/local/bin/geckodriver', options=options)

# Navega a la página web
driver.get('http://localhost')

# Cierra el navegador cuando hayas terminado
driver.quit()

# Detener el servidor X virtual
vdisplay.stop()
