<?php

// Conectar a la base de datos
$conexion = mysqli_connect("db", "usuario", "contraseña", "database");
if ($conexion) {
  $conexion->set_charset("utf8");
}

// Ejecutar una sentencia SELECT para recuperar los datos de la tabla usuarios
$sql = "SELECT * FROM usuarios";
$resultado = mysqli_query($conexion, $sql);
if (!$resultado) {
  die("Error al ejecutar la consulta: " . mysqli_error($conexion));
}

// Recorrer los resultados y mostrarlos en pantalla
while ($fila = mysqli_fetch_assoc($resultado)) {
  echo "ID: " . $fila['id'] . "<br>";
  echo "Nombre: " . $fila['nombre'] . "<br>";
  echo "Email: " . $fila['email'] . "<br>";
  echo "Contraseña: " . $fila['contraseña'] . "<br>";
  echo "<hr>";
}

// Liberar el resultado y cerrar la conexión a la base de datos
mysqli_free_result($resultado);
mysqli_close($conexion);

?>