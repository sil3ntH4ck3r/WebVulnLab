<?php
/*
=================================
Configuración y funciones globales
=================================
*/
define('DOCKER_API_URL', 'http://localhost:2375'); // URL base para la API de Docker

function dockerApiCall($endpoint, $method = 'GET', $returnTransfer = true) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, DOCKER_API_URL . $endpoint);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, $returnTransfer);
    $response = curl_exec($ch);

    $statusCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);

    if ($returnTransfer && !empty($response)) {
        $decoded = json_decode($response, true);
        return $decoded !== null ? $decoded : $response;
    }
    return null;
}

function getContainers($all = true) {
    $endpoint = '/containers/json' . ($all ? '?all=1' : '');
    $containers = dockerApiCall($endpoint);
    return is_array($containers) ? $containers : [];
}

function getContainerInfo($containerId) {
    return dockerApiCall("/containers/$containerId/json");
}

function processContainerName($containerName) {
    $containerName = ltrim($containerName, '/');
    if (strpos($containerName, '_db_') === false) {
        $containerName = str_replace('_v2', '', $containerName);
    }
    return $containerName;
}

function manageContainer($action, $containerId) {
    $method = ($action === 'delete') ? 'DELETE' : 'POST';
    $endpoint = ($action === 'delete') ?
        "/containers/$containerId" :
        "/containers/$containerId/$action";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, DOCKER_API_URL . $endpoint);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $responseBody = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);

    $decoded = json_decode($responseBody, true);
    $decoded = $decoded !== null ? $decoded : $responseBody;

    return [
        'statusCode' => $statusCode,
        'response'   => $decoded
    ];
}

/*
=================================
Lógica principal
=================================
*/
$containers = getContainers(true);
$encendidos = 0;
$apagados = 0;

foreach ($containers as $container) {
    if (strpos($container['Status'], 'Up') !== false) {
        $encendidos++;
    } else {
        $apagados++;
    }
}

$totalContenedores = $encendidos + $apagados;

