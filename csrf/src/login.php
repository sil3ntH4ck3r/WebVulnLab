<?php
session_start();
if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    // Leer el archivo que contiene los nombres de usuario y contraseñas
    $lines = file('users.txt');
    $login_successful = false;
    foreach ($lines as $line) {
        list($stored_username, $stored_password) = explode(',', $line);
        if ($username == $stored_username && $password == trim($stored_password)) {
            // Inicio de sesión exitoso
            $_SESSION['username'] = $username;
            $login_successful = true;
            break;
        }
    }
    if ($login_successful) {
        $message = 'Inicio de sesión correcto';
        $message_class = 'success-message';
    } else {
        $message = 'Contraseña o usuario inválido';
        $message_class = 'error-message';
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

        body {
            margin: 0;
            padding: 0;
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
            margin: 0;
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
            max-width: 400px;
            margin: 3rem auto;
            padding: 1rem;
        }

        .login-box {
            background: #fff;
            padding: 2rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }

        .login-box h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: var(--primary-color);
        }

        .login-box .user-box {
            margin-bottom: 1.5rem;
        }

        .login-box .user-box label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .login-box .user-box input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .login-box button {
            width: 100%;
            padding: 0.8rem;
            background-color: var(--primary-color);
            color: var(--light-text);
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s;
            margin-bottom: 1rem;
        }

        .login-box button:hover {
            background-color: #155d4e;
        }

        .message {
            text-align: center;
            margin-bottom: 1.5rem;
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
            position: fixed;
            bottom: 0;
            width: 100%;
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

    <?php if (isset($login_successful) && $login_successful): ?>
        <nav>
            <ul>
                <li><a href="welcome.php">Bienvenido</a></li>
                <li><a href="logout.php">Cerrar sesión</a></li>
                <li><a href="change.php">Cambiar contraseña</a></li>
            </ul>
        </nav>
    <?php endif; ?>

    <div class="container">
        <?php if (isset($message)): ?>
            <p class="message <?php echo $message_class; ?>"><?php echo $message; ?></p>
        <?php endif; ?>

        <div class="login-box">
            <h2>Iniciar sesión</h2>
            <form method="POST" action="login.php">
                <div class="user-box">
                    <label>Usuario</label>
                    <input id="nombre" name="username" type="text" required>
                </div>
                <div class="user-box">
                    <label>Contraseña</label>
                    <input id="contraseña" name="password" type="password" required>
                </div>
                <button type="submit">Iniciar sesión</button>
                <button type="button" onclick="accederComoInvitado()">Acceder como invitado</button>
            </form>
        </div>
    </div>

    <footer>
        <p>
            <a href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev" target="_blank" style="color: var(--light-text); text-decoration: none;">WebVulnLab</a> by 
            <a href="https://github.com/sil3ntH4ck3r" target="_blank" style="color: var(--light-text); text-decoration: none;">sil3nth4ck3r</a> is licensed under 
            <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1" target="_blank" style="color: var(--light-text); text-decoration: none;">CC BY-NC-SA 4.0</a>
        </p>
    </footer>

    <script>
        function accederComoInvitado() {
            document.getElementsByName('username')[0].value = 'invitado';
            document.getElementsByName('password')[0].value = 'invitado';
        }
    </script>
</body>
</html>
