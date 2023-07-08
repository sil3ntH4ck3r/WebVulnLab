<?php
$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");

// Verificar si se envió el formulario de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Crear un filtro para buscar el usuario en la base de datos
    $filter = [
        'username' => $username,
        'password' => $password
    ];

    // Crear una consulta con el filtro
    $query = new MongoDB\Driver\Query($filter);

    // Ejecutar la consulta en la base de datos
    $rows = $manager->executeQuery('test.users', $query);

    // Verificar si se encontró el usuario
    $loggedIn = false;
    foreach ($rows as $row) {
        $loggedIn = true;
        // Realizar acciones correspondientes para el inicio de sesión exitoso
    }

    if (!$loggedIn) {
        // Mostrar un mensaje de error o redirigir a otra página en caso de inicio de sesión fallido
        echo "Inicio de sesión fallido";
    }
}

// Mostrar el formulario de inicio de sesión
?>
<!DOCTYPE html>
<html>
<head>
    <title>Iniciar sesión</title>
</head>
<body>
    <h2>Iniciar sesión</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <label for="username">Nombre de usuario:</label>
        <input type="text" name="username" id="username" required><br>
        
        <label for="password">Contraseña:</label>
        <input type="password" name="password" id="password" required><br>
        
        <input type="submit" value="Iniciar sesión">
    </form>
</body>
</html>
