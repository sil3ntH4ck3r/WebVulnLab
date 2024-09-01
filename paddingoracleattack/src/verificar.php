<?php
session_start();

// Comprobar si el usuario ya ha iniciado sesión
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $_SESSION['mensaje'] = 'Ya tienes una sesión activa, cierrala para iniciar sesión';
    header('Location: index.php'); // Redirigir a la página principal u otra de tu elección
    exit;
}

// Conectar a la base de datos
$conexion = mysqli_connect("db", "usuario", "contraseña", "database");
if ($conexion) {
  $conexion->set_charset("utf8");
}

// Verificar si la conexión fue exitosa
if (!$conexion) {
    die('Error al conectar a la base de datos: ' . mysqli_connect_error());
}

// Obtener los valores del formulario
$nombre = $_POST['nombre'];
$contraseña = $_POST['contraseña'];

// Crear una consulta SQL para buscar el nombre de usuario y la contraseña en la tabla
$sql = "SELECT * FROM usuarios WHERE nombre = '$nombre' AND contraseña = '$contraseña'";

// Ejecutar la consulta SQL
$resultado = mysqli_query($conexion, $sql);

if ($resultado === false) {
    die('Error al ejecutar la consulta SQL: ' . mysqli_error($conexion));
}

// Verificar si se encontró algún registro que coincida con los datos ingresados
if (mysqli_num_rows($resultado) > 0) {
    // Si se encontró un registro que coincida, mostrar un mensaje al usuario
    $_SESSION['mensaje'] = '¡Bienvenido!';
    $_SESSION['loggedin'] = true;
    $_SESSION['user'] = $_POST['nombre'];
} else {
    // Si no se encontró ningún registro que coincida, mostrar un mensaje de error al usuario
    $_SESSION['mensaje'] = 'Nombre de usuario o contraseña incorrectos';
    $_SESSION['loggedin'] = false;
}

// Cerrar la conexión a la base de datos
mysqli_close($conexion);

header('Location: index.php');
exit;

?>