<?php
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
		<h1>Configuración</h1>
	</div>


	<div class="container">
		<ul class="nav nav-pills">
	  	<li class="nav-item">
	    	<a class="nav-link <?php if ($CURRENT_PAGE == "Index") {?>active<?php }?>" href="http://localhost:8003/?show=include.php">Inicio</a>
	  	</li>
	  	<li class="nav-item">
	    	<a class="nav-link <?php if ($CURRENT_PAGE == "About") {?>active<?php }?>" href="http://localhost:8003/?show=about.php">Sobre nosotros</a>
	  	</li>
	  	<li class="nav-item">
	    	<a class="nav-link <?php if ($CURRENT_PAGE == "Contact") {?>active<?php }?>" href="http://localhost:8003/?show=contact.php">Contáctenos</a>
	  	</li>
          <?php
                    session_start();

                    if(!$_SESSION['user'])
                    {
                ?>
                <li class="nav-item"> <a class="nav-link <?php if ($CURRENT_PAGE == "Login") {?>active<?php }?>" href="http://localhost:8003/?show=inicio.php">Iniciar Sesión</a> </li>
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
                <li class="nav-item"> <a class="nav-link <?php if ($CURRENT_PAGE == "Logout") {?>active<?php }?>" href="http://localhost:8003/?show=logout.php">Cerrar Sesión</a> </li>
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
			<h2>¡Configura la base de datos!</h2>
			<div class="thumbnail">
        <div class="col-lg-12"> 
        <p align="center"> 
            <form method='get' action=''>
                <div class="form-group" align="center"> 
                    <label></label>
                    <button class="btn btn-primary" name="action" value="do" type="submit">Configura / Reinicia</button>
               </div> 
            </form>
        </p>
    </div>
</div>
<?php
include('../config.php');
function cleanup($conn,$XVWA_WEBROOT){
    // clean the database
    $tables = array('comments','caffaine','users');
    for($i=0;$i<count($tables);$i++){
        $sql = 'DROP TABLE '. $tables[$i].';';
        $sqlexec = $conn->query($sql);
    }
    // clean extra files
    $files = glob('../img/uploads/*'); 
    foreach($files as $file){ 
        if(is_file($file)){
            unlink($file); 
        }
    }
     
}
$submit = isset($_GET['action']) ? $_GET['action'] : '';
// $submit=$_GET['action'];
 if($submit){
     echo "<div class=\"well\">";  
     echo "<ul class=\"featureList\">";
     if($conn->connect_errno > 0){
        die("<li class=\"cross\">La conexión fallo. Compruebe el archivo de configuracion.".$conn->connect_error ."</li>");
     }else{
        //connection successfull.
            
            cleanup($conn,$XVWA_WEBROOT);
            echo "<li class=\"tick\">Conectado a la base de datos con éxito.</li>";   
            // creating comment tables
            $table_comment=$conn->query('CREATE TABLE comments(id int not null primary key auto_increment,user varchar(30),comment varchar(100),date varchar(30))');
            if($table_comment){
                $insert_comment=$conn->query('INSERT INTO comments (id,user,comment,date) VALUES (\'1\', \'admin\', \'Keep publicando sus comentarios aqui \', \'25 Aug 2006\');');
                if($insert_comment){
                    echo "<li class=\"tick\">Tabla de comentarios creada con éxito.</li>"; 
                }else{
                    echo "<li class=\"cross\">No se puede crear un comentario de tabla. Intente configurar/reiniciar de nuevo. </li>"; 
                }
            }else{            
                echo "<li class=\"cross\">No se pudo usar/seleccionar la base de datos. Compruebe el archivo de configuracion.".mysql_error()."</li>";
            }

            //creating product_caffe table
            $table_product=$conn->query('CREATE TABLE caffaine(itemid int not null primary key auto_increment, itemcode varchar(15),itemdisplay varchar(500),itemname varchar(50),itemdesc varchar(1000),categ varchar(200),price varchar(20))');
            if($table_product){
                $itemcode = array('0987','3876','4589','7619','5642','7569','3671','1672','4276','9680');
                $itemname = array('Affogato','Americano','Bicerin','Café Bombón','Café au lait','Caffé corretto','Caffé latte','Café mélange','Cafe mocha','Cappuccino');
                $itemdesc = array('An affogato (Italian, "drowned") is a coffee-based beverage. It usually takes the form of a scoop of vanilla gelato or ice cream topped with a shot of hot espresso. Some variations also include a shot of Amaretto or other liqueur.','An affogato (Italian, "drowned") is a coffee-based beverage. It usually takes the form of a scoop of vanilla gelato or ice cream topped with a shot of hot espresso. Some variations also include a shot of Amaretto or other liqueur.','An Americano is an espresso-based drink designed to resemble coffee brewed in a drip filter, considered popular in the United States of America. This drink consists of a single or double-shot of espresso combined with up to four or five ounces of hot water in a two-demitasse cup.','Cafe Bombon was made popular in Valencia, Spain, and spread gradually to the rest of the country. It might have been re-created and modified to suit European tastebuds as in many parts of Asia such as Malaysia, Thailand and Singapore the same recipe for coffee which is called "Kopi Susu Panas" (Malaysia) or "Kafe Ron" (Thailand) has already been around for decades and is very popular in "mamak" stalls or "kopitiams" in Malaysia.','Café au lait is a French coffee drink. In Europe, "café au lait" stems from the same continental tradition as "caffè latte" in Italy, "café con leche" in Spain, "kawa biała" ("white coffee") in Poland, "Milchkaffee" in Germany, "Grosser Brauner" in Austria, "koffie verkeerd" in Netherlands, and "café com leite" in Portugal, simply "coffee with milk".','Caffè corretto is an Italian beverage that consists of a shot of espresso with a shot of liquor, usually grappa, and sometimes sambuca or brandy. It is also known (outside of Italy) as an "espresso corretto". It is ordered as "un caffè corretto alla grappa," "[…] corretto alla sambuca," or "[…] corretto al cognac," depending on the desired liquor.','In Italy, latte means milk. What in English-speaking countries is now called a latte is shorthand for "caffelatte" or "caffellatte" ("caffè e latte"). The Italian form means "coffee and milk", similar to the French café au lait, the Spanish café con leche and the Portuguese café com leite. Other drinks commonly found in shops serving caffè lattes are cappuccinos and espressos. Ordering a "latte" in Italy will get the customer a glass of hot or cold milk. Caffè latte is a coffee-based drink made primarily from espresso and steamed milk. It consists of one-third espresso, two-thirds heated milk and about 1cm of foam. Depending on the skill of the barista, the foam can be poured in such a way to create a picture. Common pictures that appear in lattes are love hearts and ferns. Latte art is an interesting topic in itself.','In Italy, latte means milk. What in English-speaking countries is now called a latte is shorthand for "caffelatte" or "caffellatte" ("caffè e latte"). The Italian form means "coffee and milk", similar to the French café au lait, the Spanish café con leche and the Portuguese café com leite. Other drinks commonly found in shops serving caffè lattes are cappuccinos and espressos. Ordering a "latte" in Italy will get the customer a glass of hot or cold milk. Caffè latte is a coffee-based drink made primarily from espresso and steamed milk. It consists of one-third espresso, two-thirds heated milk and about 1cm of foam. Depending on the skill of the barista, the foam can be poured in such a way to create a picture. Common pictures that appear in lattes are love hearts and ferns. Latte art is an interesting topic in itself.','Café mélange is a black coffee mixed (french "mélange") or covered with whipped cream, very popular in Austria, Switzerland and the Netherlands.','Caffè Mocha or café mocha, is an American invention and a variant of a caffe latte, inspired by the Turin coffee beverage Bicerin. The term "caffe mocha" is not used in Italy nor in France, where it is referred to as a "mocha latte". Like a caffe latte, it is typically one third espresso and two thirds steamed milk, but a portion of chocolate is added, typically in the form of sweet cocoa powder, although many varieties use chocolate syrup. Mochas can contain dark or milk chocolate.');
                $categ = array('Espresso,Vanilla Gelato','Espresso','Espresso, Chocolate, Milk','Espresso, Sweetened Milk','Coffee, Milk','Espresso, Liquor Shot','Espresso, Milk','White Creame','Latte, Chocolate','Espresso, Milk');
                $itemprice = array(4.69,5.00,8.90,7.08,10.15,6.01,6.04,3.06,4.05,3.06);
                for($i = 0; $i<count($itemcode); $i++){
                    $pic = '/xvwa/img/'.$itemcode[$i].'.png';
                    $sql = 'INSERT into caffaine(itemcode,itemdisplay,itemname,itemdesc,categ,price) VALUES (\''.$itemcode[$i].'\',\''.$pic.'\',\''.$itemname[$i].'\',\''.$itemdesc[$i].'\',\''.$categ[$i].'\',\''.$itemprice[$i].'\');';
                    $insert_product=$conn->query($sql);
                }
                if($insert_product){
                    echo "<li class=\"tick\">Tabla de productos creado con éxito.</li>"; 
                }else{
                    echo "<li class=\"cross\">No se pueden crear productos de mesa. Intente configurar/reiniciar de nuevo.".mysql_error()." </li>"; 
                }
            }else{            
                echo "<li class=\"cross\">No se pudo usar/seleccionar la base de datos. Compruebe el archivo de configuracion.".mysql_error()."</li>";
            }
            //creating user table
            $table_user=$conn->query("CREATE table users(uid int not null primary key auto_increment, username varchar(20),password varchar(50))");
            if($table_user){
                $uname = array('admin', 'invitado');
                $pwd = array('70141889550cec0cfa21962be7d171ef', 'a6ae8a143d440ab8c006d799f682d48d');
                for($i=0;$i<count($uname);$i++){
                    $sql = "INSERT INTO users (username,password) values ('".$uname[$i]."','".$pwd[$i]."')";
                    $insert_user=$conn->query($sql);
                }
                if($insert_user){
                    echo "<li class=\"tick\">Tabla de usuarios creado con éxito.</li>"; 
                }else{
                    echo "<li class=\"cross\">No se puede crear la tabla de usuarios. Intente configurar/reiniciar de nuevo.".mysql_error()." </li>"; 
                }
            }else{
                echo "<li class=\"cross\">No se pudo usar/seleccionar la base de datos. Compruebe el archivo de configuracion.".mysql_error()."</li>";   
            }

            
       
        echo "<br><li class=\"tick\">Configuracion finalizada</li>";

        echo "<hr>";

        echo "</div>";
    }
     echo "</ul>";
}

?>
	<div class="footer">
		&copy; <?php print date("Y");?>
	</div>

</body>
</html>