<!DOCTYPE html>
<head>
	<html>

	</html>
	<body class="u-body">
  
  		<?php

			header('Content-Type: text/html; charset=UTF-8');
			error_reporting(0);
    		$file=$_GET["show"];
    		//$file=preg_replace('/[^a-zA-Z0-9_]/' , '' , $file);
			//include($file);

			if($file != "include.php" && $file != "contact.php" && $file != "about.php" && $file != "inicio.php" && $file != "setup.php" && $file != "login.php" && $file != "logout.php"){
				include("404.php");
				exit;
   			}
			else
			{
				include($file);
			}

  		?>
  	</body>
</head>
