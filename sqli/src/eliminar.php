<?php
// Conexión a la base de datos
$db = $conexion = mysqli_connect("127.0.0.1", "usuario", "password", "database");

// Vaciar la tabla productos
mysqli_query($db, "TRUNCATE TABLE productos");

header("Location: index.php"); //redirigir al usuario a la página de inicio

?>
