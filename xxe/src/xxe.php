<?php
// Obtener los datos en formato XML
$xml = file_get_contents('php://input');

if (empty($xml)) {
  die('No se recibió ningún dato en formato XML');
}

// Crear un objeto DOMDocument a partir del XML recibido
$doc = new DOMDocument();
$doc->loadXML($xml);

// Obtener los valores de usuario y contraseña del objeto DOMDocument
$username = $doc->getElementsByTagName('username')->item(0)->nodeValue;
$password = $doc->getElementsByTagName('password')->item(0)->nodeValue;

// Verificar si los valores son correctos
if ($username === 'admin' && $password === 'admin') {
  echo '<mensaje>Bienvenido, ' . $username . '</mensaje>';
} else {
  echo '<mensaje>Error de inicio de sesión</mensaje>';
}
?>

