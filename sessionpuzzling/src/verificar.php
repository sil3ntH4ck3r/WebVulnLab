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

// Obtener los valores del formulario
$nombre = $_POST['nombre'];
$contraseña = $_POST['contraseña'];
$sessionId = $_GET['session_id'];

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

    // Obtener el email correspondiente al usuario
    $fila = mysqli_fetch_assoc($resultado);
    $email = $fila['email'];

    // Insertar el valor de session_id en la base de datos
    $sqlInsert = "UPDATE usuarios SET session_id = ? WHERE nombre = ? AND contraseña = ?";
    $stmtInsert = $conexion->prepare($sqlInsert);
    $stmtInsert->bind_param("sss", $sessionId, $nombre, $contraseña);
    $stmtInsert->execute();
    
    mysqli_close($conexion);

    // Redireccionar al usuario
    $redirectUrl = "http://sessionpuzzling.local/perfil.php?session_id=" . $sessionId;
    header("Location: $redirectUrl");
    exit;
} else {
    // Si no se encontró ningún registro que coincida, mostrar un mensaje de error al usuario
    $_SESSION['mensaje'] = 'Nombre de usuario o contraseña incorrectos';
    $_SESSION['loggedin'] = false;
    mysqli_close($conexion);

    // Redireccionar al usuario
    header('Location: index.php');
    exit;
}
?>
