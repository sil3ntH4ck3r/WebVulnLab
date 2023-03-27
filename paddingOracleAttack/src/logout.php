<?php
    session_start();
    unset($_SESSION['user']);
    setcookie("cookieAuth", "", time() - 3600);
    session_destroy();
    header("Location: login.php"); //redirigir al usuario a la página de inicio
?>