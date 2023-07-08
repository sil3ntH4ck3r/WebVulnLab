<?php
$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");

// Verificar si se envió el formulario de inserción
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Crear un nuevo documento con los datos del usuario
    $userDocument = [
        'username' => $username,
        'password' => $password
    ];

    // Insertar el documento en la base de datos
    $bulk = new MongoDB\Driver\BulkWrite();
    $bulk->insert($userDocument);
    $manager->executeBulkWrite('database.usuarios', $bulk);
}

// Mostrar el formulario de inserción
?>
<!DOCTYPE html>
<html>
<head>
    <title>Insertar usuario</title>
</head>
<body>
    <h2>Insertar usuario</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <label for="username">Nombre de usuario:</label>
        <input type="text" name="username" id="username" required><br>
        
        <label for="password">Contraseña:</label>
        <input type="password" name="password" id="password" required><br>
        
        <input type="submit" value="Insertar">
    </form>
</body>
</html>