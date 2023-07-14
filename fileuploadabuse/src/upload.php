<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    $resume = $_FILES['resume'];

    // Lista negra de extensiones
    $blacklist = array('php', 'php2', 'php3', 'php4', 'php5', 'php6', 'php7', 'phps', 'pht', 'phtm', 'phtml', 'pgif', 'shtml', 'phar', 'inc', 'hphp', 'ctp', 'module');

    if (!empty($name) && !empty($email) && !empty($message) && !empty($resume)) {
        // Verificar la extensión del archivo
        $file_extension = strtolower(pathinfo($resume['name'], PATHINFO_EXTENSION));
        if (in_array($file_extension, $blacklist)) {
            echo '<script>alert("Solo se permite PDF.");</script>';
        } else {
            // Verificar los primeros bytes del archivo
            $handle = fopen($resume['tmp_name'], 'rb');
            $magic_bytes = fread($handle, 8);
            fclose($handle);

            $mime_type = $resume['type'];
            if ($mime_type == 'application/pdf') {
                if (strpos($magic_bytes, '<?php') !== false || strpos($magic_bytes, '<?=') !== false) {
                    echo "<script>alert('No se permiten CV en PHP.');</script>";
                } else {
                    $upload_dir = 'pdf/';
                    $upload_file = $upload_dir . basename($resume['name']);
                    $upload_success = move_uploaded_file($resume['tmp_name'], $upload_file);
    
                    if ($upload_success) {
                        echo "<script>alert('El CV se ha subido correctamente. En un periodo de 5-10 días obtendrás respuesta');</script>";
                    } else {
                        echo "<script>alert('Error al subir el CV.');</script>";
                    }
                }
            } else {
                echo "<script>alert('No se permiten CV con el tipo de contenido $mime_type.');</script>";
            }
        }
    } else {
        echo "<script>alert('Por favor, complete todos los campos y seleccione un CV.');</script>";
    }
}
?>

<!-- Redirigir a la página principal después de mostrar la alerta -->
<script>window.location.href = 'index.php';</script>