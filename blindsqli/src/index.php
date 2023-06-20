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
$table_name = "productos";
$sql = "SELECT 1 FROM $table_name LIMIT 1";
$result = mysqli_query($conexion, $sql);

if ($result === false) {
  // La tabla no existe, crearla
  // Conexión a la base de datos
    $db = mysqli_connect("db", "usuario", "contraseña", "database");

    // Crear tabla productos
    mysqli_query($db, "CREATE TABLE productos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(255) NOT NULL,
        descripcion TEXT,
        precio DECIMAL(10,2) NOT NULL
    )");
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
                    <button href="eliminar.php" type="submit" id="search-btn">Reiniciar Base de Datos</button>
                </form>
            </div>
        </nav>
    </header>
    <h1>Nuestos antivirus</h1>

    <?php
        error_reporting(0); //desactivar warnings
        // Conexión a la base de datos
        $db = mysqli_connect("db", "usuario", "contraseña", "database");

        // Verificar si la tabla productos ya contiene datos
        $result = mysqli_query($db, "SELECT COUNT(*) FROM productos");
        $row = mysqli_fetch_row($result);
        if ($row[0] == 0) {
            // La tabla productos está vacía, insertar los productos

            // Lista de productos a insertar
            $productos = [
                ['nombre' => 'SecureShield Basic', 'descripcion' => 'Protección en tiempo real contra virus y malware. Actualizaciones automáticas. Análisis de archivos y enlaces sospechosos. Navegación segura en internet.', 'precio' => 49.99],
                ['nombre' => 'SecureShield Plus', 'descripcion' => 'Incluye todas las ventajas de SecureShield Basic y más: Detección avanzada de ransomware. Cortafuegos integrado. Protección de identidad y privacidad en línea.', 'precio' => 69.99],
                ['nombre' => 'SecureShield Pro', 'descripcion' => 'Incluye todas las ventajas de SecureShield Plus y más: Protección de múltiples dispositivos. Escaneo programado y automático. Protección avanzada contra phishing y suplantación de identidad.', 'precio' => 89.99],
                ['nombre' => 'SecureShield Ultimate', 'descripcion' => 'Incluye todas las ventajas de SecureShield Pro y más: Escudo de navegación seguro. Protección de archivos confidenciales. Herramientas de optimización del sistema.', 'precio' => 109.99],
            ];


            // Insertar los productos en la tabla productos
            foreach ($productos as $producto) {
                mysqli_query($db, "INSERT INTO productos (nombre, descripcion, precio) VALUES ('" . $producto['nombre'] . "', '" . $producto['descripcion'] . "', " . $producto['precio'] . ")");
            }

        }

        // Obtener la búsqueda del usuario, si se ha enviado
        $busqueda = "";
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            $busqueda = $_GET["busqueda"];
        }

        // Consulta para obtener los productos
        if (empty($busqueda)) {
            $result = mysqli_query($db, "SELECT * FROM productos");
        } else {
            $result = mysqli_query($db, "SELECT * FROM productos WHERE descripcion LIKE '%" . $busqueda . "%'");
        }
        echo '<div class="productos-container">';
        // Mostrar los productos
        $count = mysqli_num_rows($result);
        if ($count == 0) {
            echo "<div id='counter' style='margin: auto;'>";
            echo "<h3>No se encontraron resultados para su búsqueda.</h3>";
            echo "</div>";
        } else {
            while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='producto'>";
            echo "<h2>" . $row['nombre'] . "</h2>";
            echo "<p>" . $row['descripcion'] . "</p>";
            echo "<p class='precio'>Precio: $" . $row['precio'] . "</p>";
            echo '<form action="comprar.php" method="POST">';
            echo '  <div class="subscribe-form">';
            echo '      <input type="hidden" name="nombre_producto" value="' . $row['nombre'] . '">';
            echo '      <input type="email" name="email" id="email" placeholder="Ingrese su correo electrónico" required>';
            echo '      <button class="boton-comprar-gratis">Solicitar prueba</button>';
            echo '  </div>';
            echo '</form>';
            echo "</div>";
            }
        }
        echo "</div>";
    ?>
</body>

<footer>
<p xmlns:cc="http://creativecommons.org/ns#" xmlns:dct="http://purl.org/dc/terms/"><a property="dct:title" rel="cc:attributionURL" href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev">WebVulnLab</a> by <a rel="cc:attributionURL dct:creator" property="cc:attributionName" href="https://github.com/sil3ntH4ck3r">sil3nth4ck3r</a> is licensed under <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1" target="_blank" rel="license noopener noreferrer" style="display:inline-block;">CC BY-NC-SA 4.0
</footer>
</html>
