<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost:2375/containers/json?all=1");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch);
curl_close($ch);
$containers = json_decode($output, true);

$encendidos = 0; // Inicializar contador de contenedores encendidos
$apagados = 0; // Inicializar contador de contenedores apagados

foreach ($containers as $container) {
    if (strpos($container['Status'], 'Up') !== false) {
        $encendidos++; // Incrementar contador de contenedores encendidos
    } else {
        $apagados++; // Incrementar contador de contenedores apagados
    }
}

$totalContenedores = $apagados + $encendidos;

if (isset($_POST['action']) && isset($_POST['container_id'])) {
    $action = $_POST['action'];
    $containerId = $_POST['container_id'];

    if ($action === 'delete') {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://localhost:2375/containers/$containerId");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        if ($statusCode === 204) {
            header('Location: ' . "http://tablero.local/");
            exit();
        } else {
            echo "<script>alert('Error al eliminar el contenedor');</script>";
        }
    } else {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://localhost:2375/containers/$containerId/$action");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        if ($statusCode === 204) {
            header('Location: ' . "http://tablero.local/");
            exit();
        } else {
            echo "<script>alert('Error al $action el contenedor');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tablero</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="api.css">
    <meta charset="UTF-8">

    <style>
            form {
              display: inline-block;
            }

            form button {
              display: inline-block;
              margin-right: 5px;
            }
    </style>

</head>
<body>
<header>
    <h1>Tablero</h1>
</header>
<h1>Contenedores Docker</h1>

<div id="counter" style="margin: auto;">

        <?php

            if ($totalContenedores === 0) {
                echo "<h3>NO HAY CONTENEDORES DESPLEGADOS</h3>";
            } else {
                echo "<h3>Contenedores en ejecución: $encendidos </h3> <!-- Mostrar número de contenedores encendidos -->
                <h3>Contenedores apagados: $apagados </h3> <!-- Mostrar número de contenedores apagados -->
                <h3>Total contenedores: $totalContenedores </h3> <!-- Mostrar número de contenedores totales -->";
            }
        ?>
</div>


<ul>
    <?php foreach ($containers as $container): ?>
        <li>
            <h2><?php echo $container['Names'][0]; ?></h2>

            <?php if (strpos($container['Status'], 'Up') !== false): ?>
                <form method="POST">
                    <input type="hidden" name="action" value="stop">
                    <input type="hidden" name="container_id" value="<?php echo $container['Id']; ?>">
                    <button class="stop-button" type="submit">Apagar</button>
                </form>
                <form method="POST">
                    <input type="hidden" name="action" value="restart">
                    <input type="hidden" name="container_id" value="<?php echo $container['Id']; ?>">
                    <button class="restart-button" type="submit">Reiniciar</button>
                </form>
            <?php else: ?>
                <form method="POST">
                    <input type="hidden" name="action" value="start">
                    <input type="hidden" name="container_id" value="<?php echo $container['Id']; ?>">
                    <button class="start-button"type="submit">Encender</button>
                </form>

                <form method="POST">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="container_id" value="<?php echo $container['Id']; ?>">
                    <button class="delete-button" type="submit" onclick="confirmDelete(event, '<?php echo $container['Names'][0]; ?>', this.form)">Eliminar</button>

                </form>

            <?php endif; ?>

            <script>
                function confirmDelete(event, containerName, form) {
                    event.preventDefault(); // evita el envío del formulario si el usuario hace clic en "Cancelar"
                    if (confirm("¿Está seguro de que desea eliminar el contenedor " + containerName + "?")) {
                        // Si el usuario confirma la eliminación, envía la solicitud al servidor
                        form.submit();
                    }


//                    Swal.fire({
//                        title: '¿Está seguro de que desea eliminar el contenedor ' + containerName + '?',
//                        text: "¡Esta acción no se puede deshacer!",
//                        icon: 'warning',
//                        showCancelButton: true,
//                        confirmButtonColor: '#3085d6',
//                        cancelButtonColor: '#d33',
//                        confirmButtonText: 'Sí, eliminarlo'
//                    }).then((result) => {
//                        if (result.isConfirmed) {
//                            
//                            form.submit();
//                            
//                        }
//                    })
                }
            </script>
        </li>
    <?php endforeach; ?>
</ul>
</body>
<footer>
    <p>Derechos de autor © 2023. Todos los derechos reservados.</p>
</footer>
</html>