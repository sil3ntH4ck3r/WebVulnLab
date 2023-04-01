<?php
    session_start();
    unset($_SESSION['user']);
    unset($_SESSION['loggedin']);
    setcookie("cookieAuth", "", time() - 3600);
    session_destroy();
    header("Location: index.php"); //redirigir al usuario a la página de inicio
?>