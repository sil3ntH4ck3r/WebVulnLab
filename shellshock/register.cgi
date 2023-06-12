#!/usr/local/bin/bash

# Leer los datos enviados desde el formulario
read -r email

email_param=$email

# Decodifica la representación URL del correo electrónico
email_decoded=$(echo -e "$(sed 's/%\([0-9A-Fa-f][0-9A-Fa-f]\)/\\x\1/g' <<< "$email_param")")

# Eliminar "email=" del correo electrónico
email_clean=$(echo "$email_decoded" | sed 's/email=//')

# Imprimir una respuesta de confirmación al usuario
cat << EOF
Content-Type: text/html; charset=utf-8

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Confirmación de Registro</title>
  <style>
    html, body {
      font-family: 'Roboto', sans-serif;
      font-size: 16px;
      line-height: 1.6;
      color: #444;
      margin: 0;
      padding: 0;
      background-color: #f7f7f7;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .confirmation-container {
      background-color: #ffffff;
      padding: 40px;
      border-radius: 5px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    h1 {
      font-size: 2.5em;
      font-weight: 700;
 margin-bottom: 20px;
    }

    p {
      font-size: 1.2em;
    }
a {
      display: inline-block;
      padding: 10px 20px;
      font-size: 1em;
      background-color: #333;
      color: #fff;
      text-decoration: none;
      border-radius: 3px;
      transition: background-color 0.3s;
      font-weight: 700;
      margin-top: 20px;
    }

    a:hover {
      background-color: #555;
    }
  </style>
</head>
<body>
  <div class="confirmation-container">
    <h1>Confirmación de Registro</h1>
    <p>¡Gracias por registrarte!</p>
    <p>Tu dirección de correo electrónico ($email_clean) ha sido registrada con éxito.</p>
    <a href="http://shellshock.local/website/">Volver a la página principal</a>
 </div>
</body>
</html>
EOF