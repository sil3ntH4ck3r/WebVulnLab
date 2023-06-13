<?php 

// Conectar a la base de datos
$conexion = mysqli_connect("db", "usuario", "contraseña", "database");
if ($conexion) {
  $conexion->set_charset("utf8");
}

// Comprobar la conexión
if (mysqli_connect_errno()) {
  die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Verificar si la tabla ya existe
$table_name = "usuarios";
$sql = "SELECT 1 FROM $table_name LIMIT 1";
$result = mysqli_query($conexion, $sql);

if ($result === false) {
  // La tabla no existe, crearla
  $sql = "CREATE TABLE $table_name (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(30) NOT NULL,
    contraseña VARCHAR(30) NOT NULL,
    email VARCHAR(50) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  )";

  mysqli_query($conexion, $sql);
  mysqli_query($conexion, "INSERT INTO usuarios (nombre, contraseña, email) VALUES ('admin', 'P@$\$w0rd!', 'admin@admin.com')");
} 

// Cerrar la conexión
mysqli_close($conexion);

    session_start(); 
    //include "verificar.php";
        if ($_SESSION['loggedin']==true){
            $logedInUsername = $_SESSION['user'];
            //echo $_SESSION['user'];
            $user = $_SESSION['user']; // aquí obtienes el valor de $user desde la variable de sesión
            //echo "El usuario es: $user";
            createcookie($user, $password);
        }

    $password="invitado";

    function createcookie($user, $password) {
        $_SESSION['user'] = $user;
        $string = "user=$user"; 
        //echo $string;
        $passphrase = 'pntstrlb'; 
        $encryptedCookie = encryptString($string, $passphrase); 
        setcookie("cookieAuth", $encryptedCookie);

    }

    function encryptString($unencryptedText, $passphrase) { 
        $iv = mcrypt_create_iv( mcrypt_get_iv_size(MCRYPT_DES, MCRYPT_MODE_CBC), MCRYPT_RAND);
        $text = pkcs5_pad($unencryptedText,8);
        $enc = mcrypt_encrypt(MCRYPT_DES, $passphrase, $text, MCRYPT_MODE_CBC, $iv);
        return base64_encode($iv.$enc); 
    }

    function decryptString($encryptedText, $passphrase) {
        $encrypted = base64_decode($encryptedText);
        $iv_size =  mcrypt_get_iv_size(MCRYPT_DES, MCRYPT_MODE_CBC);
        $iv = substr($encrypted,0,$iv_size);
        $dec = mcrypt_decrypt(MCRYPT_DES, $passphrase, substr($encrypted,$iv_size), MCRYPT_MODE_CBC, $iv);
        $str = pkcs5_unpad($dec); 
        if ($str === false) {
            echo "Padding Invalido";
            die(); 
        }
        else {
            return $str; 
        }
    }
    function pkcs5_pad ($text, $blocksize) 
    { 
        $pad = $blocksize - (strlen($text) % $blocksize); 
        return $text . str_repeat(chr($pad), $pad); 
    } 

    function pkcs5_unpad($text) 
    { 
        $pad = ord($text{strlen($text)-1}); 
        if ($pad === 0) return false;
        if ($pad > strlen($text)) return false; 
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return false; 
        return substr($text, 0, -1 * $pad); 
    } 

?>
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
                    <?php
                        if ($_SESSION['loggedin']==false)
                        {   
                            echo '<li><a href="http://paddingoracleattack.local/index.php">Login</a></li>';
                            echo '<li><a href="http://paddingoracleattack.local/register.php">Register</a></li>';
                            echo '<li><a href="http://paddingoracleattack.local/reiniciar.php">Reiniciar Base de Datos</a></li>';
                        }
                    ?>
                    <?php
                    if ($_SESSION['loggedin']==true)
                    {   
                        echo '<li><a href="http://paddingoracleattack.local/logout.php">Logout</a></li>';
                        echo '<li><a href="http://paddingoracleattack.local/perfil.php">Perfil</a></li>';
                        echo '<li><a href="http://paddingoracleattack.local/reiniciar.php">Reiniciar Base de Datos</a></li>';
                    }
                    ?>

                </ul>
            </div>
        </nav>
    </header>

    <div class="login-box">
      <h2>Iniciar sesión</h2>
      <form id="formulario-inicio" action="verificar.php" method="post">
        <div class="user-box">
          <input type="text" id="nombre" name="nombre" required="">
          <label>Nombre de usuario</label>
        </div>
        <div class="user-box">
          <input type="password" id="contraseña" name="contraseña" required="">
          <label>Contraseña</label>
        </div>
        <button>
          Iniciar sesión
        </button>
      </form>
    </div>
    <?php if (isset($_SESSION['mensaje'])): ?>
    <p class="mensaje"><?php echo $_SESSION['mensaje']; ?></p>
    <?php unset($_SESSION['mensaje']); endif; ?>
    
        <?php

            if (isset($_COOKIE["cookieAuth"])) {
                // desencriptamos la cookie
                        $decryptedCookie = decryptString($_COOKIE["cookieAuth"], "pntstrlb");
                        $pattern = "/user=/i";
                        $cookieUser = preg_replace($pattern, "", $decryptedCookie);
                // mostramos el valor de la cookie desencriptada
                    //echo "Cookie desencriptada: " . $decryptedCookie;
                } else {
                // la cookie no está establecida, mostramos un mensaje de error
                    //echo "La cookie no está establecida.";
                }

        ?>

</body>

<footer>
<p xmlns:cc="http://creativecommons.org/ns#" xmlns:dct="http://purl.org/dc/terms/"><a property="dct:title" rel="cc:attributionURL" href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev">WebVulnLab</a> by <a rel="cc:attributionURL dct:creator" property="cc:attributionName" href="https://github.com/sil3ntH4ck3r">sil3nth4ck3r</a> is licensed under <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1" target="_blank" rel="license noopener noreferrer" style="display:inline-block;">CC BY-NC-SA 4.0
</footer>
</html>
