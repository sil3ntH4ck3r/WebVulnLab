<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>CSRF</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
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
           margin: 0;
           padding: 0;
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
           max-width: 400px;
           margin: 3rem auto;
           padding: 1rem;
       }
       .form-container {
           background: #fff;
           padding: 2rem;
           border-radius: 8px;
           box-shadow: 0 4px 10px rgba(0,0,0,0.1);
       }
       .form-container h2 {
           text-align: center;
           margin-bottom: 1.5rem;
           color: var(--primary-color);
       }
       .form-container .user-box {
           margin-bottom: 1.5rem;
       }
       .form-container .user-box label {
           display: block;
           margin-bottom: 0.5rem;
           font-weight: 500;
       }
       .form-container .user-box input {
           width: 100%;
           padding: 0.8rem;
           border: 1px solid #ddd;
           border-radius: 4px;
           font-size: 1rem;
       }
       .form-container button {
           width: 100%;
           padding: 0.8rem;
           background-color: var(--primary-color);
           color: var(--light-text);
           border: none;
           border-radius: 4px;
           cursor: pointer;
           font-size: 1rem;
           transition: background-color 0.3s;
       }
       .form-container button:hover {
           background-color: #155d4e;
       }
       .message {
           text-align: center;
           margin-bottom: 1rem;
           font-size: 1rem;
       }
       .success-message {
           color: var(--success-color);
       }
       .error-message {
           color: var(--error-color);
       }
       footer {
           background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
           color: var(--light-text);
           text-align: center;
           padding: 1rem;
           font-size: 0.9rem;
           margin-top: 3rem;
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
   <nav>
       <ul>
           <li><a href="welcome.php">Bienvenido</a></li>
           <li><a href="logout.php">Cerrar sesión</a></li>
           <li><a href="change.php">Cambiar contraseña</a></li>
       </ul>
   </nav>
   <div class="container">
       <?php if (isset($message)): ?>
           <p class="message <?php echo $message_class; ?>"><?php echo $message; ?></p>
       <?php endif; ?>
       <div class="form-container">
           <h2>Cambiar Contraseña</h2>
           <form method="GET" action="change_password.php">
               <div class="user-box">
                   <label>Contraseña nueva</label>
                   <input type="password" name="new_password" required>
               </div>
               <div class="user-box">
                   <label>Repita la contraseña</label>
                   <input type="password" name="confirm_password" required>
               </div>
               <button type="submit">Cambiar la contraseña</button>
           </form>
           <p style="margin-top: 1rem; font-size: 0.9rem; text-align: center;">
               Recuerda: utiliza al menos 8 caracteres combinando letras y números para mayor seguridad.
           </p>
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
