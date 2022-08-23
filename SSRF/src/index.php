<!DOCTYPE html>
<head>
	<html>

	</html>
	<body class="u-body">
  
  		<?php
			//error_reporting(0);
    		$file=$_GET["show"];
    		//$file=preg_replace('/[^a-zA-Z0-9_]/' , '' , $file);
			//include($file);

			if($file != "include.php" && $file != "contact.php" && $file != "about.php"){
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