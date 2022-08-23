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
		<h1>Maquinas</h1>
	</div>


	<div class="container">
        <ul class="nav nav-pills">
          <li class="nav-item">
            <a class="nav-link <?php if ($CURRENT_PAGE == "Index") {?>active<?php }?>" href="?show=include.php">Inicio</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php if ($CURRENT_PAGE == "About") {?>active<?php }?>" href="?show=maquinas.php">Maquinas</a>
          </li>
        </ul>
</div>
	<div class="container" id="main-content">
		<h2>Comprueba que maquinas estan activas en este momento!</h2>
        <?php
            $salida = shell_exec('sudo -u root docker ps');
			echo "<pre>$salida</pre>";
			exit;
        ?>
		

	<div class="footer">
		&copy; <?php print date("Y");?>
	</div>

</body>
</html>