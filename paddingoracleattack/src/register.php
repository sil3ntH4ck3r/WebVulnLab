<!DOCTYPE html>
<html>
<head>
    <title>Padding Oracle Attack</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="padding.css">

    <style>
        header {
    background-color: #333;
    color: #fff;
    padding: 20px;
    text-align: center;
}

nav {
    background-color: #444;
    padding: 10px;
}

nav ul {
    list-style: none;
    margin: 0;
    padding: 0;
    text-align: center;
}

nav ul li {
    display: inline-block;
    margin-right: 20px;
}

nav ul li:last-child {
    margin-right: 0;
}

nav ul li a {
    color: #fff;
    text-decoration: none;
    padding: 10px;
    transition: background-color 0.3s;
}

nav ul li a:hover {
    background-color: #555;
}

header h1 {
    text-align: center;
  font-size: 3rem;
  margin-top: 1rem;
}

h1 {
    text-align: center;
    font-size: 3rem;
    margin-top: 1rem;
}
footer {
    position: absolute;
    bottom: 0;
    width: 100%;
    height: 50px;
    background-color: #333;
    color: #fff;
    padding: 20px;
    text-align: center;
}

body {
    margin: 0;
    padding: 0;
    font-family: sans-serif;
    background: #ffffff;
}
  
.login-box {
    width: 360px;
    height: 400px;
    background: #ffffff;
    color: #000000;
    top: 50%;
    left: 50%;
    position: absolute;
    transform: translate(-50%, -50%);
    box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    overflow: hidden;
}
  
.login-box h2 {
    margin: 0;
    padding: 20px;
    text-align: center;
    font-size: 22px;
    color: #000000;
}
  
.login-box form {
    padding: 20px;
    text-align: center;
}
  
.login-box form .user-box {
    position: relative;
    margin: 20px 0;
}
  
.login-box form .user-box input {
    width: 100%;
    padding: 10px 0;
    font-size: 16px;
    color: #000000;
    border: none;
    border-bottom: 1px solid #000000;
    outline: none;
    background: transparent;
}
  
.login-box form .user-box label {
    position: absolute;
    top: 0;
    left: 0;
    padding: 10px 0;
    font-size: 16px;
    color: #000000;
    pointer-events: none;
    transition: 0.5s;
}
  
.login-box form .user-box input:focus ~ label,
.login-box form .user-box input:valid ~ label {
    top: -20px;
    left: 0;
    color: #000000;
    font-size: 12px;
}
  
.login-box button {
    display: block;
    width: 100%;
    padding: 10px;
    border: none;
    background-color: #000000;
    color: #ffffff;
    font-size: 18px;
    cursor: pointer;
    border-radius: 5px;
    margin: 30px 0;
    transition: 0.5s;
}
  
.login-box button:hover {
    background-color: #ffffff;
    color: #000000;
    border: 1px solid #000000;
}
#mensaje-error {
  background-color: #f8d7da;
  border: 1px solid #f5c6cb;
  color: #721c24;
  padding: 1rem;
  margin-bottom: 1rem;
  border-radius: 0.25rem;
}
.oculto {
  display: none;
}
</style>

</head>
<body>

    <header>
        <nav>
            <div class="container">
                <h1 class="logo">Cybertec</h1>
                <ul class="menu">
                <li><a href="http://paddingoracleattack.local/index.php">Login</a></li>
                    <li><a href="http://paddingoracleattack.local/register.php">Register</a></li>
                    <li><a href="http://paddingoracleattack.local/reiniciar.php">Reiniciar Base de Datos</a></li>
                    
                </ul>
            </div>
        </nav>
    </header>
   
    <div class="login-box">
      <h2>Registro</h2>
      <form id="formulario-registro" action="newuser.php" method="post">
        <div class="user-box">
            <input id="nombre" type="text" name="nombre" required="">
            <label>Nombre de usuario</label>
        </div>
        <div class="user-box">
            <input id="email" type="email" name="email" required="">
            <label>Correo Electrónico</label>
        </div>
        <div class="user-box">
            <input id="contraseña" type="password" name="contraseña" required="">
            <label>Contraseña</label>
        </div>
        <button type="submit">
        Registrarse
        </button>
    </form>
    </div>

    <div id="mensaje-error" class="oculto"></div>

    <script>
        // Obtener una referencia al formulario
        const mensajeError = document.getElementById('mensaje-error');
        var formulario = document.getElementById("formulario-registro");
        mensajeError.classList.add('oculto');

        // Agregar un controlador de eventos para el envío del formulario
        formulario.addEventListener("submit", function(evento) {
        // Prevenir el comportamiento predeterminado del formulario (recargar la página)
        evento.preventDefault();

        // Obtener los valores ingresados por el usuario
        var nombre = document.getElementById("nombre").value;
        var email = document.getElementById("email").value;
        var contraseña = document.getElementById("contraseña").value;

        // Crear un objeto FormData para enviar los datos del formulario al servidor
        var datos = new FormData();
        datos.append("nombre", nombre);
        datos.append("email", email);
        datos.append("contraseña", contraseña);

        // Enviar los datos al servidor utilizando AJAX
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
            // Actualizar el contenido de la página con el mensaje recibido del servidor
            mensajeError.classList.remove('oculto');
            document.getElementById("mensaje-error").innerHTML = this.responseText;
            }
        };
        xhr.open("POST", "newuser.php");
        xhr.send(datos);
        });
  </script>

</body>

<footer>
<p xmlns:cc="http://creativecommons.org/ns#" xmlns:dct="http://purl.org/dc/terms/"><a property="dct:title" rel="cc:attributionURL" href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev">WebVulnLab</a> by <a rel="cc:attributionURL dct:creator" property="cc:attributionName" href="https://github.com/sil3ntH4ck3r">sil3nth4ck3r</a> is licensed under <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1" target="_blank" rel="license noopener noreferrer" style="display:inline-block;">CC BY-NC-SA 4.0
</footer>
</html>