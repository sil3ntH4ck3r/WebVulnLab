<?php
session_start();

if ($_SESSION['logged'] != true) {
    header('Location: index.php', true, 302);
    exit;
}

if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: index.php', true, 302);
    exit;
}

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header('Access-Control-Allow-Credentials: true');
}

// Agrega el encabezado CORS para las solicitudes OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    exit;
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
        .content-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            margin: 0 auto;
        }
        .info {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .logout-button {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
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
    <div class="content-container">
        <div class="info">
            <table>
                <tr>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                </tr>
                <tr>
                    <td>Empresa XYZ</td>
                    <td>Calle Principal 123</td>
                    <td>555-1234</td>
                </tr>
                <tr>
                    <td>Empresa ABC</td>
                    <td>Avenida Secundaria 456</td>
                    <td>555-5678</td>
                </tr>
            </table>
        </div>
        <form method="POST">
            <input type="submit" name="logout" value="Salir" class="logout-button">
        </form>
    </div>
    <footer>
        <p xmlns:cc="http://creativecommons.org/ns#" xmlns:dct="http://purl.org/dc/terms/"><a property="dct:title" rel="cc:attributionURL" href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev">WebVulnLab</a> by <a rel="cc:attributionURL dct:creator" property="cc:attributionName" href="https://github.com/sil3ntH4ck3r">sil3nth4ck3r</a> is licensed under <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1" target="_blank" rel="license noopener noreferrer" style="display:inline-block;">CC BY-NC-SA 4.0</a></p>
    </footer>
</body>
</html>

