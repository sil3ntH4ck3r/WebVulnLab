<!DOCTYPE html>
<html>
<head>
    <title>Tienda en línea</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="sqli.css">
</head>
<body>

    <header>
        <nav>
            <ul>
                <!-- <button class="new-message-button">Nuevo Mensaje</button> -->
            </ul>
            <div class="container">
                <h1 class="logo">Cybertec</h1>
                <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input id="search-bar" type="text" name="busqueda" placeholder="Buscar productos">
                    <button type="submit" id="search-btn">Buscar</button>
                </form>
            </div>
        </nav>
    </header>
    <h1>Comentarios</h1>

    <?php
        // Conexión a la base de datos
        $db = mysqli_connect("db", "usuario", "contraseña", "database");

        // Verificar si la tabla productos ya contiene datos
        $result = mysqli_query($db, "SELECT COUNT(*) FROM productos");
        $row = mysqli_fetch_row($result);
        if ($row[0] == 0) {
            // La tabla productos está vacía, insertar los productos

            // Lista de productos a insertar
            $productos = [
                ['nombre' => 'Producto 1', 'descripcion' => 'Descripción del producto 1', 'precio' => 9.99],
                ['nombre' => 'Producto 2', 'descripcion' => 'Descripción del producto 2', 'precio' => 19.99],
                ['nombre' => 'Producto 3', 'descripcion' => 'Descripción del producto 3', 'precio' => 29.99],
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
            $result = mysqli_query($db, "SELECT * FROM productos WHERE nombre LIKE '%" . $busqueda . "%'");
        }

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
            echo "</div>";
            }
        }
    ?>

    <div class="message-popup">
        <p>Mensaje recibido de Juan:</p>
        <p>¡Hola! ¿Cómo estás?</p>
    </div>

    <script>
        document.querySelector('.new-message-button').addEventListener('click', function() {
            var messagePopup = document.querySelector('.message-popup');
            
            messagePopup.style.display = 'block';
            
            setTimeout(function() {
                messagePopup.style.display = 'none';
            },3000); // Desaparece después de tres segundos
        });
    </script>
</body>

<footer>
    <p>Derechos de autor © 2023. Todos los derechos reservados.</p>
</footer>
</html>
