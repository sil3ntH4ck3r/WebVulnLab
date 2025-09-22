<!DOCTYPE html>
<html>
<?php
error_reporting(0);
session_start();
require_once __DIR__ . '/jwt_keys.php';

function getUserFromJwt($jwt) {
    $publicKeyPem = jwt_get_public_key();

    list($header, $payload, $signature) = explode('.', $jwt);

    $decodedHeader = base64_decode(str_replace(['-', '_', ''], ['+', '/', '='], $header));
    $decodedPayload = base64_decode(str_replace(['-', '_', ''], ['+', '/', '='], $payload));

    $headerData = json_decode($decodedHeader, true);
    $algorithm = $headerData['alg'];

    // Helper: base64url decode
    $b64url_to_bin = function($b64url) {
        $b64 = strtr($b64url, '-_', '+/');
        $pad = strlen($b64) % 4;
        if ($pad) {
            $b64 .= str_repeat('=', 4 - $pad);
        }
        return base64_decode($b64);
    };

    if ($algorithm === 'RS256') {
        $data = $header . '.' . $payload;
        $sigBin = $b64url_to_bin($signature);
        $ok = openssl_verify($data, $sigBin, openssl_pkey_get_public($publicKeyPem), OPENSSL_ALGO_SHA256);
        if ($ok !== 1) {
            return false; // Firma RS256 inválida
        }
    } elseif ($algorithm === 'HS256') {
        // Vulnerable path: treat public RSA key as HMAC secret
        $expectedSignature = hash_hmac('sha256', $header . '.' . $payload, $publicKeyPem, true);
        $expectedBase64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($expectedSignature));
        if ($signature !== $expectedBase64UrlSignature) {
            return false; // Firma HS256 inválida
        }
    } else {
        return false; // Algoritmo no soportado
    }

    $userData = json_decode($decodedPayload, true);
    // Enforce expiration if present
    if (isset($userData['exp']) && is_numeric($userData['exp']) && $userData['exp'] < time()) {
        return false;
    }
    return $userData['user'];
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
                    <?php
                        if (isset($_COOKIE["jwtToken"])) {
                            list($h, $p, $s) = explode('.', $_COOKIE["jwtToken"]);
                            $b64 = strtr($p, '-_', '+/');
                            $pad = strlen($b64) % 4; if ($pad) { $b64 .= str_repeat('=', 4 - $pad); }
                            $payloadArr = json_decode(base64_decode($b64), true);
                            $iat = isset($payloadArr['iat']) ? (int)$payloadArr['iat'] : null;
                            $exp = isset($payloadArr['exp']) ? (int)$payloadArr['exp'] : null;
                        }
                    ?>
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
