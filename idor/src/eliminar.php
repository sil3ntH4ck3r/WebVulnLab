<?php
// Conexión a la base de datos
$conexion = mysqli_connect("db", "usuario", "contraseña", "database");
if ($conexion) {
  $conexion->set_charset("utf8");
}
//$connection->set_charset("utf8");

// Comprobar la conexión
if (mysqli_connect_errno()) {
  die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

$sql = "DROP TABLE IF EXISTS Users";
mysqli_query($conexion, $sql);
$sql = "CREATE TABLE Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
  )";
  mysqli_query($conexion, $sql);

// Cerrar la conexión
mysqli_query($conexion, "INSERT INTO Users (name, email, password) VALUES ('admin', 'admin@idor.local','P@$\$w0rd!')");
mysqli_close($conexion);

session_start();
session_destroy();

header("Location: index.php"); //redirigir al usuario a la página de inicio
exit();

?>
