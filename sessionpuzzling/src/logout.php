<?php
session_start();

// Conectar a la base de datos
$conexion = mysqli_connect("db", "usuario", "contraseña", "database");
if ($conexion) {
    $conexion->set_charset("utf8");
}

// Verificar si la conexión fue exitosa
if (!$conexion) {
    die('Error al conectar a la base de datos: ' . mysqli_connect_error());
}

// Obtener el nombre de usuario de la sesión
$nombreUsuario = $_SESSION['user'];

// Eliminar el valor de session_id de la base de datos para el usuario correspondiente
$sqlDelete = "UPDATE usuarios SET session_id = NULL WHERE nombre = ?";
$stmtDelete = $conexion->prepare($sqlDelete);
$stmtDelete->bind_param("s", $nombreUsuario);
$stmtDelete->execute();

// Cerrar la conexión a la base de datos
mysqli_close($conexion);

// Limpiar y destruir la sesión
unset($_SESSION['user']);
unset($_SESSION['loggedin']);
setcookie("session_id", "", time() - 3600, "/");
session_destroy();

// Redireccionar al usuario a la página de inicio
header("Location: index.php");
exit;
?>