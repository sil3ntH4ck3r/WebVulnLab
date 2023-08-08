<?php
    session_start();


    if (isset($_GET["session_id"])) {
        $sessionId = $_GET["session_id"];
        setcookie("session_id", $sessionId, time() + 3600, "/");
        header("Location: perfil.php");
        exit;
    }

    // Conectar a la base de datos
    $conexion = mysqli_connect("db", "usuario", "contraseña", "database");
    if ($conexion) {
        $conexion->set_charset("utf8");
    }

    // Verificar si la conexión fue exitosa
    if (!$conexion) {
        die('Error al conectar a la base de datos: ' . mysqli_connect_error());
    }

    //$cookieUser = null;
    if (isset($_COOKIE["session_id"])) {
        $cookieUser = $_COOKIE["session_id"];
    }

    // Obtener el nombre de usuario de la persona que tiene el session_id almacenado en la cookie
    if ($cookieUser) {
        $sql = "SELECT nombre FROM usuarios WHERE session_id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $cookieUser);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $nombreUsuario = $row['nombre'];
            //echo "El nombre de usuario con el session_id " . $cookieUser . " es: " . $nombreUsuario;
        } else {
            echo "<script>alert('No se encontró ningún usuario con el session_id proporcionada');</script>";
            exit;
        }
    } else {
        //echo "La cookie 'session_id' no está establecida.";
    }

    // Cerrar la conexión a la base de datos
    mysqli_close($conexion);

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submitComment"])) {
        // Check if the comment is not empty
        if (!empty($_POST["comment"])) {
            $comment = $_POST["comment"];
            
            // Open the comments.txt file in append mode
            $file = fopen("comments.txt", "a");
            
            if ($file) {
                // Format the comment and user information
                $commentLine = "User: $nombreUsuario\nComment: $comment\n\n";
                
                // Write the comment to the file
                fwrite($file, $commentLine);
                
                // Close the file
                fclose($file);
                
                echo "<script>alert('Comentario añadido correctamente');</script>";
            } else {
                echo "<script>alert('Error al subir el comentario.');</script>";
            }
        } else {
            echo "<script>alert('El comentario no puede estar vacío.');</script>";
        }
    }

?>
<!DOCTYPE html>
<html>
<head>
    <title>Session Puzzling</title>
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
        .comment-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        
        .comment-container h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .comment-container form {
            display: flex;
            flex-direction: column;
        }
        
        .comment-container textarea {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: vertical;
        }
        
        .comment-container button {
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
        }
        
        .comment-container button:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="container">
                <h1 class="logo">Cybertec</h1>
                <ul class="menu">
                    <?php if ($cookieUser) : ?>
                        <li><a href="http://sessionpuzzling.local/logout.php">Logout</a></li>
                        <li><a href="http://sessionpuzzling.local/perfil.php">Perfil</a></li>
                        <?php if ($nombreUsuario == "admin") : ?>
                            <li><a href="http://sessionpuzzling.local/dashboard.php">Dashboard</a></li>
                        <?php endif; ?>
                    <?php else : ?>
                        <li><a href="http://sessionpuzzling.local/index.php">Login</a></li>
                        <li><a href="http://sessionpuzzling.local/register.php">Register</a></li>
                        <li><a href="http://sessionpuzzling.local/reiniciar.php">Reiniciar Base de Datos</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>

    <h1>Perfil de usuario</h1>
    <div class="profile-container">
        <div class="profile">
            <div class="profile-info">
                <?php if ($cookieUser) : ?>
                    <h1>session_id: <?php echo $cookieUser ?></h1>
                    <h1>Username: <?php echo $nombreUsuario ?></h1>
                    <div class="comment-container">
                        <h2>Contacto con el Administrador</h2>
                        <p>¿Tienes alguna queja, sugerencia o pregunta? ¡No dudes en ponerte en contacto con el administrador! Utiliza el formulario a continuación para compartir tus comentarios. Revisamos todos los mensajes con atención y te responderemos lo antes posible.</p>
                        <form method="post" action="">
                            <textarea name="comment" placeholder="Ingresa tu comentario"></textarea>
                            <button type="submit" name="submitComment">Enviar</button>
                        </form>
                    </div>
                <?php else : ?>
                    <p>Debes iniciar sesión para ver tu perfil.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>

<footer>
    <p xmlns:cc="http://creativecommons.org/ns#" xmlns:dct="http://purl.org/dc/terms/"><a property="dct:title" rel="cc:attributionURL" href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev">WebVulnLab</a> by <a rel="cc:attributionURL dct:creator" property="cc:attributionName" href="https://github.com/sil3ntH4ck3r">sil3nth4ck3r</a> is licensed under <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1" target="_blank" rel="license noopener noreferrer" style="display:inline-block;">CC BY-NC-SA 4.0
</footer>
</html>
