<?php
        session_start();
        echo "ERROR - Antes de iniciar sesion, has de configurar la base de datos";
        $uname = $_POST['username'];
        $password = md5($_POST['password']);
        $ActiveUser = '';
        include_once('config.php');
        $sql = "select username from users where username=:username and password=:password";
        $stmt = $conn1->prepare($sql);
        $stmt->bindParam(':username',$uname);
        $stmt->bindParam(':password',$password);
        $stmt->execute();
        while($rows = $stmt->fetch(PDO::FETCH_NUM)){
                $ActiveUser = $rows[0];
        }
        if(!empty($ActiveUser)){
                $_SESSION['user'] = $ActiveUser;
        }
        header("Location: http://localhost:8007/?show=inicio.php");

?>
