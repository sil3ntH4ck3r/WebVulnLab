<?php
    session_start();
    if(session_destroy()){
        $_SESSION['logged']==false;
        header("Location: http://localhost:8003/?show=inicio.php");
    }

?>