/*
  Manejo de acciones del formulario (start, stop, restart, delete).
*/
if (isset($_POST['action'], $_POST['container_id'])) {
    $action = $_POST['action'];
    $containerId = $_POST['container_id'];

    $resultado = manageContainer($action, $containerId);
    $statusCode = $resultado['statusCode'];
    $response   = $resultado['response'];

    switch ($action) {
        case 'delete':
            if ($statusCode === 204) {
                header('Location: /');
                exit();
            } else {
                echo "<script>alert('Error al eliminar el contenedor');</script>";
            }
            break;
        case 'start':
            if ($statusCode === 204) {
                header('Location: /');
                exit();
            } else {
                if (is_array($response) && isset($response['message'])) {
                    $errorMessage = $response['message'];
                    if (strpos($errorMessage, 'address already in use') !== false) {
                        preg_match('/(?<=0\.0\.0\.0:)\d+/', $errorMessage, $portMatches);
                        $portInUse = isset($portMatches[0]) ? $portMatches[0] : "desconocido";
                        echo "<script>
                            alert('El puerto $portInUse ya está en uso en su sistema. Asegúrese de que el puerto $portInUse esté libre antes de intentar nuevamente.');
                        </script>";
                    } elseif (preg_match('/Cannot link to a non running container: (.+) AS (.+)/', $errorMessage, $matches)) {
                        $contenedor = str_replace('/db', '', $matches[1]);
                        $contenedor1 = str_replace('/db', '', $matches[2]);
                        $contenedor1 = rtrim($contenedor1, "\n");
                        echo "<script>
                            alert('Debe iniciar primero el contenedor $contenedor para encender $contenedor1.');
                        </script>";
                    } else {
                        $safeMessage = addslashes($errorMessage);
                        echo "<script>alert('Error no contemplado: " . $safeMessage . "');</script>";
                    }
                } else {
                    echo "<script>alert('Error desconocido al iniciar el contenedor.');</script>";
                }
            }
            break;
        case 'stop':
            if ($statusCode === 204) {
                header('Location: /');
                exit();
            } else {
                echo "<script>alert('Error al detener el contenedor');</script>";
            }
            break;
        case 'restart':
            if ($statusCode === 204) {
                header('Location: /');
                exit();
            } else {
                echo "<script>alert('Error al reiniciar el contenedor');</script>";
            }
            break;
        default:
            echo "<script>alert('Acción no válida');</script>";
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Tablero Docker</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="all.min.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/fontawesome.min.css"> -->
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #3b82f6;
            --background: #0f172a;
            --card-bg: #1e293b;
            --success: #22c55e;
            --danger: #ef4444;
            --warning: #f59e0b;
            --text: #f8fafc;
            --text-light: #cbd5e1;
            --border-radius: 8px;
            --shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --transition: all 0.3s ease;
        }
        * {
            box-sizing: border-box;
            padding: 0;
            margin: 0;
        }
        body {
            background-color: var(--background);
            font-family: "Poppins", system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--text);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            line-height: 1.6;
        }
        header {
            background-color: var(--card-bg);
            padding: 1rem 0;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 10;
        }
        nav.container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        nav h1 {
            font-weight: 600;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        nav h1:before {
            content: "\f0c9";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            color: var(--primary);
        }
        nav a {
            background-color: var(--primary);
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            font-weight: 500;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        nav a:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }
        nav a:before {
            content: "\f060";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
        }
        main {
            padding: 2rem 1rem;
            flex-grow: 1;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        .dashboard-summary {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            border-radius: var(--border-radius);
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
        }
        .dashboard-summary h2 {
            font-size: 1.75rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .dashboard-summary h2:before {
            content: "\f0ae";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
        }
        .counters {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        .counter-item {
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 1.25rem;
            border-radius: var(--border-radius);
            text-align: center;
            transition: var(--transition);
        }
        .counter-item:hover {
            transform: translateY(-5px);
            background-color: rgba(255, 255, 255, 0.15);
        }
        .counter-item h3 {
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 0.75rem;
        }
        .counter-item .count {
            font-size: 2.25rem;
            font-weight: 700;
        }
        .container-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem;
        }
        .container-card {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }
        .container-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .container-header {
            padding: 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .container-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .container-header a {
            color: var(--text);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition);
        }
        .container-header a:hover {
            color: var(--primary);
        }
        .container-header a:after {
            content: "\f08e";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            font-size: 0.875rem;
        }
        .container-status {
            padding: 0.5rem 1.25rem;
            background-color: rgba(0, 0, 0, 0.2);
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .status-running:before {
            content: "\f111";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            color: var(--success);
        }
        .status-stopped:before {
            content: "\f111";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            color: var(--danger);
        }
        .container-details {
            padding: 1.25rem;
            color: var(--text-light);
            font-size: 0.875rem;
        }
        .container-details p {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }
        .container-details p:before {
            content: "\f0ac";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            color: var(--secondary);
        }
        .container-actions {
            padding: 1.25rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }
        form {
            display: inline-block;
        }
        button {
            border: none;
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
            border-radius: var(--border-radius);
            font-family: inherit;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text);
        }
        button:hover {
            transform: translateY(-2px);
        }
        .btn-start {
            background-color: var(--success);
        }
        .btn-start:hover {
            background-color: #16a34a;
        }
        .btn-start:before {
            content: "\f04b";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
        }
        .btn-stop {
            background-color: var(--primary);
        }
        .btn-stop:hover {
            background-color: var(--primary-dark);
        }
        .btn-stop:before {
            content: "\f04d";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
        }
        .btn-restart {
            background-color: var(--warning);
            color: #1e293b;
        }
        .btn-restart:hover {
            background-color: #d97706;
        }
        .btn-restart:before {
            content: "\f2f1";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
        }
        .btn-terminal {
            background-color: #6b7280;
        }
        .btn-terminal:hover {
            background-color: #4b5563;
        }
        .btn-terminal:before {
            content: "\f120";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
        }
        .btn-delete {
            background-color: var(--danger);
        }
        .btn-delete:hover {
            background-color: #dc2626;
        }
        .btn-delete:before {
            content: "\f2ed";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
        }
        .empty-state {
            text-align: center;
            padding: 3rem;
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            margin: 2rem 0;
            box-shadow: var(--shadow);
        }
        .empty-state i {
            font-size: 4rem;
            color: var(--text-light);
            opacity: 0.5;
            margin-bottom: 1rem;
        }
        .empty-state h3 {
            font-size: 1.5rem;
            font-weight: 500;
            color: var(--text-light);
            margin-bottom: 1rem;
        }
        .empty-state p {
            color: var(--text-light);
            opacity: 0.8;
        }
        footer {
            background-color: var(--card-bg);
            color: var(--text-light);
            font-size: 0.875rem;
            text-align: center;
            padding: 1.5rem 1rem;
            margin-top: auto;
        }
        footer a {
            color: var(--primary);
            text-decoration: none;
            transition: var(--transition);
        }
        footer a:hover {
            color: var(--secondary);
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .counters {
                grid-template-columns: 1fr;
            }
            .container-grid {
                grid-template-columns: 1fr;
            }
            .container-actions {
                flex-direction: column;
            }
            button {
                width: 100%;
                justify-content: center;
            }
            form {
                width: 100%;
            }
        }
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        
        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        .modal-container {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            width: 85%;
            max-width: 1000px;
            max-height: 85vh;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            display: flex;
            flex-direction: column;
            transform: translateY(20px);
            transition: transform 0.3s ease;
        }
        
        .modal-overlay.active .modal-container {
            transform: translateY(0);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            background: linear-gradient(to right, var(--primary-dark), var(--primary));
        }
        
        .modal-header h2 {
            color: white;
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .modal-header h2:before {
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
        }
        
        .modal-logs h2:before {
            content: "\f15c";
        }
        
        .modal-stats h2:before {
            content: "\f080";
        }
        
        .modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.8;
            transition: opacity 0.2s ease;
        }
        
        .modal-close:hover {
            opacity: 1;
            transform: none;
        }
        
        .modal-close:before {
            content: "\f00d";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
        }
        
        .modal-content {
            padding: 1.5rem;
            overflow-y: auto;
            flex: 1;
        }
        
        /* Estilo para logs */
        .logs-container {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: var(--border-radius);
            padding: 1rem;
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
            white-space: pre-wrap;
            color: #e2e8f0;
            font-size: 0.875rem;
            line-height: 1.6;
            max-height: 50vh;
            overflow-y: auto;
        }
        
        .logs-container::-webkit-scrollbar {
            width: 8px;
        }
        
        .logs-container::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 4px;
        }
        
        .logs-container::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 4px;
        }
        
        .logs-controls {
            margin-top: 1rem;
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
        }
        
        .logs-refresh, .logs-download {
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background-color: var(--primary);
            color: white;
            border: none;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .logs-refresh:hover, .logs-download:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }
        
        .logs-refresh:before {
            content: "\f2f1";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
        }
        
        .logs-download:before {
            content: "\f019";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
        }
        
        /* Estilo para estadísticas */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .stat-card {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: var(--border-radius);
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            transition: var(--transition);
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
        }
        
        .stat-title {
            font-size: 0.875rem;
            color: var(--text-light);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .stat-title:before {
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            color: var(--primary);
        }
        
        .stat-cpu:before {
            content: "\f2db";
        }
        
        .stat-ram:before {
            content: "\f538";
        }
        
        .stat-network:before {
            content: "\f0ec";
        }
        
        .stat-disk:before {
            content: "\f0a0";
        }
        
        .stat-value {
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .stat-subtitle {
            font-size: 0.75rem;
            color: var(--text-light);
            opacity: 0.7;
            margin-top: 0.25rem;
        }
        
        .progress-bar {
            height: 8px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            margin-top: 0.75rem;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.5s ease;
        }
        
        .progress-cpu {
            background-color: var(--primary);
        }
        
        .progress-ram {
            background-color: var(--warning);
        }
        
        .progress-network {
            background-color: var(--success);
        }
        
        .progress-disk {
            background-color: #8b5cf6;
        }
        
        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            font-size: 0.875rem;
        }
        
        .stats-table th, .stats-table td {
            padding: 0.75rem 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .stats-table th {
            font-weight: 500;
            color: var(--text-light);
            background-color: rgba(0, 0, 0, 0.2);
        }
        
        .stats-controls {
            margin-top: 1rem;
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
        }
        
        .stats-refresh {
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background-color: var(--primary);
            color: white;
            border: none;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .stats-refresh:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }
        
        .stats-refresh:before {
            content: "\f2f1";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
        }
        
        /* Mejora en los botones de logs y stats */
        .btn-logs {
            background-color: #4b5563;
            color: var(--text);
            text-decoration: none;
            padding: 0.625rem 1rem;
            border-radius: var(--border-radius);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            justify-content: center;
            font-size: 0.875rem;
            font-family: inherit;
            border: none;
        }
        
        .btn-logs:hover {
            background-color: #374151;
            transform: translateY(-2px);
        }
        
        .btn-logs:before {
            content: "\f15c";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
        }
        
        .btn-stats {
            background-color: #8b5cf6;
            color: var(--text);
            text-decoration: none;
            padding: 0.625rem 1rem;
            border-radius: var(--border-radius);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            justify-content: center;
            font-size: 0.875rem;
            font-family: inherit;
            border: none;
        }
        
        .btn-stats:hover {
            background-color: #7c3aed;
            transform: translateY(-2px);
        }
        
        .btn-stats:before {
            content: "\f080";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
        }
        
        @media (max-width: 768px) {
            .btn-logs, .btn-stats {
                width: 100%;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .modal-container {
                width: 95%;
                max-height: 90vh;
            }
        }
    </style>
    <script>
        function confirmDelete(event, containerName, form) {
            event.preventDefault();
            if (confirm("¿Está seguro de que desea eliminar el contenedor " + containerName + "?")) {
                form.submit();
            }
        }
    </script>
</head>
<body>
    <header>
        <nav class="container">
            <h1>Tablero Docker</h1>
            <a href="http://tablero.local/oldVersion">Versión antigua</a>
        </nav>
    </header>
    <main>
        <div class="container">
            <div class="dashboard-summary">
                <h2>Resumen de Contenedores</h2>
                <?php if ($totalContenedores === 0): ?>
                    <div class="empty-state">
                        <i class="fas fa-docker"></i>
                        <h3>NO HAY CONTENEDORES DESPLEGADOS</h3>
                    </div>
                <?php else: ?>
                    <div class="counters">
                        <div class="counter-item">
                            <h3>En ejecución</h3>
                            <div class="count"><?php echo htmlspecialchars($encendidos); ?></div>
                        </div>
                        <div class="counter-item">
                            <h3>Apagados</h3>
                            <div class="count"><?php echo htmlspecialchars($apagados); ?></div>
                        </div>
                        <div class="counter-item">
                            <h3>Total</h3>
                            <div class="count"><?php echo htmlspecialchars($totalContenedores); ?></div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (empty($containers)): ?>
                <div class="empty-state">
                    <i class="fas fa-cubes"></i>
                    <h3>NO HAY CONTENEDORES DESPLEGADOS</h3>
                    <p>No hay contenedores disponibles en este momento</p>
                </div>
            <?php else: ?>
            <div class="container-grid">
                <?php foreach ($containers as $container): 
                    $processedName = processContainerName($container['Names'][0]);
                    $containerId   = $container['Id'];
                    $containerInfo  = getContainerInfo($containerId);
                    $networks = $containerInfo['NetworkSettings']['Networks'] ?? [];
                    $ips = [];
                    foreach ($networks as $networkName => $networkSettings) {
                        if (!empty($networkSettings['IPAddress'])) {
                            $ips[] = $networkSettings['IPAddress'];
                        }
                    }
                    $containerIP = count($ips) > 0 ? implode(', ', $ips) : 'Sin IP';
                    $containerState = $containerInfo['State']['Status'] ?? 'desconocido';
                    $isRunning = strpos($container['Status'], 'Up') !== false;
                ?>
                    <div class="container-card">
                        <div class="container-header">
                            <?php if (strpos($processedName, '_db_') === false && strpos($processedName, '_server') === false): ?>
                                <?php if ($processedName === 'http3'): ?>
                                    <h2>
                                        <a href="https://<?php echo htmlspecialchars($processedName); ?>.local" target="_blank">
                                            <?php echo htmlspecialchars($container['Names'][0]); ?>
                                        </a>
                                    </h2>
                                <?php else: ?>
                                    <h2>
                                        <a href="http://<?php echo htmlspecialchars($processedName); ?>.local" target="_blank">
                                            <?php echo htmlspecialchars($container['Names'][0]); ?>
                                        </a>
                                    </h2>
                                <?php endif; ?>
                            <?php else: ?>
                                <h2><?php echo htmlspecialchars($container['Names'][0]); ?></h2>
                            <?php endif; ?>
                        </div>
                        <div class="container-status <?php echo $isRunning ? 'status-running' : 'status-stopped'; ?>">
                            <?php echo $isRunning ? 'En ejecución' : 'Detenido'; ?>
                        </div>
                        <?php if ($containerState !== 'exited' && $containerState !== 'stopped'): ?>
                        <div class="container-details">
                            <p>IP(s): <?php echo htmlspecialchars($containerIP); ?></p>
                        </div>
                        <?php endif; ?>
                        <div class="container-actions">
                            <?php if ($isRunning): ?>
                                <form method="POST">
                                    <input type="hidden" name="action" value="stop">
                                    <input type="hidden" name="container_id" value="<?php echo htmlspecialchars($containerId); ?>">
                                    <button class="btn-stop" type="submit">Detener</button>
                                </form>
                                <form method="POST">
                                    <input type="hidden" name="action" value="restart">
                                    <input type="hidden" name="container_id" value="<?php echo htmlspecialchars($containerId); ?>">
                                    <button class="btn-restart" type="submit">Reiniciar</button>
                                </form>
                                <form method="GET" action="terminal.php" target="_blank">
                                    <input type="hidden" name="container" value="<?php echo htmlspecialchars($containerId); ?>">
                                    <button class="btn-terminal" type="submit">Terminal</button>
                                </form>
                                <a href="logs.php?container_id=<?php echo htmlspecialchars($containerId); ?>" class="btn-logs">Logs</a>
                                <a href="stats.php?container_id=<?php echo htmlspecialchars($containerId); ?>" class="btn-stats">Estadísticas</a>
                            <?php else: ?>
                                <form method="POST">
                                    <input type="hidden" name="action" value="start">
                                    <input type="hidden" name="container_id" value="<?php echo htmlspecialchars($containerId); ?>">
                                    <button class="btn-start" type="submit">Iniciar</button>
                                </form>
                                <form method="POST">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="container_id" value="<?php echo htmlspecialchars($containerId); ?>">
                                    <button class="btn-delete" type="submit" onclick="confirmDelete(event, '<?php echo htmlspecialchars($container['Names'][0]); ?>', this.form)">
                                        Eliminar
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </main>
    <!-- Modales para logs y stats -->
    <div id="logsModal" class="modal-overlay">
        <div class="modal-container modal-logs">
            <div class="modal-header">
                <h2>Logs del Contenedor</h2>
                <button class="modal-close" onclick="closeModal('logsModal')"></button>
            </div>
            <div class="modal-content">
                <div id="logsContent" class="logs-container">
                    Cargando logs...
                </div>
                <div class="logs-controls">
                    <button class="logs-refresh" onclick="refreshLogs()">Actualizar</button>
                    <button class="logs-download" onclick="downloadLogs()">Descargar</button>
                </div>
            </div>
        </div>
    </div>

    <div id="statsModal" class="modal-overlay">
        <div class="modal-container modal-stats">
            <div class="modal-header">
                <h2>Estadísticas del Contenedor</h2>
                <button class="modal-close" onclick="closeModal('statsModal')"></button>
            </div>
            <div class="modal-content">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-title stat-cpu">CPU</div>
                        <div id="cpuValue" class="stat-value">0%</div>
                        <div id="cpuSubtitle" class="stat-subtitle">0/0 cores</div>
                        <div class="progress-bar">
                            <div id="cpuProgress" class="progress-fill progress-cpu" style="width: 0%"></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-title stat-ram">Memoria</div>
                        <div id="memValue" class="stat-value">0 MB</div>
                        <div id="memSubtitle" class="stat-subtitle">0 MB / 0 MB</div>
                        <div class="progress-bar">
                            <div id="memProgress" class="progress-fill progress-ram" style="width: 0%"></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-title stat-network">Red</div>
                        <div id="netValue" class="stat-value">0 Bytes</div>
                        <div id="netSubtitle" class="stat-subtitle">↑ 0 Bytes / ↓ 0 Bytes</div>
                        <div class="progress-bar">
                            <div id="netProgress" class="progress-fill progress-network" style="width: 0%"></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-title stat-disk">Almacenamiento</div>
                        <div id="diskValue" class="stat-value">0 MB</div>
                        <div id="diskSubtitle" class="stat-subtitle">0 MB / 0 MB</div>
                        <div class="progress-bar">
                            <div id="diskProgress" class="progress-fill progress-disk" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
                
                <h3>Información detallada</h3>
                <table class="stats-table" id="detailedStats">
                    <thead>
                        <tr>
                            <th>Métrica</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Estado</td>
                            <td id="containerStatus">-</td>
                        </tr>
                        <tr>
                            <td>Tiempo de actividad</td>
                            <td id="uptime">-</td>
                        </tr>
                        <tr>
                            <td>ID</td>
                            <td id="containerId">-</td>
                        </tr>
                        <tr>
                            <td>Imagen</td>
                            <td id="imageInfo">-</td>
                        </tr>
                        <tr>
                            <td>Núcleos de CPU</td>
                            <td id="cpuCores">-</td>
                        </tr>
                        <tr>
                            <td>Memoria total</td>
                            <td id="memoryTotal">-</td>
                        </tr>
                        <tr>
                            <td>Red (RX/TX)</td>
                            <td id="networkStats">-</td>
                        </tr>
                        <tr>
                            <td>Procesos</td>
                            <td id="processCount">-</td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="stats-controls">
                    <button class="stats-refresh" onclick="refreshStats()">Actualizar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts para manejar los modales y la API -->
    <script>
        // Funciones para manejar los modales
        let statsInterval = null;

        function openModal(modalId, containerId) {
            const modal = document.getElementById(modalId);
            // Actualiza el containerId antes de cargar los logs
            modal.setAttribute('data-container-id', containerId);
            
            if (modalId === 'logsModal') {
                // Limpia el contenido previo y muestra mensaje de carga
                document.getElementById('logsContent').innerHTML = 'Cargando logs...';
                // Llama a fetchLogs con el nuevo containerId
                fetchLogs(containerId);
            } else if (modalId === 'statsModal') {
                fetchStats(containerId);
            }
            
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
            
            if (modalId === 'logsModal') {
                // Limpia el contenido para evitar que se muestren logs antiguos
                document.getElementById('logsContent').innerHTML = '';
            }
        }
        
        // Cerrar modales al hacer clic fuera del contenido
        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.addEventListener('click', event => {
                if (event.target === modal) {
                    closeModal(modal.id);
                }
            });
        });
        
        // Cerrar modales con la tecla Escape
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') {
                document.querySelectorAll('.modal-overlay').forEach(modal => {
                    if (modal.classList.contains('active')) {
                        closeModal(modal.id);
                    }
                });
            }
        });
        
        // Funciones para obtener datos de la API
        async function fetchLogs(containerId) {
            const logsContent = document.getElementById('logsContent');
            logsContent.innerHTML = 'Cargando logs...';
            
            try {
                const response = await fetch(`api.php?action=logs&container_id=${containerId}`);
                if (!response.ok) throw new Error('Error al obtener logs');
                
                let logs = await response.text();
                // Formatear los logs para una mejor visualización
                logs = logs.replace(/\n/g, '<br>');
                
                // Aplicar colores para errores y advertencias
                logs = logs.replace(/ERROR|Error|error/g, '<span style="color: #ef4444;">$&</span>');
                logs = logs.replace(/WARN|Warning|warning|WARNING/g, '<span style="color: #f59e0b;">$&</span>');
                logs = logs.replace(/INFO|Info|info/g, '<span style="color: #3b82f6;">$&</span>');
                
                logsContent.innerHTML = logs || 'No hay logs disponibles';
                
                // Hacer scroll automático al final
                logsContent.scrollTop = logsContent.scrollHeight;
            } catch (error) {
                logsContent.innerHTML = `Error: ${error.message}`;
            }
        }
        
        function refreshLogs() {
            const containerId = document.getElementById('logsModal').getAttribute('data-container-id');
            if (containerId) {
                fetchLogs(containerId);
            }
        }
        
        function downloadLogs() {
            const containerId = document.getElementById('logsModal').getAttribute('data-container-id');
            if (!containerId) return;
            
            fetch(`api.php?action=logs&container_id=${containerId}`)
                .then(response => response.text())
                .then(logs => {
                    // Crear un blob y un enlace para descarga
                    const blob = new Blob([logs], { type: 'text/plain' });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    a.download = `container_${containerId}_logs.txt`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                })
                .catch(error => console.error('Error al descargar logs:', error));
        }
        
        async function fetchStats(containerId) {
            try {
                const response = await fetch(`api.php?action=stats&container_id=${containerId}`);
                if (!response.ok) throw new Error('Error al obtener estadísticas');
                
                const stats = await response.json();
                updateStats(stats);
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('cpuValue').textContent = 'Error';
            }
        }
        
        function refreshStats() {
            const containerId = document.getElementById('statsModal').getAttribute('data-container-id');
            if (containerId) {
                // Actualización manual: solo llamamos a fetchStats una vez
                fetchStats(containerId);
            }
        }
        
        function formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }
        
        function formatUptime(seconds) {
            const days = Math.floor(seconds / (3600 * 24));
            const hours = Math.floor((seconds % (3600 * 24)) / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const remainingSeconds = Math.floor(seconds % 60);
            
            let result = '';
            if (days > 0) result += `${days}d `;
            if (hours > 0 || days > 0) result += `${hours}h `;
            if (minutes > 0 || hours > 0 || days > 0) result += `${minutes}m `;
            result += `${remainingSeconds}s`;
            
            return result;
        }
        
        function updateStats(stats) {
            // CPU
            const cpuPercent = stats.cpu_percent || 0;
            document.getElementById('cpuValue').textContent = `${cpuPercent.toFixed(1)}%`;
            document.getElementById('cpuSubtitle').textContent = `${stats.cpu_online_cpus || 0} núcleos`;
            document.getElementById('cpuProgress').style.width = `${Math.min(cpuPercent, 100)}%`;
            
            // Memoria
            const memUsage = stats.memory_usage || 0;
            const memLimit = stats.memory_limit || 1;
            const memPercent = (memUsage / memLimit) * 100;
            
            document.getElementById('memValue').textContent = formatBytes(memUsage);
            document.getElementById('memSubtitle').textContent = `${formatBytes(memUsage)} / ${formatBytes(memLimit)}`;
            document.getElementById('memProgress').style.width = `${Math.min(memPercent, 100)}%`;
            
            // Red
            const netRx = stats.network_rx_bytes || 0;
            const netTx = stats.network_tx_bytes || 0;
            const netTotal = netRx + netTx;
            
            document.getElementById('netValue').textContent = formatBytes(netTotal);
            document.getElementById('netSubtitle').textContent = `↓ ${formatBytes(netRx)} / ↑ ${formatBytes(netTx)}`;
            document.getElementById('netProgress').style.width = '60%'; // Valor ilustrativo
            
            // Disco
            const diskRead = stats.blkio_read || 0;
            const diskWrite = stats.blkio_write || 0;
            const diskTotal = diskRead + diskWrite;
            
            document.getElementById('diskValue').textContent = formatBytes(diskTotal);
            document.getElementById('diskSubtitle').textContent = `↓ ${formatBytes(diskRead)} / ↑ ${formatBytes(diskWrite)}`;
            document.getElementById('diskProgress').style.width = '40%'; // Valor ilustrativo
            
            // Tabla de información detallada
            document.getElementById('containerStatus').textContent = stats.status || '-';
            document.getElementById('uptime').textContent = formatUptime(stats.uptime || 0);
            document.getElementById('containerId').textContent = stats.id || '-';
            document.getElementById('imageInfo').textContent = stats.image || '-';
            document.getElementById('cpuCores').textContent = `${stats.cpu_online_cpus || 0} de ${stats.cpu_online_cpus || 0}`;
            document.getElementById('memoryTotal').textContent = formatBytes(stats.memory_limit || 0);
            document.getElementById('networkStats').textContent = `↓ ${formatBytes(stats.network_rx_bytes || 0)} / ↑ ${formatBytes(stats.network_tx_bytes || 0)}`;
            document.getElementById('processCount').textContent = stats.pids || 0;
        }
        
        // Reemplazar los enlaces de logs y stats con funciones para abrir modales
        document.addEventListener('DOMContentLoaded', function() {
            // Reemplazar todos los enlaces a logs.php y stats.php con botones de modal
            document.querySelectorAll('a[href*="logs.php"]').forEach(link => {
                const containerId = new URL(link.href).searchParams.get('container_id');
                const button = document.createElement('button');
                button.className = 'btn-logs';
                button.textContent = 'Logs';
                button.onclick = function(e) {
                    e.preventDefault();
                    openModal('logsModal', containerId);
                };
                link.parentNode.replaceChild(button, link);
            });
            
            document.querySelectorAll('a[href*="stats.php"]').forEach(link => {
                const containerId = new URL(link.href).searchParams.get('container_id');
                const button = document.createElement('button');
                button.className = 'btn-stats';
                button.textContent = 'Stats';
                button.onclick = function(e) {
                    e.preventDefault();
                    openModal('statsModal', containerId);
                };
                link.parentNode.replaceChild(button, link);
            });
        });
    </script>
    <footer>
        <p xmlns:cc="http://creativecommons.org/ns/" xmlns:dct="http://purl.org/dc/terms/">
            <a property="dct:title" rel="cc:attributionURL" href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev">WebVulnLab</a> by
            <a rel="cc:attributionURL dct:creator" property="cc:attributionName" href="https://github.com/sil3ntH4ck3r">sil3nth4ck3r</a>
            is licensed under 
            <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1" target="_blank" rel="license noopener noreferrer">
                CC BY-NC-SA 4.0
            </a>
        </p>
    </footer>
</body>
</html>
