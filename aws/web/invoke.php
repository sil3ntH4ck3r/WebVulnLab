<?php
// Obtener la IP del host localstack (asumiendo que el contenedor web puede resolver este hostname)
$lambdaHost = 'localstack';
$lambdaIp = gethostbyname($lambdaHost);

// Añadir otra cabecera con la IP del servidor lambda (localstack)
header("X-Lambda-Server-IP: $lambdaIp");

if(!isset($_GET['function'])) {
    echo "No se especificó ninguna función.";
    exit;
}

$function = escapeshellarg($_GET['function']); // Escapar el nombre para shell
$output = shell_exec("aws lambda invoke --function-name $function --region us-east-1 --endpoint-url http://localstack:4566 --query 'Payload' --cli-binary-format raw-in-base64-out --no-sign-request /dev/stdout");

echo "<pre>$output</pre>";
