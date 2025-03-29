<?php
// api.php - Punto de entrada para solicitudes AJAX
header('Content-Type: application/json; charset=utf-8');

/**
 * URL base para la API de Docker
 */
define('DOCKER_API_URL', 'http://localhost:2375');

/**
 * Función para llamar a la API de Docker.
 */
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

/**
 * Función para enviar un error en formato JSON y detener la ejecución.
 */
function output_error($message) {
    echo json_encode(['error' => $message]);
    exit;
}

// Verificar que se ha enviado una acción
if (!isset($_GET['action'])) {
    output_error('No se especificó ninguna acción');
}

// Verificar que se ha enviado un ID de contenedor
if (!isset($_GET['container_id'])) {
    output_error('No se especificó ningún ID de contenedor');
}

$containerId = $_GET['container_id'];
$action = $_GET['action'];

try {
    switch ($action) {
        case 'logs':
            // Para logs usaremos texto plano
            header('Content-Type: text/plain; charset=utf-8');
            echo get_container_logs($containerId);
            break;
            
        case 'stats':
            $stats = get_container_stats($containerId);
            echo json_encode($stats);
            break;
            
        default:
            output_error('Acción no válida');
    }
} catch (Exception $e) {
    output_error($e->getMessage());
}

/**
 * Obtiene los logs del contenedor especificado.
 * Se solicita la salida estándar y de error, las últimas 500 líneas y se incluyen marcas de tiempo.
 * Luego se limpia la salida de caracteres de control.
 */
function get_container_logs($containerId) {
    $logs = dockerApiCall("/containers/$containerId/logs?stdout=true&stderr=true&tail=500&timestamps=true");
    // Limpiar caracteres de control (por ejemplo, secuencias no imprimibles)
    $logs = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x80-\xFF]/', '', $logs);
    return $logs;
}

/**
 * Obtiene las estadísticas del contenedor usando el endpoint con stream=false.
 */
function get_container_stats($containerId) {
    $stats = dockerApiCall("/containers/$containerId/stats?stream=false");
    if (!is_array($stats)) {
        throw new Exception("Respuesta inesperada en stats");
    }
    
    // Calcular porcentaje de CPU
    $cpuStats = $stats['cpu_stats'] ?? [];
    $preCpuStats = $stats['precpu_stats'] ?? [];
    $onlineCpus = isset($cpuStats['online_cpus']) ? $cpuStats['online_cpus'] : 0;
    $cpuDelta = isset($cpuStats['cpu_usage']['total_usage'], $preCpuStats['cpu_usage']['total_usage']) 
                ? ($cpuStats['cpu_usage']['total_usage'] - $preCpuStats['cpu_usage']['total_usage'])
                : 0;
    $systemDelta = isset($cpuStats['system_cpu_usage'], $preCpuStats['system_cpu_usage'])
                ? ($cpuStats['system_cpu_usage'] - $preCpuStats['system_cpu_usage'])
                : 0;
    if ($systemDelta > 0 && $cpuDelta > 0 && $onlineCpus > 0) {
        $cpuPercent = ($cpuDelta / $systemDelta) * $onlineCpus * 100;
    } else {
        $cpuPercent = 0;
    }
    
    // Memoria
    $memUsage = $stats['memory_stats']['usage'] ?? 0;
    $memLimit = $stats['memory_stats']['limit'] ?? 1;
    
    // Red: sumar datos de todas las interfaces
    $netRx = 0;
    $netTx = 0;
    if (isset($stats['networks']) && is_array($stats['networks'])) {
        foreach ($stats['networks'] as $iface) {
            $netRx += $iface['rx_bytes'] ?? 0;
            $netTx += $iface['tx_bytes'] ?? 0;
        }
    }
    
    // Disco: sumar lecturas y escrituras de blkio (si están disponibles)
    $diskRead = 0;
    $diskWrite = 0;
    if (isset($stats['blkio_stats']['io_service_bytes_recursive']) && is_array($stats['blkio_stats']['io_service_bytes_recursive'])) {
        foreach ($stats['blkio_stats']['io_service_bytes_recursive'] as $entry) {
            if (isset($entry['op'])) {
                if (strtolower($entry['op']) === 'read') {
                    $diskRead += $entry['value'];
                } elseif (strtolower($entry['op']) === 'write') {
                    $diskWrite += $entry['value'];
                }
            }
        }
    }
    
    // PIDs
    $pids = $stats['pids_stats']['current'] ?? 0;
    
    // Uptime: La API no lo provee directamente, se podría calcular si se obtiene 'read' y 'preread',
    // pero por ahora lo dejamos en 0 o se podría omitir.
    $uptime = 0;
    
    // Normalizar y devolver los datos
    return [
        'cpu_percent' => $cpuPercent,
        'cpu_online_cpus' => $onlineCpus,
        'memory_usage' => $memUsage,
        'memory_limit' => $memLimit,
        'network_rx_bytes' => $netRx,
        'network_tx_bytes' => $netTx,
        'blkio_read' => $diskRead,
        'blkio_write' => $diskWrite,
        'pids' => $pids,
        'uptime' => $uptime,
        // Puedes incluir datos adicionales si lo deseas:
        'id' => $stats['id'] ?? null,
        'image' => $stats['name'] ?? null, // Nota: puede necesitar otro origen para la imagen
        'status' => $stats['status'] ?? null
    ];
}
?>
