<?php
$USER = "admin";
$PASSWORD = "e2b8200d5e85ca5674a6d0254acd15ce";

if(isset($_POST['username']) && isset($_POST['password'])){
    if($_POST['username'] == $USER){
        if(strcmp($_POST['password'],$PASSWORD) == 0){
            $_SESSION['mensaje'] = "Has iniciado sesión correctamente";
            $_SESSION['loggedin']==true;
        } else{
            $_SESSION['mensaje'] = "Contraseña o usuario incorrectos";
            $_SESSION['loggedin']==false;
        }
    }else{
        $_SESSION['mensaje'] = "Contraseña o usuario incorrectos";
        $_SESSION['loggedin']==false;
    }
}
?>

<!------------------------------------------------------------------------------------------------------------------------------------------------------------->

<!DOCTYPE html>
<html>
<head>
    <title>Panel Administrativo</title>
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
.mensaje {
    text-align: center;
    font-size: 24px;
    color: #4CAF50;
    margin-bottom: 20px;
}
    </style>

</head>
<body>

    <header>
        <nav>
            <div class="container">
                <h1 class="logo">Cybertec</h1>
                <ul class="menu">

                </ul>
            </div>
        </nav>
    </header>

    <div class="login-box">
      <h2>Iniciar sesión</h2>
      <form id="formulario-inicio" method="post">
        <div class="user-box">
          <input type="text" id="username" name="username" required="">
          <label for="username">Nombre de usuario</label>
        </div>
        <div class="user-box">
          <input type="password" id="password" name="password" required="">
          <label for="password">Contraseña</label>
        </div>
        <button type="submit">
            Iniciar sesión
        </button>
      </form>
    </div>
    <?php if (isset($_SESSION['mensaje'])): ?>
    <p class="mensaje"><?php echo $_SESSION['mensaje']; ?></p>
    <?php unset($_SESSION['mensaje']); endif; ?>

</body>

<footer>
    <p>Derechos de autor © 2023. Todos los derechos reservados.</p>
</footer>
</html>