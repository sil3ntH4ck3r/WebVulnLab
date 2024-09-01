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
    }

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Padding Oracle Attack</title>
    <style>
        /* Estilos generales */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(to bottom right, #f0f4f8, #e9ecef);
            min-height: 100vh;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header y navegación */
        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background-color: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }

        .nav-links {
            display: none;
        }

        .nav-links a {
            text-decoration: none;
            color: #555;
            margin-left: 1.5rem;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #007bff;
        }

        /* Sección de inicio */
        .hero {
            display: flex;
            min-height: 100vh;
            padding-top: 80px; /* Ajuste para el header fijo */
        }

        .hero-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 2rem;
        }

        .hero-content h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            color: #1a1a1a;
        }

        .hero-content p {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 1.5rem;
        }

        .cta-button {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .btn-descubre{
            width: 200px;
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .btn-descubre:hover {
            background-color: #0056b3;
        }

        .cta-button:hover {
            background-color: #0056b3;
        }

        .mensaje {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            font-weight: bold;
            text-align: center;
        }

        /* Estilo para mensajes de éxito */
        .mensaje-exito {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        /* Estilo para mensajes de error */
        .mensaje-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .login-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #ffffff;
        }

        .login-form {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .login-form h2 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .login-form button {
            width: 100%;
            padding: 0.75rem;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-form button:hover {
            background-color: #0056b3;
        }

        /* Sección de soluciones */
        .solutions {
            background-color: #fff;
            padding: 4rem 0;
        }

        .solutions h2 {
            font-size: 2rem;
            text-align: center;
            margin-bottom: 3rem;
        }

        .solutions-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        .solution-item {
            background-color: #f8f9fa;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .solution-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .solution-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .solution-item h3 {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }

        /* Sección de innovación */
        .innovation {
            background: linear-gradient(to right, #007bff, #6610f2);
            color: #fff;
            padding: 4rem 0;
        }

        .innovation h2 {
            font-size: 2rem;
            text-align: center;
            margin-bottom: 2rem;
        }

        .innovation-content {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .innovation-text {
            flex: 1;
        }

        .innovation-text p {
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }

        .innovation-demo {
            flex: 1;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .demo-placeholder {
            width: 100%;
            height: 0;
            padding-bottom: 56.25%; /* 16:9 aspect ratio */
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            position: relative;
            overflow: hidden;
        }

        .demo-placeholder::before,
        .demo-placeholder::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(255, 255, 255, 0.8);
        }

        .demo-placeholder::before {
            width: 60px;
            height: 60px;
            border-radius: 50%;
        }

        .demo-placeholder::after {
            width: 0;
            height: 0;
            border-style: solid;
            border-width: 15px 0 15px 30px;
            border-color: transparent transparent transparent #007bff;
            transform: translate(-40%, -50%);
        }

        /* Sección Nuestra Visión */
        .vision {
            padding: 4rem 0;
        }

        .vision h2 {
            font-size: 2rem;
            text-align: center;
            margin-bottom: 2rem;
        }

        .vision-content {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }

        .vision-content p {
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }

        /* Footer */
        footer {
            background-color: #333;
            color: #fff;
            padding: 3rem 0;
        }

        .footer-content {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        .footer-section h3 {
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.5rem;
        }

        .footer-links a {
            color: #ddd;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: #fff;
        }

        .footer-bottom {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #555;
        }

        /* Media queries para responsividad */
        @media (min-width: 768px) {
            .nav-links {
                display: flex;
            }

            .solutions-grid {
                grid-template-columns: repeat(3, 1fr);
            }

            .innovation-content {
                flex-direction: row;
            }

            .footer-content {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .hero-content h1 {
                font-size: 3.5rem;
            }
        }

        @media (max-width: 767px) {
            .hero {
                flex-direction: column;
            }

            .hero-content, .login-section {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav class="container">
            <div class="logo">TechNova</div>
            <div class="nav-links">
                <a href="http://paddingoracleattack.local/index.php#solutions">Soluciones</a>
                <a href="http://paddingoracleattack.local/index.php#innovation">Innovación</a>
                <a href="http://paddingoracleattack.local/index.php#vision">Nosotros</a>
                <a href="http://paddingoracleattack.local/reiniciar.php">Reiniciar Base de Datos</a>
                <?php
                    if ($_SESSION['loggedin']==true)
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

    <main>
        <section class="hero">
            <div class="hero-content">
                <h1>Impulsa tu negocio con tecnología de vanguardia</h1>
                <p>TechNova ofrece soluciones innovadoras que transforman la forma en que las empresas operan en la era digital.</p>
                <a href="descubre.php" class="btn-descubre">Descubre cómo</a>
            </div>
            <div class="login-section">
                <div class="login-form">
                <?php if (isset($_SESSION['mensaje'])): ?>
                    <?php 
                    // Determinar la clase del mensaje según si la sesión está iniciada correctamente
                    $tipoMensaje = $_SESSION['loggedin'] ? 'mensaje-exito' : 'mensaje-error';
                    ?>
                    <!-- Mostrar el mensaje con la clase correspondiente -->
                    <p class="mensaje <?php echo $tipoMensaje; ?>"><?php echo $_SESSION['mensaje']; ?></p>
                    <?php unset($_SESSION['mensaje']); endif; ?>
                    <h2>Accede a tu cuenta</h2>
                    <form action="verificar.php" method="post">
                        <div class="form-group">
                            <label for="username">Usuario</label>
                            <input type="text" id="nombre" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <input type="password" id="contraseña" name="contraseña" required="">
                        </div>
                        <button type="submit">Iniciar sesión</button>
                    </form>
                    <p style="text-align: center; margin-top: 1rem; font-size: 0.9rem;">
                        ¿No tienes una cuenta? <a href="http://paddingoracleattack.local/register.php" style="color: #007bff; text-decoration: none;">Regístrate</a>
                    </p>
                </div>
            </div>
        </section>

        <section id="solutions" class="solutions">
            <div class="container">
                <h2>Soluciones Revolucionarias</h2>
                <div class="solutions-grid">
                    <div class="solution-item">
                        <div class="solution-icon">⚡</div>
                        <h3>Automatización Inteligente</h3>
                        <p>Optimiza tus procesos con IA avanzada</p>
                    </div>
                    <div class="solution-item">
                        <div class="solution-icon">🛡️</div>
                        <h3>Seguridad Robusta</h3>
                        <p>Protege tus datos con tecnología de punta</p>
                    </div>
                    <div class="solution-item">
                        <div class="solution-icon">💡</div>
                        <h3>Innovación Continua</h3>
                        <p>Mantente a la vanguardia del mercado</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="innovation" class="innovation">
            <div class="container">
                <h2>Impulsando la Innovación</h2>
                <div class="innovation-content">
                    <div class="innovation-text">
                        <p>En TechNova, no solo seguimos las tendencias, las creamos. Nuestro equipo de expertos está constantemente explorando nuevas fronteras tecnológicas para ofrecer soluciones que marcan la diferencia.</p>
                        <a href="innovaciones.php" class="cta-button" style="background-color: #fff; color: #007bff;">Explora nuestras innovaciones</a>
                    </div>
                    <div class="innovation-demo">
                        <div class="demo-placeholder"></div>
                    </div>
                </div>
            </div>
        </section>

        <section id="vision" class="vision">
            <div class="container">
                <h2>Nuestra Visión</h2>
                <div class="vision-content">
                    <p>En TechNova, creemos en un futuro donde la tecnología potencia el potencial humano. Nos dedicamos a crear soluciones que no solo resuelven problemas actuales, sino que también anticipan los desafíos del mañana.</p>
                    <a href="equipo.php" class="cta-button">Conoce a nuestro equipo</a>
                </div>
            </div>
        </section>
    </main>

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
