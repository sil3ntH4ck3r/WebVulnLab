<!DOCTYPE html>
<html>
<head>
    <title>Blind XXE</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
    height: 450px;
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
.mensaje {
    text-align: center;
    font-size: 24px;
    color: #4CAF50;
    margin-bottom: 20px;
}
    </style>

</head>
<body>
<!-- HEADER -->
    <header>
        <nav>
            <div class="container">
                <h1 class="logo">Cybertec</h1>
                <ul class="menu">

                </ul>
            </div>
        </nav>
    </header>
<!-- Panel de inicio de sesión -->
<div class="login-box">
      <h2>Login</h2>
      <form id="formulario-inicio" method="post">
        <div class="user-box">
            <input id="email" name="email" type="email" required>
            <label for="email">Correo Electornico</label>
        </div>
        <div class="user-box">
			<input id="password" name="password" type="password" required>
            <label for="password">Contraseña</label>
        </div>
        <button id="registerNew" type="button" onclick="XMLFunction()">
            Iniciar sesión
        </button>
      </form>
    </div>
<!-- Mostrar mensaje -->
    <div class="mensaje" id="mensaje"></div>
<!-- Funcion XML -->
    <script>
		function XMLFunction(){
            var email = document.getElementById('email').value;
            var password = document.getElementById('password').value;

            // Verificar si los campos están vacíos
            if (email === '' || password === '') {
                alert('Por favor, completa todos los campos');
                return;
            }
			var xml = '' +
				'\<\?xml version="1.0" encoding="UTF-8"\?\>' +
				'<root>' +
				'<email>' + document.getElementById('email').value + '</email>' +
				'<password>' + document.getElementById('password').value + '</password>' +
			'</root>';
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function () {
				if(xmlhttp.readyState == 4){
					console.log(xmlhttp.readyState);
					console.log(xmlhttp.responseText);
					document.getElementById('mensaje').innerHTML = xmlhttp.responseText;
				}
			}
			xmlhttp.open('POST', 'process.php', true);
			xmlhttp.send(xml);
		}
	</script>

</body>

<footer>
<p xmlns:cc="http://creativecommons.org/ns#" xmlns:dct="http://purl.org/dc/terms/"><a property="dct:title" rel="cc:attributionURL" href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev">WebVulnLab</a> by <a rel="cc:attributionURL dct:creator" property="cc:attributionName" href="https://github.com/sil3ntH4ck3r">sil3nth4ck3r</a> is licensed under <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1" target="_blank" rel="license noopener noreferrer" style="display:inline-block;">CC BY-NC-SA 4.0
</footer>
</html>