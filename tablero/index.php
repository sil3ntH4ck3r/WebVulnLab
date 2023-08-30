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
        <nav class="container">
            <h1>Admin Panel</h1>
            <a href="http://tablero.local/oldVersion">Versión antigua</a> <!-- Agrega esta línea -->
        </nav>
        </header>
    <main class="container">
        <div class="card">
            <h2>Contenedores Docker</h2>
            <div class="counters">
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
        </div>

        <ul>
            <?php foreach ($containers as $container): ?>
            <li class="card">
                <h2><?php echo $container['Names'][0]; ?></h2>
                <div class="buttons">
                    <?php if (strpos($container['Status'], 'Up') !== false): ?>
                    <form method="POST">
                        <input type="hidden" name="action" value="stop">
                        <input type="hidden" name="container_id" value="<?php echo $container['Id']; ?>">
                        <button class="stop" type="submit">Apagar</button>
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
                        <button class="start-button" type="submit">Encender</button>
                    </form>

                    <form method="POST">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="container_id" value="<?php echo $container['Id']; ?>">
                        <button class="delete" type="submit"
                            onclick="confirmDelete(event, '<?php echo $container['Names'][0]; ?>', this.form)">Eliminar</button>

                    </form>

                    <?php endif; ?>
                </div>

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
    </main>
    <footer>
        <p xmlns:cc="http://creativecommons.org/ns#" xmlns:dct="http://purl.org/dc/terms/" style="text-align: center";>
            <a property="dct:title" rel="cc:attributionURL" href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev">WebVulnLab</a> by 
            <a rel="cc:attributionURL dct:creator" property="cc:attributionName" href="https://github.com/sil3ntH4ck3r">sil3nth4ck3r</a> is licensed under 
            <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1" target="_blank" rel="license noopener noreferrer" style="display:inline-block;">CC BY-NC-SA 4.0</a>
            <br>
            Improved by <a href="https://github.com/RogelioLB">RogelioLB</a>
        </p>
    </footer>
</body>

</html>
