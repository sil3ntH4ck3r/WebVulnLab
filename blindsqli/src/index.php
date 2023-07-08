<!DOCTYPE html>
<html>
<head>
    <title>Blind SQL Injection</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="blindsqli.css">
</head>
<body>

<?php
$conexion = mysqli_connect("db", "usuario", "contraseña", "database");
if ($conexion) {
  $conexion->set_charset("utf8");
}

// Comprobar la conexión
if (mysqli_connect_errno()) {
  die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Verificar si la tabla ya existe
$table_name = "usuarios";
$sql = "SELECT 1 FROM $table_name LIMIT 1";
$result = mysqli_query($conexion, $sql);

if ($result === false) {
  // La tabla no existe, crearla
  // Conexión a la base de datos
  $db = mysqli_connect("db", "usuario", "contraseña", "database");

  // Crear tabla usuarios
  $create_table_query = "CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
)";

  if (mysqli_query($db, $create_table_query)) {
    echo '<script>alert("La tabla ha sido creada correctamente.");</script>';
  } else {
    echo '<script>alert("Error al crear la tabla: ' . mysqli_error($db) . '");</script>';
  }
} 

// Cerrar la conexión
mysqli_close($conexion);
?>


<header>
    <nav>
        <div class="container">
            <h1 class="logo">Cybertec</h1>
            <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input id="search-bar" type="text" name="busqueda" placeholder="Buscar productos">
                <button type="submit" id="search-btn">Buscar</button>
                <button onclick="eliminarBaseDatos()" type="button" id="search-btn">Reiniciar Base de Datos</button>

                <script>
                function eliminarBaseDatos() {
                    window.location.href = "eliminar.php";
                }
                </script>

            </form>
        </div>
    </nav>
</header>
<h1>Este sitio web se encuentra en labores de mantenimiento.</h1>

<?php
// Conexión a la base de datos
$db = mysqli_connect("db", "usuario", "contraseña", "database");

// Verificar si la tabla usuarios ya contiene datos
$result = mysqli_query($db, "SELECT COUNT(*) FROM usuarios");
$row = mysqli_fetch_row($result);
if ($row[0] == 0) {
    // La tabla usuarios está vacía, insertar los usuarios
    // Lista de usuarios a insertar
    $usuarios = [
        ['nombre' => 'Etelvina Viera Gonzales', 'email' => 'EtelvinaVieraGonzales@blindsqli.local', 'password' => 'jee2kaZ8'],
        ['nombre' => 'Gerald Rico Camacho', 'email' => 'GeraldRicoCamacho@blindsqli.local', 'password' => 'Zoon7eifee'],
        ['nombre' => 'Apólito Olvera Lira', 'email' => 'ApolitoOlveraLira@blindsqli.local', 'password' => 'ho1Oosia'],
        ['nombre' => 'Betsabé Sandoval Roybal', 'email' => 'BetsabeSandovalRoybal@blindsqli.local', 'password' => 'oH3aeDahh'],
    ];

    // Insertar los usuarios en la tabla usuarios
    foreach ($usuarios as $usuario) {
        $nombre = $usuario['nombre'];
        $email = $usuario['email'];
        $password = $usuario['password'];

        $sql = "INSERT INTO usuarios (nombre, email, password) VALUES ('$nombre', '$email', '$password')";
        $result = mysqli_query($db, $sql);

        if ($result === false) {
            echo "Error al insertar los usuarios: " . mysqli_error($db);
        }
    }
}

// Obtener la búsqueda del usuario, si se ha enviado
$busqueda = "";
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $busqueda = $_GET["busqueda"];
}

// Consulta para obtener los usuarios
if (empty($busqueda)) {
    $result = mysqli_query($db, "SELECT * FROM usuarios WHERE id=1 LIMIT 1");
} else {
    $result = mysqli_query($db, "SELECT * FROM usuarios WHERE id='$busqueda' LIMIT 1");
}

if ($result === false) {
    die("Error al obtener los usuarios: " . mysqli_error($db));
}

echo '<div class="usuarios-container">';
echo "<div id='counter' style='margin: auto;'>";
echo "<h3>Le informamos cuando volvamos a estar disponibles.</h3>";
echo '<form action="comprar.php" method="POST">';
echo '  <div class="subscribe-form">';
echo '      <input type="email" name="email" id="email" placeholder="Ingrese su correo electrónico" required>';
echo '      <button class="boton-comprar-gratis">Apúntate</button>';
echo '  </div>';
echo '</form>';
echo "</div>";
?>

</body>

<footer>
<p xmlns:cc="http://creativecommons.org/ns#" xmlns:dct="http://purl.org/dc/terms/"><a property="dct:title" rel="cc:attributionURL" href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev">WebVulnLab</a> by <a rel="cc:attributionURL dct:creator" property="cc:attributionName" href="https://github.com/sil3ntH4ck3r">sil3nth4ck3r</a> is licensed under <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1" target="_blank" rel="license noopener noreferrer" style="display:inline-block;">CC BY-NC-SA 4.0
</footer>
</html>
