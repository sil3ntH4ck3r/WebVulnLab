<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');
switch ($_SERVER["SCRIPT_NAME"]) {
    case "about.php":
		$CURRENT_PAGE = "About"; 
		$PAGE_TITLE = "About Us";
	    break;
	case "contact.php":
		$CURRENT_PAGE = "Contact"; 
		$PAGE_TITLE = "Contact Us";
		break;
	default:
		$CURRENT_PAGE = "Index";
		$PAGE_TITLE = "Welcome to my homepage!";
	}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php print "Cross Site Request Forgery";?></title>

    <?php if ($CURRENT_PAGE == "Index") { ?>
	    <meta name="description" content="" />
	    <meta name="keywords" content="" /> 
    <?php } ?>

    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
	    #main-content {
		    margin-top:20px;
	    }
	    .footer {
		    font-size: 14px;
		    text-align: center;
	    }
    </style> 

	<SCRIPT type=text/javascript>
		function sumbit() {     
			var input = document.getElementById('userInput').value;	
		}
	</SCRIPT>

</head>
<body>

	<div class="jumbotron">
		<h1>Cambiar tu contraseña</h1>
	</div>


	<div class="container">
		<ul class="nav nav-pills">
	  	<li class="nav-item">
	    	<a class="nav-link <?php if ($CURRENT_PAGE == "Index") {?>active<?php }?>" href="http://localhost:8003/?show=include.php">Inicio</a>
	  	</li>
	  	<li class="nav-item">
	    	<a class="nav-link <?php if ($CURRENT_PAGE == "About") {?>active<?php }?>" href="http://localhost:8003/?show=about.php">Sobre nosotros</a>
	  	</li>
	  	<li class="nav-item">
	    	<a class="nav-link <?php if ($CURRENT_PAGE == "Contact") {?>active<?php }?>" href="http://localhost:8003/?show=contact.php">Contáctenos</a>
	  	</li>
		  <?php
                    session_start();

                    if(!$_SESSION['user'])
                    {
                ?>
                <li class="nav-item"> <a class="nav-link <?php if ($CURRENT_PAGE == "Login") {?>active<?php }?>" href="http://localhost:8003/?show=inicio.php">Iniciar Sesión</a> </li>
                <?php 
                    }                
				?>

		<li class="nav-item"> 
			<a class="nav-link <?php if ($CURRENT_PAGE == "Setup") {?>active<?php }?>" href="http://localhost:8003/setup/database.php">Configurar Base de Datos</a> 
		</li>
		  <?php
                    session_start();

                    if($_SESSION['user'])
                    {
                ?>
                <li class="nav-item"> <a class="nav-link <?php if ($CURRENT_PAGE == "Logout") {?>active<?php }?>" href="http://localhost:8003/?show=logout.php">Cerrar Sesión</a> </li>
                <?php 
                    }                
				?>
				<?php
                    session_start();

                    if($_SESSION['user'])
                    {
                ?>
                <li class="nav-item"> <a class="nav-link <?php if ($CURRENT_PAGE == "Logout") {?>active<?php }?>" href="http://localhost:8003/vuln/csrf.php">Cambiar Contraseña</a> </li>
                <?php 
                    }                
				?>
		</ul>
	</div>

	<div class="container" id="main-content">
			<h2>¡La página web está en desarrollo!</h2>
<div class="thumbnail">
    <div class="well">
        <div class="col-lg-6"> 
            <p><h4>Cambia la contraseña</h4>  
                <form method='get' action=''>
                    <div class="form-group"> 
                        <label></label>
                        <input type="password" class="form-control" width="50%" placeholder="Nueva contraseña" name="passwd"></input> <br>
                        <input type="password" class="form-control" width="50%" placeholder="Confirma la nueva contraseña" name="confirm"></input> <br>
                        <div align="right"> <button class="btn btn-default" type="submit" name="submit" value="submit">Cambiar</button></div>
                    </div> 
                </form>
                <?php
                session_start();
                $current_user = isset($_SESSION['user']) ? $_SESSION['user'] : '' ;
                $password = isset($_GET['passwd']) ? $_GET['passwd'] : '' ;
                $confirm = isset($_GET['confirm']) ? $_GET['confirm'] : '' ;
                include('../config.php');
                if($current_user){
                    if(isset($_GET['submit'])){
                        if(empty($password) && empty($password)){
                            echo "No puede estar en blanco, inténtalo de nuevo";
                        }else if($password != $confirm){
                            echo "Las contraseñas no coinciden";
                        }else{
                            $stmt = $conn1->prepare("UPDATE users set password=:pass where username=:user");
                            $stmt->bindParam(':pass', md5($password));
                            $stmt->bindParam(':user', $current_user);
                            $stmt->execute(); 
                            if($stmt->rowCount() > 0){
                                echo "<b>Cambio de contraseña correcto<br></b>";
                            }else{
                                echo "<b>Usuario inválido<br></b>";
                            }
                        }
                    }
                }else{
                    echo "<b> Has de iniciar sesión </b>";
                }
                ?>
            </p>
        </div>
        
        <hr>
        
    </div>

    <?php include_once('../../about.html'); ?>

	<div class="footer">
		&copy; <?php print date("Y");?>
	</div>

</body>
</html>