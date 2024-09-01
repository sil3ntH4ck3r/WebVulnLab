<?php 
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
        $pad = ord($text[strlen($text) - 1]); 
        if ($pad === 0) return false;
        if ($pad > strlen($text)) return false; 
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return false; 
        return substr($text, 0, -1 * $pad); 
    } 

?>
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
                    header('Location: index.php');
                }

        ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Padding Oracle Attack</title>
    <link href="main.css" rel="stylesheet">
    <style>

        main {
            padding-top: 100px;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .profile-header {
            display: flex;
            align-items: center;
            background-color: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 2rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .profile-info {
            flex-grow: 1;
        }

        .profile-info h1 {
            color: #007bff;
            margin-bottom: 0.5rem;
        }

        .profile-info p {
            color: #666;
            margin-bottom: 1rem;
        }

        .edit-profile {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 0.5rem 1rem;
            cursor: pointer;
            border-radius: 5px;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .edit-profile:hover {
            background-color: #0056b3;
        }

        .profile-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .detail-box {
            background-color: #fff;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .detail-box h3 {
            color: #007bff;
            margin-bottom: 1rem;
        }

        .detail-box p {
            color: #333;
            margin-bottom: 1rem;
        }

        .stats-list {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .stat-item {
            background-color: #e9ecef;
            padding: 0.5rem 1rem;
            border-radius: 4px;
        }

        @media (min-width: 768px) {
            .nav-links {
                display: flex;
            }
        }
        .redacted {
            background-color: black;
            color: black;
        }
    </style>
</head>
<body>
    <br><br><br>
    <header>
        <nav class="container">
            <a class="logo"  href="index.php">TechNova</a>
            <div class="nav-links">
                <a href="http://paddingoracleattack.local/index.php#solutions">Soluciones</a>
                <a href="http://paddingoracleattack.local/index.php#innovation">Innovación</a>
                <a href="http://paddingoracleattack.local/index.php#vision">Nosotros</a>
                <a href="http://paddingoracleattack.local/reiniciar.php">Reiniciar Base de Datos</a>
                <?php
                if (isset($_COOKIE['cookieAuth']))
                {   
                    echo '<a href="http://paddingoracleattack.local/logout.php">Logout</a>';
                    echo '<a href="http://paddingoracleattack.local/perfil.php">Perfil</a>';
                }
                if ($cookieUser=="admin"){
                    echo '<a href="http://paddingoracleattack.local/dashboard.php">Dashboard</a>';
                } 
            ?>
            </div>
        </nav>
    </header>

    <main class="container">
        <!-- Encabezado del perfil -->
        <div class="profile-header">
            <img src="profile-pic-placeholder.jpg" alt="Imagen de Usuario" class="profile-image">
            <div class="profile-info">
                <h1>Usuario: <?php echo $cookieUser?></h1>
                <!-- <p>Rol del Usuario</p> -->
                <button class="edit-profile">Editar Perfil</button>
            </div>
        </div>

        <!-- Sección de detalles de usuario -->
        <div class="profile-details">
            <div class="detail-box">
                <h3>Detalles Personales</h3>
                <p><strong>Email:</strong> <span class="redacted">usuario@correo.com</span></p>
                <p><strong>Teléfono:</strong> No introducido</p>
                <p><strong>Dirección:</strong> No introducida</p>
            </div>

            <div class="detail-box">
                <h3>Estadísticas</h3>
                <div class="stats-list">
                    <div class="stat-item">Sin estadísticas</div>
                </div>
            </div>

            <div class="detail-box">
                <h3>Preferencias</h3>
                <p><strong>Notificaciones:</strong> Activadas</p>
                <p><strong>Idioma:</strong> Español</p>
            </div>
        </div>
    </main>
    <br>
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>TechNova</h3>
                    <p>Transformando el futuro, hoy.</p>
                </div>
                <div class="footer-section">
                    <h3>Enlaces rápidos</h3>
                    <ul class="footer-links">
                        <li><a href="http://paddingoracleattack.local/index.php#solutions">Soluciones</a></li>
                        <li><a href="http://paddingoracleattack.local/index.php#innovation">Innovación</a></li>
                        <li><a href="http://paddingoracleattack.local/index.php#vision">Sobre nosotros</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Contáctanos</h3>
                    <p>info@webvulnlab.paddingoracleattack.local</p>
                    <p>+1 (555) 123-4567</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <span id="year"></span> <a property="dct:title" rel="cc:attributionURL" href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev">WebVulnLab</a> by <a href="https://github.com/sil3ntH4ck3r">sil3nth4ck3r</a> is licensed under <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1">CC BY-NC-SA 4.0                
                <script>
                    document.getElementById("year").textContent = new Date().getFullYear();
                </script>
            </div>
        </div>
    </footer>
</body>
</html>
