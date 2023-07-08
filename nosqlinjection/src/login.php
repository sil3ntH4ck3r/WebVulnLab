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
    $rows = $manager->executeQuery('database.usuarios', $query);

    // Verificar si se encontró el usuario
    $loggedIn = false;
    foreach ($rows as $row) {
        $loggedIn = true;
        // Realizar acciones correspondientes para el inicio de sesión exitoso
        echo "Inicio de sesión exitoso. ¡Bienvenido, $username!";
    }

    if (!$loggedIn) {
        // Mostrar un mensaje de error o redirigir a otra página en caso de inicio de sesión fallido
        echo "Inicio de sesión fallido";
    }
}

// Agregar usuarios automáticamente si no existen
$usersToInsert = [
    ['username' => 'user1', 'password' => 'password1'],
    ['username' => 'user2', 'password' => 'password2'],
    ['username' => 'user3', 'password' => 'password3']
];

foreach ($usersToInsert as $user) {
    $filter = ['username' => $user['username']];
    $query = new MongoDB\Driver\Query($filter);
    $rows = $manager->executeQuery('database.usuarios', $query);

    $userExists = false;
    foreach ($rows as $row) {
        $userExists = true;
        break;
    }

    if (!$userExists) {
        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->insert($user);
        $manager->executeBulkWrite('database.usuarios', $bulk);
        echo "Usuario agregado automáticamente: " . $user['username'] . "<br>";
    }
}

// Reiniciar la base de datos al pulsar el botón
if (isset($_POST['reset'])) {
    $bulkDelete = new MongoDB\Driver\BulkWrite();
    $bulkDelete->delete([]);
    $manager->executeBulkWrite('database.usuarios', $bulkDelete);

    // Agregar los usuarios definidos por defecto
    $bulkInsert = new MongoDB\Driver\BulkWrite();
    foreach ($usersToInsert as $user) {
        $bulkInsert->insert($user);
    }
    $manager->executeBulkWrite('database.usuarios', $bulkInsert);

    echo "Base de datos reiniciada. Todo el contenido ha sido eliminado y se han agregado los usuarios por defecto.";
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
    
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <input type="hidden" name="reset" value="1">
        <input type="submit" value="Reiniciar base de datos">
    </form>
</body>
</html>

