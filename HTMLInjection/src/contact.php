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
    <title><?php print "HTML Injection";?></title>

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
</head>
<body>

<div class="jumbotron">
	<h1>Contactenos!</h1>
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
	    <a class="nav-link <?php if ($CURRENT_PAGE == "Contact") {?>active<?php }?>" href="?show=contact.php">Contactenos</a>
	  </li>
	</ul>
</div>

<div class="container" id="main-content">
	<h2>Deje su comentario!</h2>
			<div class="vulnerable_code_area">
                <form method="post">
                        <table width="550" cellpadding="2" cellspacing="1">
                                <tr>
                                        <td width="100">Nombre</td>
                                        <td><input name="txtName" type="text" size="30" maxlength="5000"></td>
                                </tr>
                                <tr>
                                        <td width="100">Mensaje</td>
                                        <td><textarea name="mtxMessage" cols="50" rows="3" maxlength="5000"></textarea></td>
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
        if(isset($_POST['btnSend'])) {

                $name=$_REQUEST['txtName'];
				$mensaje=$_REQUEST['mtxMessage'];

				if($mensaje == NULL && $name == NULL){
					echo "No puedes dejar los campos vacios";
					exit;
				}
                if($name == NULL){
                        echo "Introduzca un nombre";
						exit;
                }
				if($mensaje == NULL){
					echo "Introduzca un mensaje";
					exit;
				}
                if($mensaje && $name)
                {
                        echo 'Sr. ' . $name . ', gracias por su aprotacion';  
                }
        }
        ?>
</div>

<div class="footer">
	&copy; <?php print date("Y");?>
</div>

</body>
</html>