<!DOCTYPE html>
<html>
<?php
error_reporting(0);
session_start();

function getUserFromJwt($jwt) {
    $key = "your_secret_key"; // Change this to your own secret key

    list($header, $payload, $signature) = explode('.', $jwt);

    $decodedHeader = base64_decode(str_replace(['-', '_', ''], ['+', '/', '='], $header));
    $decodedPayload = base64_decode(str_replace(['-', '_', ''], ['+', '/', '='], $payload));

    $headerData = json_decode($decodedHeader, true);
    $algorithm = $headerData['alg'];

    if ($algorithm === 'NONE') {
        $userData = json_decode($decodedPayload, true);
        return $userData['user'];
    } else {
        $expectedSignature = hash_hmac('sha256', $header . '.' . $payload, $key, true);
        $expectedBase64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($expectedSignature));

        if ($signature !== $expectedBase64UrlSignature) {
            return false; // Invalid JWT signature
        }

        $userData = json_decode($decodedPayload, true);
        return $userData['user'];
    }
}

$cookieUser = null;
if (isset($_COOKIE["jwtToken"])) {
    $jwtToken = $_COOKIE["jwtToken"];
    $cookieUser = getUserFromJwt($jwtToken);
}

?>

<head>
    <title>Json Web Token</title>
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
</head>
<body>
    <header>
        <nav>
            <div class="container">
                <h1 class="logo">Cybertec</h1>
                <ul class="menu">
                    <?php if ($cookieUser) : ?>
                        <li><a href="http://jwt.local/logout.php">Logout</a></li>
                        <li><a href="http://jwt.local/perfil.php">Perfil</a></li>
                        <?php if ($cookieUser == "admin") : ?>
                            <li><a href="http://jwt.local/dashboard.php">Dashboard</a></li>
                        <?php endif; ?>
                    <?php else : ?>
                        <li><a href="http://jwt.local/index.php">Login</a></li>
                        <li><a href="http://jwt.local/register.php">Register</a></li>
                        <li><a href="http://jwt.local/reiniciar.php">Reiniciar Base de Datos</a></li>
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
                    <h1>Usuario: <?php echo $cookieUser ?></h1>
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
