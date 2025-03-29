<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

// Obtener el nombre de usuario de la sesión
$username = $_SESSION['username'];
$login_successful = true;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submitComment"])) {
    if (!empty($_POST["comment"])) {
        $comment = $_POST["comment"];
        // Abrir el archivo para añadir el comentario
        $file = fopen("comments.txt", "a");
        if ($file) {
            $commentLine = "User: $username\nComment: $comment\n\n";
            fwrite($file, $commentLine);
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
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>CSRF</title>
   <link rel="stylesheet" href="all.min.css">
   <style>
       :root {
           --primary-color: #1e7a66;
           --secondary-color: #6eaa95;
           --accent-color: #f5f5f5;
           --text-color: #333;
           --light-text: #fff;
           --error-color: #d9534f;
           --success-color: #5cb85c;
           --warning-color: #f0ad4e;
       }
       * {
           margin: 0;
           padding: 0;
           box-sizing: border-box;
       }
       body {
           background-color: #f8f9fa;
           font-family: 'Roboto', 'Segoe UI', sans-serif;
           color: var(--text-color);
           line-height: 1.6;
       }
       header {
           background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
           color: var(--light-text);
           padding: 1.5rem 0;
           text-align: center;
       }
       header .logo {
           display: flex;
           align-items: center;
           justify-content: center;
           gap: 0.5rem;
       }
       header .logo i {
           font-size: 2rem;
       }
       header h1 {
           font-size: 2.5rem;
       }
       header .tagline {
           font-size: 1.2rem;
           font-style: italic;
           margin-top: 0.5rem;
       }
       nav {
           background-color: #fff;
           padding: 1rem 0;
           box-shadow: 0 2px 5px rgba(0,0,0,0.1);
           text-align: center;
       }
       nav ul {
           list-style: none;
           padding: 0;
           margin: 0;
       }
       nav ul li {
           display: inline-block;
           margin: 0 0.5rem;
       }
       nav ul li a {
           color: var(--primary-color);
           text-decoration: none;
           padding: 0.5rem 1rem;
           transition: background-color 0.3s;
       }
       nav ul li a:hover {
           background-color: var(--primary-color);
           color: var(--light-text);
       }
       .container {
           max-width: 800px;
           margin: 2rem auto;
           padding: 0 1rem;
       }
       .welcome-message {
           text-align: center;
           margin-bottom: 2rem;
       }
       .welcome-message h2 {
           font-size: 2rem;
           margin-bottom: 0.5rem;
       }
       .welcome-message p {
           font-size: 1.2rem;
       }
       .comment-container {
           background-color: var(--accent-color);
           border: 1px solid #ddd;
           border-radius: 8px;
           padding: 2rem;
           margin-bottom: 2rem;
       }
       .comment-container h2 {
           font-size: 1.8rem;
           margin-bottom: 1rem;
           color: var(--primary-color);
       }
       .comment-container p {
           font-size: 1rem;
           margin-bottom: 1.5rem;
       }
       .comment-container form {
           display: flex;
           flex-direction: column;
       }
       .comment-container textarea {
           padding: 0.8rem;
           font-size: 1rem;
           border: 1px solid #ddd;
           border-radius: 4px;
           resize: vertical;
           min-height: 100px;
           margin-bottom: 1rem;
       }
       .comment-container button {
           padding: 0.8rem;
           background-color: var(--primary-color);
           color: var(--light-text);
           border: none;
           border-radius: 4px;
           cursor: pointer;
           font-size: 1rem;
           transition: background-color 0.3s;
           width: fit-content;
           align-self: center;
       }
       .comment-container button:hover {
           background-color: #155d4e;
       }
       .additional-info {
           text-align: center;
           margin-top: 2rem;
           padding: 1rem;
           background-color: #fff;
           border-radius: 8px;
           box-shadow: 0 2px 5px rgba(0,0,0,0.1);
       }
       .additional-info h2 {
           color: var(--primary-color);
           margin-bottom: 0.5rem;
       }
       .additional-info p {
           font-size: 1rem;
       }
       footer {
           background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
           color: var(--light-text);
           text-align: center;
           padding: 1rem;
           font-size: 0.9rem;
       }
   </style>
</head>
<body>
   <header>
       <div class="logo">
           <i class="fas fa-mortar-pestle"></i>
           <h1>Farmacia Saludable</h1>
       </div>
       <p class="tagline">Tu salud, nuestra prioridad</p>
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
   <div class="container">
       <div class="welcome-message">
           <h2>Bienvenido, <?php echo htmlspecialchars($username); ?>!</h2>
           <p>Has iniciado sesión correctamente. Aquí puedes ponerte en contacto con el administrador para cualquier duda o sugerencia.</p>
       </div>
       <div class="comment-container">
           <h2>Contacto con el Administrador</h2>
           <p>¿Tienes alguna queja, sugerencia o pregunta? ¡No dudes en ponerte en contacto con el administrador! Utiliza el formulario a continuación para compartir tus comentarios. Revisamos todos los mensajes con atención y te responderemos lo antes posible.</p>
           <form method="post" action="">
               <textarea name="comment" placeholder="Ingresa tu comentario" required></textarea>
               <button type="submit" name="submitComment">Enviar comentario</button>
           </form>
       </div>
       <div class="additional-info">
           <h2>Información Adicional</h2>
           <p>Para más información sobre nuestros servicios o para consultar dudas frecuentes, visita nuestra sección de <a href="#" style="color: var(--primary-color); text-decoration: underline;">Preguntas Frecuentes</a>.</p>
       </div>
   </div>
   <footer>
       <p>
           <a href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev" target="_blank" style="color: var(--light-text); text-decoration: none;">WebVulnLab</a> by 
           <a href="https://github.com/sil3ntH4ck3r" target="_blank" style="color: var(--light-text); text-decoration: none;">sil3nth4ck3r</a> is licensed under 
           <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1" target="_blank" style="color: var(--light-text); text-decoration: none;">CC BY-NC-SA 4.0</a>
       </p>
   </footer>
</body>
</html>
