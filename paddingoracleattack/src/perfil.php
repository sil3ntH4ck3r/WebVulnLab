<!DOCTYPE html>
<html>

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
        $pad = ord($text{strlen($text)-1}); 
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
                }

        ?>
<head>
    <title>Padding Oracle Attack</title>
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
    

    .profile-container {
  max-width: 800px;
  margin: 0 auto;
}

.profile {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  align-items: center;
}

.profile img {
  width: 200px;
  height: 200px;
  border-radius: 50%;
  margin-bottom: 20px;
  object-fit: cover;
}

.profile h1 {
  font-size: 36px;
  margin-bottom: 10px;
}

.profile p {
  font-size: 18px;
  margin-bottom: 20px;
}

.profile button {
  background-color: #333;
  color: #fff;
  border: none;
  border-radius: 5px;
  padding: 10px 20px;
  font-size: 18px;
  cursor: pointer;
  transition: all 0.3s ease-in-out;
}

.profile button:hover {
  background-color: #555;
}

.fa-user-circle {
  font-size: 100px;
  margin-right: 20px;
}

.fa-envelope {
  font-size: 24px;
  margin-right: 10px;
}

.fa-phone {
  font-size: 24px;
  margin-right: 10px;
}
    </style>
    </style>

</head>
<body>

    <header>
        <nav>
            <div class="container">
                <h1 class="logo">Cybertec</h1>
                <ul class="menu">
                    <li><a href="http://paddingoracleattack.local/logout.php">Logout</a></li>
                    <li><a href="http://paddingoracleattack.local/perfil.php">Perfil</a></li>
                    <?php
                        if ($cookieUser=="admin"){
                            echo '<li><a href="http://paddingoracleattack.local/dashboard.php">Dashboard</a></li>';
                        } 
                    ?>
                </ul>
            </div>
        </nav>
    </header>

		<h1>Perfil de usuario</h1>
	<div class="profile-container">
		<div class="profile">
			<div class="profile-info">
				<h1>Usuario: <?php echo $cookieUser?></h1>
			</div>
		</div>
    </div>
</div>

</body>

<footer>
<p xmlns:cc="http://creativecommons.org/ns#" xmlns:dct="http://purl.org/dc/terms/"><a property="dct:title" rel="cc:attributionURL" href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev">WebVulnLab</a> by <a rel="cc:attributionURL dct:creator" property="cc:attributionName" href="https://github.com/sil3ntH4ck3r">sil3nth4ck3r</a> is licensed under <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1" target="_blank" rel="license noopener noreferrer" style="display:inline-block;">CC BY-NC-SA 4.0
</footer>
</html>