<?php
$servername = "db";
$username = "usuario";
$password = "contraseña";
$dbname = "database";

// Crear conexión
$conexion = mysqli_connect($servername, $username, $password, $dbname);

// Verificar la conexión
if (!$conexion) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

$table_name = "Users";
$sql = "SELECT 1 FROM $table_name LIMIT 1";
$result = mysqli_query($conexion, $sql);

if ($result === false) {
    // La tabla no existe, crearla
    $create_table_sql = "CREATE TABLE Users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL
    )";

    if (mysqli_query($conexion, $create_table_sql)) {
        echo "Tabla Users creada exitosamente";
    } else {
        echo "Error al crear la tabla Users: " . mysqli_error($conexion);
    }
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Verificar si el correo ya existe en la base de datos
    $email_check_sql = "SELECT * FROM Users WHERE email='$email'";
    $email_check_result = $conn->query($email_check_sql);

    if ($email_check_result->num_rows > 0) {
        $error = "El correo ingresado ya existe";
    } else {
        // Verificar si el nombre ya existe en la base de datos
        $name_check_sql = "SELECT * FROM Users WHERE name='$name'";
        $name_check_result = $conn->query($name_check_sql);

        if ($name_check_result->num_rows > 0) {
            $error = "El usuario ingresado ya existe";
        } else {
            $sql = "INSERT INTO Users (name, email, password) VALUES ('$name', '$email', '$password')";

            if ($conn->query($sql) === TRUE) {
                $error = "Se ha registrado correctamente";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
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

        .register-box {
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

        .register-box h2 {
            margin: 0;
            padding: 20px;
            text-align: center;
            font-size: 22px;
            color: #000000;
        }

        .register-box form {
            padding: 20px;
            text-align: center;
        }

        .register-box form .user-box {
            position: relative;
            margin: 20px 0;
        }

        .register-box form .user-box input {
            width: 100%;
            padding: 10px 0;
            font-size: 16px;
            color: #000000;
            border: none;
            border-bottom: 1px solid #000000;
            outline: none;
            background: transparent;
        }

        .register-box form .user-box label {
            position: absolute;
            top: 0;
            left: 0;
            padding: 10px 0;
            font-size: 16px;
            color: #000000;
            pointer-events: none;
            transition: 0.5s;
        }

        .register-box form .user-box input:focus ~ label,
        .register-box form .user-box input:valid ~ label {
            top: -20px;
            left: 0;
            color: #000000;
            font-size: 12px;
        }

        .register-box form input[type="submit"] {
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

        .register-box form input[type="submit"]:hover {
            background-color: #ffffff;
            color: #000000;
            border: 1px solid #000000;
        }
        .register-box p {
            text-align: center;
            font-size: 14px;
            color: #555555;
            margin-top: 20px;
        }

        .register-box p a {
            color: #000000;
            text-decoration: none;
            transition: color 0.3s;
        }

        .register-box p a:hover {
            color: #555555;
        }
        #mensaje-error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0.25rem;
        }

        .oculto {
            display: none;
        }
    </style>
</head>
<body>
<header>
        <nav>
            <div class="container">
                <h1 class="logo">Register</h1>
                <ul class="menu">
                    <li><a href="http://idor.local/eliminar.php">Reiniciar Base de Datos</a></li>
                </ul>
            </div>
        </nav>
</header>
<?php
    if (isset($error)) {
        echo "<div id='mensaje-error'>$error</div>";
    }
?>
<div class="register-box">
    <h2>Register</h2>
    <form action="register.php" method="post">
        <div class="user-box">
            <input type="text" id="name" name="name" required="">
            <label for="name">Usuario:</label>
        </div>
        <div class="user-box">
            <input type="email" id="email" name="email" required="">
            <label for="email">Email:</label>
        </div>
        <div class="user-box">
            <input type="password" id="password" name="password" required="">
            <label for="password">Contraseña:</label>
        </div>
        <input type="submit" value="Registrarse">
        <p>Si ya tiene cuenta, <a href="index.php">Inicie sesión aquí</a></p>
    </form>
</div>
</body>
<footer>
<p xmlns:cc="http://creativecommons.org/ns#" xmlns:dct="http://purl.org/dc/terms/"><a property="dct:title" rel="cc:attributionURL" href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev">WebVulnLab</a> by <a rel="cc:attributionURL dct:creator" property="cc:attributionName" href="https://github.com/sil3ntH4ck3r">sil3nth4ck3r</a> is licensed under <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1" target="_blank" rel="license noopener noreferrer" style="display:inline-block;">CC BY-NC-SA 4.0
</footer>
</html>