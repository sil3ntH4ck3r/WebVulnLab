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
    <title><?php print "SQL Injection - Error Based";?></title>

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
	    	<a class="nav-link <?php if ($CURRENT_PAGE == "Index") {?>active<?php }?>" href="http://localhost:8005/?show=include.php">Inicio</a>
	  	</li>
	  	<li class="nav-item">
	    	<a class="nav-link <?php if ($CURRENT_PAGE == "About") {?>active<?php }?>" href="http://localhost:8005/?show=about.php">Sobre nosotros</a>
	  	</li>
	  	<li class="nav-item">
	    	<a class="nav-link <?php if ($CURRENT_PAGE == "Contact") {?>active<?php }?>" href="http://localhost:8005/?show=contact.php">Contactenos</a>
	  	</li>
		  <?php
                    session_start();

                    if(!$_SESSION['user'])
                    {
                ?>
                <li class="nav-item"> <a class="nav-link <?php if ($CURRENT_PAGE == "Login") {?>active<?php }?>" href="http://localhost:8005/?show=inicio.php">Iniciar Sesion</a> </li>
                <?php 
                    }                
				?>

		<li class="nav-item"> 
			<a class="nav-link <?php if ($CURRENT_PAGE == "Setup") {?>active<?php }?>" href="http://localhost:8005/setup/database.php">Configurar Base de Datos</a> 
		</li>
		  <?php
                    session_start();

                    if($_SESSION['user'])
                    {
                ?>
                <li class="nav-item"> <a class="nav-link <?php if ($CURRENT_PAGE == "Logout") {?>active<?php }?>" href="http://localhost:8005/?show=logout.php">Cerrar Sesion</a> </li>
                <?php 
                    }                
				?>
				<?php
                    session_start();

                    if($_SESSION['user'])
                    {
                ?>
                <li class="nav-item"> <a class="nav-link <?php if ($CURRENT_PAGE == "Logout") {?>active<?php }?>" href="http://localhost:8005/vuln/sqli.php">Cafes</a> </li>
                <?php 
                    }                
				?>
		</ul>
	</div>

	<div class="container" id="main-content">
			<h2>Pagina web en desarrollo!</h2>

            <div class="well">
        <div class="col-lg-6"> 
            <p>Search by Itemcode or use search option  
                <form method='post' action=''>
                    <div class="form-group"> 
                        <label></label>
                        <select class="form-control" name="item">
                            <option value="">Select Item Code</option>
                            <?php 
                            session_start();
                            error_reporting(E_ALL);
                            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
                            ini_set('display_errors', 1);
                            include('../config.php');
                            if($conn->connect_errno > 0){
                                echo "ERROR - No se puede conectar a la base de datos";
                            }else{
                                $sql = 'select itemid from caffaine';
                                $result = $conn->query($sql);
                                while($rows = $result->fetch_assoc()) {
                                    echo "<option value=\"".$rows['itemid']."\">".$rows['itemid']."</option>";
                                }
                            } 

                            echo "</select><br>";
                            echo "<input class=\"form-control\" width=\"50%\" placeholder=\"Search\" name=\"search\"></input> <br>";
                            echo "<div align=\"right\"> <button class=\"btn btn-default\" type=\"submit\">Submit</button></div>";
                            echo "</div> </form> </p>";
                            echo "</div>";
                            $item = isset($_POST['item']) ? $_POST['item'] : '';
                            $search = isset($_POST['search']) ? $_POST['search'] : '';
                            $isSearch = false;
                            if(($item!="") && $search!=""){ 
                                echo "<br><ul class=\"featureList\">";
                                echo "<li class=\"cross\">ERROR - No se puede ambas opciones a la vez</li>";
                                echo "</ul>";
                            }else if($item){
                                $sql = "select * from caffaine where itemid = ".$item;
                                $result = $conn->query($sql);
                                $isSearch = true;
                            }else if($search){
                                $sql = "SELECT * FROM caffaine WHERE itemname LIKE '%" . $search . "%' OR itemdesc LIKE '%" . $search . "%' OR categ LIKE '%" . $search . "%'";
                                $result = $conn->query($sql);
                                $isSearch = true;
                            }
                            if($isSearch){
                                echo "<table>";
                                while($rows = $result->fetch_assoc()){
                                    echo "<tr><td><b>Codigo : </b>".$rows['itemcode']."</td><td rowspan=5>&nbsp;&nbsp;</td><td rowspan=5 valign=\"top\" align=\"justify\"><b>Descripcion : </b>".$rows['itemdesc']."</td></tr>";
                                    echo "<tr><td><b>Nombre : </b>".$rows['itemname']."</td></tr>";
                                    echo "<td><img src='".$rows['itemdisplay']."' height=130 weight=20/></td>";
                                    echo "<tr><td><b>Categoria : </b>".$rows['categ']."</td></tr>";
                                    echo "<tr><td><b>Precio : </b>".$rows['price']."$</td></tr>"; 
                                    echo "<tr><td colspan=3><hr></td></tr>";
                                }
                                echo "</table>"; 
                            }

                            ?>



                            <hr>
                            
                        </div>
                    </div>
                </div>
                
                

	<div class="footer">
		&copy; <?php print date("Y");?>
	</div>

</body>
</html>