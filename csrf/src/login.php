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
            margin-bottom: 20px;
        }

        .success-message {
            color: #4CAF50;
        }

        .error-message {
            color: #FF0000;
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
        <?php if (isset($message)): ?>
            <p class="mensaje <?php echo $message_class; ?>"><?php echo $message; ?></p>
        <?php endif; ?>
    <div class="login-box">
        <h2>Iniciar sesión</h2>
        <form method="POST" action="login.php">
            <div class="user-box">
                <input type="text" name="username" required>
                <label>Usuario</label>
            </div>
            <div class="user-box">
                <input type="password" name="password" required>
                <label>Contraseña</label>
            </div>
            <button type="submit">Iniciar sesión</button>
            <button type="button" onclick="accederComoInvitado()">Acceder como invitado</button>
        </form>
    </div>

    <script>
        function accederComoInvitado() {
            document.getElementsByName('username')[0].value = 'invitado';
            document.getElementsByName('password')[0].value = 'invitado';
        }
    </script>

    <footer>
    <p xmlns:cc="http://creativecommons.org/ns#" xmlns:dct="http://purl.org/dc/terms/"><a property="dct:title" rel="cc:attributionURL" href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev">WebVulnLab</a> by <a rel="cc:attributionURL dct:creator" property="cc:attributionName" href="https://github.com/sil3ntH4ck3r">sil3nth4ck3r</a> is licensed under <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1" target="_blank" rel="license noopener noreferrer" style="display:inline-block;">CC BY-NC-SA 4.0
    </footer>
</body>
</html>