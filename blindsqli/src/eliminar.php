<?php
// Conexión a la base de datos
$db = mysqli_connect("db", "usuario", "contraseña", "database");

// Vaciar la tabla productos
mysqli_query($db, "TRUNCATE TABLE productos");

header("Location: index.php"); //redirigir al usuario a la página de inicio

?>
