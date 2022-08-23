
<?php
	session_start();
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
    <title><?php print "Tablero";?></title>

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
		<h1>Tablero</h1>
	</div>


	<div class="container">
        <ul class="nav nav-pills">
          <li class="nav-item">
            <a class="nav-link <?php if ($CURRENT_PAGE == "Index") {?>active<?php }?>" href="?show=include.php">Inicio</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php if ($CURRENT_PAGE == "Maquinas") {?>active<?php }?>" href="?show=maquinas.php">Maquinas</a>
          </li>
        </ul>
</div>
	<div class="container" id="main-content">
		<h2>Enciende y apaga las maquina que desees!</h2>
		<form action="" method="post" id="this">
			Inicio<br>
			<button name="startServer" type="sumbit" class="btn btn-default">Iniciar</button>
			<button name="stopServer" type="sumbit" class="btn btn-default">Parar</button><br>
			Local File Inclusion<br>
			<button name="startLfi" type="sumbit" class="btn btn-default" >Iniciar</button> 
			<button name="stopLfi" type="sumbit" class="btn btn-default" >Parar</button><br>
			HTML Injection<br>
			<button name="startHTML" type="sumbit" class="btn btn-default">Iniciar</button>
			<button name="stopHTML" type="sumbit" class="btn btn-default">Parar</button><br>
			SQLI Blind<br>
			<button name="startSQLIBlind" type="sumbit" class="btn btn-default">Iniciar</button>
			<button name="stopSQLIBlind" type="sumbit" class="btn btn-default">Parar</button><br>
			SQL Injection<br>
			<button name="startSQLI" type="sumbit" class="btn btn-default">Iniciar</button>
			<button name="stopSQLI" type="sumbit" class="btn btn-default">Parar</button><br>
			Server Site Request Forgery<br>
			<button name="startSSRF" type="sumbit" class="btn btn-default">Iniciar</button>
			<button name="stopSSRF" type="sumbit" class="btn btn-default">Parar</button><br>
			Cross Site Request Forgery<br>
			<button name="startCSRF" type="sumbit" class="btn btn-default">Iniciar</button>
			<button name="stopCSRF" type="sumbit" class="btn btn-default">Parar</button><br>
		</form>
	<SCRIPT type=text/javascript>
		function start(){
			const start = document.getElementById("start");
			const stop = document.getElementById("stop");
			start.disabled = true;
			stop.disabled = false;
		}
		function stop(){
			const start = document.getElementById("start");
			const stop = document.getElementById("stop");
			start.disabled = false;
			stop.disabled = true;
		}
	</SCRIPT>

<?php
    //session_start();

	$numero = 2;
  
    if(isset($_POST['startLfi'])){

		$numero = 1;
		if($numero == 1){
        	$salida = shell_exec('sudo -u root docker start lfi');
			echo "<pre>Se ha iniciado el docker: $salida</pre>";
			exit;
		}
    }
    if(isset($_POST['stopLfi'])){

		$numero = 0;
		if($numero == 0){
			$salida1 = shell_exec('sudo -u root docker stop lfi');
			echo "<pre>Se ha parado el docker: $salida1</pre>";
			exit;
		}
    }


	if(isset($_POST['startServer'])){

		$numero = 1;
		if($numero == 1){
        	$salida = shell_exec('sudo -u root docker start main_server');
			echo "<pre>Se ha iniciado el docker: $salida</pre>";
			exit;
		}
    }
    if(isset($_POST['stopServer'])){

		$numero = 0;
		if($numero == 0){
			$salida1 = shell_exec('sudo -u root docker stop main_server');
			echo "<pre>Se ha parado el docker: $salida1</pre>";
			exit;
		}
    }


	if(isset($_POST['startHTML'])){

		$numero = 1;
		if($numero == 1){
        	$salida = shell_exec('sudo -u root docker start html_injection');
			echo "<pre>Se ha iniciado el docker: $salida</pre>";
			exit;
		}
    }
    if(isset($_POST['stopHTML'])){

		$numero = 0;
		if($numero == 0){
			$salida1 = shell_exec('sudo -u root docker stop html_injection');
			echo "<pre>Se ha parado el docker: $salida1</pre>";
			exit;
		}
    }


	if(isset($_POST['startSQLIBlind'])){

		$numero = 1;
		if($numero == 1){
        	$salida = shell_exec('sudo -u root docker start sqli_blind');
			echo "<pre>Se ha iniciado el docker: $salida</pre>";
			exit;
		}
    }
    if(isset($_POST['stopSQLIBlind'])){

		$numero = 0;
		if($numero == 0){
			$salida1 = shell_exec('sudo -u root docker stop sqli_blind');
			echo "<pre>Se ha parado el docker: $salida1</pre>";
			exit;
		}
    }


	if(isset($_POST['startSQLI'])){

		$numero = 1;
		if($numero == 1){
        	$salida = shell_exec('sudo -u root docker start sqli');
			echo "<pre>Se ha iniciado el docker: $salida</pre>";
			exit;
		}
    }
    if(isset($_POST['stopSQLI'])){

		$numero = 0;
		if($numero == 0){
			$salida1 = shell_exec('sudo -u root docker stop sqli');
			echo "<pre>Se ha parado el docker: $salida1</pre>";
			exit;
		}
    }


	if(isset($_POST['startSSRF'])){

		$numero = 1;
		if($numero == 1){
        	$salida = shell_exec('sudo -u root docker start ssrf');
			echo "<pre>Se ha iniciado el docker: $salida</pre>";
			exit;
		}
    }
    if(isset($_POST['stopSSRF'])){

		$numero = 0;
		if($numero == 0){
			$salida1 = shell_exec('sudo -u root docker stop ssrf');
			echo "<pre>Se ha parado el docker: $salida1</pre>";
			exit;
		}
    }
	

	if(isset($_POST['startCSRF'])){

		$numero = 1;
		if($numero == 1){
        	$salida = shell_exec('sudo -u root docker start csrf');
			echo "<pre>Se ha iniciado el docker: $salida</pre>";
			exit;
		}
    }
    if(isset($_POST['stopCSRF'])){

		$numero = 0;
		if($numero == 0){
			$salida1 = shell_exec('sudo -u root docker stop csrf');
			echo "<pre>Se ha parado el docker: $salida1</pre>";
			exit;
		}
    }
?>

	<div class="footer">
		&copy; <?php print date("Y");?>
	</div>

</body>
</html>
