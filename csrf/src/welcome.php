<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['username'])) {
    // Redirigir al usuario a la página de inicio de sesión
    header('Location: index.php');
    exit;
}

// Obtener el nombre de usuario de la sesión
$username = $_SESSION['username'];
$login_successful = true;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submitComment"])) {
    // Check if the comment is not empty
    if (!empty($_POST["comment"])) {
        $comment = $_POST["comment"];
        
        // Open the comments.txt file in append mode
        $file = fopen("comments.txt", "a");
        
        if ($file) {
            // Format the comment and user information
            $commentLine = "User: $username\nComment: $comment\n\n";
            
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
    <title>CSRF</title>
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
            width: 97.9vw;
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
        <h1>Cybertec</h1>
    </header>

    <?php if ($login_successful): ?>
        <nav>
            <ul>
                <li><a href="welcome.php">Bienvenido</a></li>
                <li><a href="logout.php">Cerrar sesión</a></li>
                <li><a href="change.php">Cambiar contraseña</a></li>
            </ul>
        </nav>
    <?php endif; ?>

    <div class="content" style="text-align: center;">
        <h2 style="font-size: 2rem;">Bienvenido, <?php echo $username; ?>!</h2>
        <p style="font-size: 1.5rem;">Has iniciado sesión correctamente.</p>
        <div class="comment-container">
                        <h2>Contacto con el Administrador</h2>
                        <p>¿Tienes alguna queja, sugerencia o pregunta? ¡No dudes en ponerte en contacto con el administrador! Utiliza el formulario a continuación para compartir tus comentarios. Revisamos todos los mensajes con atención y te responderemos lo antes posible.</p>
                        <form method="post" action="">
                            <textarea name="comment" placeholder="Ingresa tu comentario"></textarea>
                            <button type="submit" name="submitComment">Enviar</button>
                        </form>
        </div>
    </div>

    <footer>
    <p xmlns:cc="http://creativecommons.org/ns#" xmlns:dct="http://purl.org/dc/terms/"><a property="dct:title" rel="cc:attributionURL" href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev">WebVulnLab</a> by <a rel="cc:attributionURL dct:creator" property="cc:attributionName" href="https://github.com/sil3ntH4ck3r">sil3nth4ck3r</a> is licensed under <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1" target="_blank" rel="license noopener noreferrer" style="display:inline-block;">CC BY-NC-SA 4.0
    </footer>
</body>
</html>