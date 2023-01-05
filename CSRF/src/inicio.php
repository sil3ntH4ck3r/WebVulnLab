<?php
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
		<h1>Inicio Sesión</h1>
	</div>


	<div class="container">
		<ul class="nav nav-pills">
	  	<li class="nav-item">
	    	<a class="nav-link <?php if ($CURRENT_PAGE == "Index") {?>active<?php }?>" href="/?show=include.php">Inicio</a>
	  	</li>
	  	<li class="nav-item">
	    	<a class="nav-link <?php if ($CURRENT_PAGE == "About") {?>active<?php }?>" href="/?show=about.php">Sobre nosotros</a>
	  	</li>
	  	<li class="nav-item">
	    	<a class="nav-link <?php if ($CURRENT_PAGE == "Contact") {?>active<?php }?>" href="?show=contact.php">Contáctenos</a>
	  	</li>

		  <?php
                    session_start();

                    if(!$_SESSION['user'])
                    {
                ?>
                <li class="nav-item"> <a class="nav-link <?php if ($CURRENT_PAGE == "Login") {?>active<?php }?>" href="?show=inicio.php">Iniciar Sesión</a> </li>
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
                <li class="nav-item"> <a class="nav-link <?php if ($CURRENT_PAGE == "Logout") {?>active<?php }?>" href="?show=logout.php">Cerrar Sesión</a> </li>
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
		<form class="form" method="POST" id="formLogin" action="/login.php">
			<style>
				p { margin: 0; }
			</style>
			<?php session_start(); ?>
				<!--
//				echo "<h3> PHP List All Session Variables</h3>";
//				foreach ($_SESSION as $key=>$val)
//				echo $key." ".$val."<br/>";
				-->

				<?php if($_SESSION['user']=="admin" | $_SESSION['user']=="invitado") { ?>
						<?php $logedInUsername = $_SESSION['user'];?>
						<span class="text text-danger"><b><?php echo "Has iniciado session como: $logedInUsername"; ?></b></span>
				<?php }
				else{ ?> <span class="text text-danger"><b><?php echo "Has de iniciar sesión"; ?></b></span> <?php } ?>
			<p>Por motivos de desarrollo, hemos habilitado una cuenta accesible para todo el mundo. </p><br>
			<p>Usuario: invitado</p>
			<p>Contraseña: invitado</p><br>
			<p>Si esta es la primera que inicias sesión en este sitio web, tienes que configurar la base de datos</p>
			<input name="username" id="username" class="form-control" placeholder="Usuario" type="text"><br>
			<input name="password" id="password" class="form-control" placeholder="Contraseña" type="password"><br><br>
			<button type="submit" id="btnLogin" class="btn btn-primary pull-right">Iniciar Sesión</button><br>
		</form>
	<div class="footer">
		&copy; <?php print date("Y");?>
	</div>

</body>
</html>