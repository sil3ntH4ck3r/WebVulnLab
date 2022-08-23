<!DOCTYPE html>
<head>
	<html>

	</html>
	<body class="u-body">
  
  		<?php
			error_reporting(0);
    		$file=$_GET["show"];
    		//$file=preg_replace('/[^a-zA-Z0-9_]/' , '' , $file);
			//include($file);

			if(isset($file)){
       			include("/var/www/html/" . $file);
   			}
   			else
   			{
       			include("include.php");
   			}
  		?>
  	</body>
</head>