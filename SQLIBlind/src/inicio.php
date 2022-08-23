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
	<title><?php print "Blind SQL Injection - Error Based";?></title>

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
		<h1>Inicio Sesion</h1>
	</div>


	<div class="container">
		<ul class="nav nav-pills">
	  	<li class="nav-item">
	    	<a class="nav-link <?php if ($CURRENT_PAGE == "Index") {?>active<?php }?>" href="http://localhost:8006/?show=include.php">Inicio</a>
	  	</li>
	  	<li class="nav-item">
	    	<a class="nav-link <?php if ($CURRENT_PAGE == "About") {?>active<?php }?>" href="http://localhost:8006/?show=about.php">Sobre nosotros</a>
	  	</li>
	  	<li class="nav-item">
	    	<a class="nav-link <?php if ($CURRENT_PAGE == "Contact") {?>active<?php }?>" href="http://localhost:8006/?show=contact.php">Contactenos</a>
	  	</li>

		  <?php
                    session_start();

                    if(!$_SESSION['user'])
                    {
                ?>
                <li class="nav-item"> <a class="nav-link <?php if ($CURRENT_PAGE == "Login") {?>active<?php }?>" href="http://localhost:8006/?show=inicio.php">Iniciar Sesion</a> </li>
                <?php 
                    }                
				?>

		<li class="nav-item"> 
			<a class="nav-link <?php if ($CURRENT_PAGE == "Setup") {?>active<?php }?>" href="http://localhost:8006/setup/database.php">Configurar Base de Datos</a> 
		</li>
		  <?php
                    session_start();

                    if($_SESSION['user'])
                    {
                ?>
                <li class="nav-item"> <a class="nav-link <?php if ($CURRENT_PAGE == "Logout") {?>active<?php }?>" href="http://localhost:8006/?show=logout.php">Cerrar Sesion</a> </li>
                <?php 
                    }                
				?>
				<?php
                    session_start();

                    if($_SESSION['user'])
                    {
                ?>
                <li class="nav-item"> <a class="nav-link <?php if ($CURRENT_PAGE == "Logout") {?>active<?php }?>" href="http://localhost:8006/vuln/sqli.php">Cafes</a> </li>
                <?php 
                    }                
				?>
		</ul>
	</div>

	<div class="container" id="main-content">
		<form class="form" method="POST" id="formLogin" action="/login.php">
			<h2>Inicio Sesion</h2>
			<style>
				p { margin: 0; }
			</style>
			<?php if($_SESSION['user']=="admin" | $_SESSION['user']=="invitado") { ?>
						<?php $logedInUsername = $_SESSION['user'];?>
						<span class="text text-danger"><b><?php echo "Has iniciado session como: $logedInUsername"; ?></b></span>
				<?php }
				else{ ?> <span class="text text-danger"><b><?php echo "Has de iniciar sesion"; ?></b></span> <?php } ?>
			<p>Por motivos de desarollo, hemos habititado una cuenta accesible para todo el mundo. </p><br>
			<p>Usuario: invitado</p>
			<p>Contraseña: invitado</p><br>
			<p>Si esta es la primera que inicias sesion en este sitio web, tienes que configurar la base de datos</p>
			<input name="username" id="username" class="form-control" placeholder="Usuario" type="text"><br>
			<input name="password" id="password" class="form-control" placeholder="Contraseña" type="password"><br><br>
			<button type="submit" id="btnLogin" class="btn btn-primary pull-right">Iniciar Sesion</button><br>
		</form>
	<div class="footer">
		&copy; <?php print date("Y");?>
	</div>

</body>
</html>