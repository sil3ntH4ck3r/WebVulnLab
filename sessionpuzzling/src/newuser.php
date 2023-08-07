<?php

// Conectar a la base de datos
$conexion = mysqli_connect("db", "usuario", "contraseña", "database");
if ($conexion) {
  $conexion->set_charset("utf8");
}

// Comprobar la conexión
if (mysqli_connect_errno()) {
  die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Recuperar los valores ingresados por el usuario
$nombre = $_POST['nombre'];
$email = $_POST['email'];
$contraseña = $_POST['contraseña'];

// Verificar si ya existe un usuario con el mismo correo electrónico
$sql = "SELECT * FROM usuarios WHERE nombre='$nombre'";
$resultado = mysqli_query($conexion, $sql);
if (mysqli_num_rows($resultado) > 0) {
  // Ya existe un usuario con ese correo electrónico
  echo "Ya existe un usuario registrado con ese nombre de usuario";
} else {

  // Insertar un nuevo usuario en la tabla usuarios
  $sql = "INSERT INTO usuarios (nombre, email, contraseña) VALUES ('$nombre', '$email', '$contraseña')";
  if (!mysqli_query($conexion, $sql)) {
    die("Error al insertar usuario: " . mysqli_error($conexion));
  }

  // Mostrar un mensaje al usuario indicando que se ha registrado correctamente
  echo "Usuario registrado correctamente";
}

// Cerrar la conexión a la base de datos
mysqli_close($conexion);

?>