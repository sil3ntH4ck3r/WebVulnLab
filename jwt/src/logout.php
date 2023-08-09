<?php
    session_start();
    unset($_SESSION['user']);
    unset($_SESSION['loggedin']);
    setcookie("jwtToken", "", time() - 3600, "/"); // Remove the JWT token cookie
    session_destroy();
    header("Location: index.php"); // Redirect the user to the homepage
?>
