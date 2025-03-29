<?php
    $containerId = $_GET['container'] ?? '';
    if (!$containerId) {
        die("No container ID");
    }

    $port = mt_rand(10000, 11000);

    $containerIdEscaped = escapeshellarg($containerId);

    $cmd = "ttyd --port {$port} --writable --once sudo /usr/local/bin/docker_exec_wrapper.sh {$containerIdEscaped} /bin/bash > /dev/null 2>&1 &";
    exec($cmd);

    sleep(2);

    header("Location: http://localhost:{$port}");
    exit;
?>