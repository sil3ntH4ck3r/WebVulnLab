<?php
// Conectar a la base de datos
$conexion = mysqli_connect("db", "usuario", "contraseña", "database");
if ($conexion) {
  $conexion->set_charset("utf8");
}
//$connection->set_charset("utf8");

// Comprobar la conexión
if (mysqli_connect_errno()) {
  die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

$sql = "DROP TABLE IF EXISTS usuarios";
if (mysqli_query($conexion, $sql)) {
  echo "La tabla usuarios ha sido eliminada exitosamente";
} else {
  echo "Error al eliminar la tabla: " . mysqli_error($conexion);
}

$sql = "CREATE TABLE usuarios (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(30) NOT NULL,
    contraseña VARCHAR(30) NOT NULL,
    email VARCHAR(50) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  )";
  
  if (mysqli_query($conexion, $sql)) {
    echo "La tabla usuarios ha sido creada exitosamente";
  } else {
    echo "Error al crear la tabla: " . mysqli_error($conexion);
  }

// Insertar un nuevo usuario
//$nombre = "juan";
//$contraseña = "secreta";
//$sql = "INSERT INTO usuarios (nombre, contraseña) VALUES ('$nombre', '$contraseña')";
//if (!mysqli_query($conexion, $sql)) {
//  die("Error al insertar usuario: " . mysqli_error($conexion));
//}

//echo "Usuario insertado correctamente";

// Cerrar la conexión
mysqli_close($conexion);
?>