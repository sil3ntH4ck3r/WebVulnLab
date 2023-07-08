<?php
// Conexión a la base de datos
$db = mysqli_connect("db", "usuario", "contraseña", "database");

// Vaciar la tabla usuarios
mysqli_query($db, "DROP TABLE usuarios");

header("Location: index.php"); //redirigir al usuario a la página de inicio

?>
