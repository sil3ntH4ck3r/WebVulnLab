<?php
// Asegúrate de que no hay espacios en blanco ni nada antes de este bloque de PHP

// Inicia la sesión antes de enviar cualquier salida al navegador
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Obtén el ID del usuario de la variable de sesión
$user_id = $_SESSION['user_id'];
$servername = "db";
$username = "usuario";
$password = "contraseña";
$dbname = "database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica la conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$output = '';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET["id"];

    $sql = "SELECT * FROM Users WHERE id=$id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Salida de datos de cada fila
        while($row = $result->fetch_assoc()) {
            $output .= "<tr>";
            $output .= "<td>" . $row["id"]. "</td>";
            $output .= "<td>" . $row["name"]. "</td>";
            $output .= "<td>" . $row["email"]. "</td>";
            $output .= "</tr>";
        }
    } else {
        $output = "<tr><td colspan='3'>0 results</td></tr>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
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

        body {
            margin: 0;
            padding: 0;
            font-family: sans-serif;
            background: #ffffff;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .user-info {
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 20px;
            margin-top: 20px;
        }

        .user-info h2 {
            font-size: 1.5rem;
            margin-top: 0;
        }

        .user-info p {
            margin: 0;
            font-size: 1.2rem;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th, table td {
            border: 1px solid #ccc;
            padding: 8px;
        }

        table th {
            background-color: #f2f2f2;
            font-weight: bold;
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
    </style>
</head>
<body>
<header>
        <nav>
            <div class="container">
                <h1 class="logo">Detalles del usuario</h1>
                <ul class="menu">
                    <li><a href="http://idor.local/logout.php">Cerrar sesión</a></li>
                </ul>
            </div>
        </nav>
</header>
<div class="container">
    <div class="user-info">
        <h2>Información del Usuario</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Email</th>
            </tr>
            <?php echo $output; ?>
        </table>
    </div>
</div>
</body>
<footer>
    <p xmlns:cc="http://creativecommons.org/ns#" xmlns:dct="http://purl.org/dc/terms/"><a property="dct:title" rel="cc:attributionURL" href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev">WebVulnLab</a> by <a rel="cc:attributionURL dct:creator" property="cc:attributionName" href="https://github.com/sil3ntH4ck3r">sil3nth4ck3r</a> is licensed under <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1" target="_blank" rel="license noopener noreferrer" style="display:inline-block;">CC BY-NC-SA 4.0
</footer>
</html>