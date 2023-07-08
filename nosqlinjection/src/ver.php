<?php
$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");

// Obtener los documentos de la colección 'users'
$query = new MongoDB\Driver\Query([]);
$cursor = $manager->executeQuery('database.usuarios', $query);

// Mostrar los documentos en una tabla
?>
<!DOCTYPE html>
<html>
<head>
    <title>Usuarios</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <h2>Usuarios</h2>
    <table>
        <tr>
            <th>Nombre de usuario</th>
            <th>Contraseña</th>
        </tr>
        <?php foreach ($cursor as $document): ?>
        <tr>
            <td><?php echo $document->username; ?></td>
            <td><?php echo $document->password; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>