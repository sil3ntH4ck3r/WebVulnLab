<?php
session_start();

function generateCaptcha() {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $captcha = '';
    for ($i = 0; $i < 6; $i++) {
        $captcha .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $captcha;
}

if (!isset($_SESSION['correctCaptcha'])) {
    $_SESSION['correctCaptcha'] = generateCaptcha();
}

if (isset($_POST['login'])) {
    $enteredCaptcha = @$_POST['captcha'];
    
    if ($enteredCaptcha === $_SESSION['correctCaptcha']) {
        $_SESSION['logged'] = true;
        header('Location: confidential.php', true, 302);
        exit();
    } else {
        echo "<script>alert('Please enter the correct captcha code.');</script>";
        $_SESSION['correctCaptcha'] = generateCaptcha();
    }
}

if (@$_SESSION['logged'] === true) {
    header('Location: confidential.php', true, 302);
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CORS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 100vh;
        }
        .header {
            background-color: #333;
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            font-size: 3em;
            margin: 0;
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
        .login-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            margin: 0 auto;
        }
        .login-container h2 {
            margin-top: 0;
        }
        .captcha {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .captcha-input {
            width: 100px;
            padding: 5px;
            text-align: center;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .login-button {
            margin-top: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        footer {
            background-color: #333;
            color: #fff;
            padding: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Cybertec</h1>
    </div>

    <div class="login-container">
        <h2>Login</h2>
        <form method="POST">
            <div class="captcha">Ingrese el siguiente Captcha: <?php echo $_SESSION['correctCaptcha']; ?></div>
            <input type="text" name="captcha" class="captcha-input" required>
            <br><br>
            <input type="submit" name="login" value="Login" class="login-button">
        </form>
    </div>

    <footer>
        <p xmlns:cc="http://creativecommons.org/ns#" xmlns:dct="http://purl.org/dc/terms/"><a property="dct:title" rel="cc:attributionURL" href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev">WebVulnLab</a> by <a rel="cc:attributionURL dct:creator" property="cc:attributionName" href="https://github.com/sil3ntH4ck3r">sil3nth4ck3r</a> is licensed under <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1" target="_blank" rel="license noopener noreferrer" style="display:inline-block;">CC BY-NC-SA 4.0</a></p>
    </footer>
</body>
</html>
