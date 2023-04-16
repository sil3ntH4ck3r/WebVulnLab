<?php
// Cargar archivo XML
$xml = simplexml_load_file('users.xml');

// Obtener credenciales del formulario
$username = $_POST['username'];
$password = $_POST['password'];

// Buscar usuario en el archivo XML
$users = $xml->xpath("//user[username='$username' and password='$password']");

// Comprobar si el usuario existe
if (count($users) == 1) {
    echo "Bienvenido, $username!";
} else {
    echo "Credenciales inválidas.";
}
?>
