<?php
// Conexión a la base de datos
$db = mysqli_connect("db", "usuario", "contraseña", "database");

// Vaciar la tabla productos
mysqli_query($db, "TRUNCATE TABLE productos");

?>
