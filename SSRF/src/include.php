
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
    <title><?php print "Server Site Request Forgery";?></title>

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
		<h1>Inicio</h1>
	</div>


	<div class="container">
		<ul class="nav nav-pills">
	  	<li class="nav-item">
	    	<a class="nav-link <?php if ($CURRENT_PAGE == "Index") {?>active<?php }?>" href="?show=include.php">Inicio</a>
	  	</li>
	  	<li class="nav-item">
	    	<a class="nav-link <?php if ($CURRENT_PAGE == "About") {?>active<?php }?>" href="?show=about.php">Sobre nosotros</a>
	  	</li>
	  	<li class="nav-item">
	    	<a class="nav-link <?php if ($CURRENT_PAGE == "Contact") {?>active<?php }?>" href="?show=contact.php">Contáctenos</a>
	  	</li>
		</ul>
	</div>

	<div class="container" id="main-content">
	<h2>¡La página web está en desarrollo!</h2>
	<p>Compruebe si la página web deseada existe:</p>
			<div class="vulnerable_code_area">
                <form method="post">
                        <table width="550" cellpadding="2" cellspacing="1">
                                <tr>
                                        <td width="100">Nombre</td>
                                        <td><input name="txtName" class="form-control" type="text" size="30" maxlength="5000"></td>
                                </tr>
                                <tr>
                                        <td width="100">&nbsp;</td>
                                        <td>
                                                <input name="btnSend" class="form-control" type="submit" value="Enviar"/>
                                                <input name="btnClear" class="form-control" type="submit" value="Limpiar"/>
                                        </td>
                                </tr>
                        </table>
						<?php
            if (isset($_REQUEST['txtName'])) {
                $target = $_REQUEST['txtName'];
                if($target){
                    //if (stristr(php_uname('s'), 'Windows NT')) { 
                    //    $cmd = shell_exec( "curl localhost" );
                    //    $text = "<".$cmd.">";
					//	highlight_string($text);

                    //} 
					//else { 
                        $cmd = shell_exec( "curl $target" );
						//highlight_string($cmd);

						if ($cmd){
							echo "Existe el sitio web introducido";
						}
						else{
							echo "El sitio web introducido no existe";
						}
                    //}
                }
            }
                
            ?>
</div>
	<div class="footer">
		&copy; <?php print date("Y");?>
	</div>

</body>
</html>