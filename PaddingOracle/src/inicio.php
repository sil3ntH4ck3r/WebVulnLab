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

<?php
$password="invitado";

function createcookie($user, $password) {
  $string = "user=".$user; 
  $passphrase = 'pntstrlb'; 
  $encryptedCookie = encryptString($string, $passphrase); 
  setcookie("cookieAuth", $encryptedCookie);

}

function encryptString($unencryptedText, $passphrase) { 
  $iv = mcrypt_create_iv( mcrypt_get_iv_size(MCRYPT_DES, MCRYPT_MODE_CBC), MCRYPT_RAND);
  $text = pkcs5_pad($unencryptedText,8);
  $enc = mcrypt_encrypt(MCRYPT_DES, $passphrase, $text, MCRYPT_MODE_CBC, $iv);
  return base64_encode($iv.$enc); 
}

function decryptString($encryptedText, $passphrase) {
  $encrypted = base64_decode($encryptedText);
  $iv_size =  mcrypt_get_iv_size(MCRYPT_DES, MCRYPT_MODE_CBC);
  $iv = substr($encrypted,0,$iv_size);
  $dec = mcrypt_decrypt(MCRYPT_DES, $passphrase, substr($encrypted,$iv_size), MCRYPT_MODE_CBC, $iv);
  $str = pkcs5_unpad($dec); 
  if ($str === false) {
    echo "Padding Invalido";
    die(); 
  }
  else {
    return $str; 
  }
}
function pkcs5_pad ($text, $blocksize) 
{ 
    $pad = $blocksize - (strlen($text) % $blocksize); 
    return $text . str_repeat(chr($pad), $pad); 
} 

function pkcs5_unpad($text) 
{ 
    $pad = ord($text{strlen($text)-1}); 
    if ($pad === 0) return false;
    if ($pad > strlen($text)) return false; 
    if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return false; 
    return substr($text, 0, -1 * $pad); 
} 

if (isset($_COOKIE["cookieAuth"])) {
  // desencriptamos la cookie
 	$decryptedCookie = decryptString($_COOKIE["cookieAuth"], "pntstrlb");
  // mostramos el valor de la cookie desencriptada
  //echo "Cookie desencriptada: " . $decryptedCookie;
} else {
  // la cookie no está establecida, mostramos un mensaje de error
  //echo "La cookie no está establecida.";
}

?>


<!DOCTYPE html>
<html>
<head>
    <title><?php print "Padding Oracle Attack";?></title>

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
	    	<a class="nav-link <?php if ($CURRENT_PAGE == "Index") {?>active<?php }?>" href="/?show=include.php">Inicio</a>
	  	</li>
	  	<li class="nav-item">
	    	<a class="nav-link <?php if ($CURRENT_PAGE == "About") {?>active<?php }?>" href="/?show=about.php">Sobre nosotros</a>
	  	</li>
	  	<li class="nav-item">
	    	<a class="nav-link <?php if ($CURRENT_PAGE == "Contact") {?>active<?php }?>" href="?show=contact.php">Contactenos</a>
	  	</li>

		  <?php
                    session_start();

                    if(!$_SESSION['user'])
                    {
                ?>
                <li class="nav-item"> <a class="nav-link <?php if ($CURRENT_PAGE == "Login") {?>active<?php }?>" href="?show=inicio.php">Iniciar Sesion</a> </li>
                <?php 
                    }                
				?>

		<li class="nav-item"> 
			<a class="nav-link <?php if ($CURRENT_PAGE == "Setup") {?>active<?php }?>" href="http://localhost:8007/setup/database.php">Configurar Base de Datos</a> 
		</li>
		  <?php
                    session_start();

                    if($_SESSION['user'])
                    {
                ?>
                <li class="nav-item"> <a class="nav-link <?php if ($CURRENT_PAGE == "Logout") {?>active<?php }?>" href="?show=logout.php">Cerrar Sesion</a> </li>
                <?php 
                    }                
				?>
				<?php
                    session_start();

                    if($_SESSION['user'])
                    {
                ?>
                <li class="nav-item"> <a class="nav-link <?php if ($CURRENT_PAGE == "Logout") {?>active<?php }?>" href="http://localhost:8007/vuln/padding.php">Perfil</a> </li>
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

			<?php if($_SESSION['user']=="admin" || $_SESSION['user']=="invitado") { ?>
						<?php $logedInUsername = $_SESSION['user'];?>
						<?php $user=$logedInUsername?>
						<?php createcookie($user, $password);?>
						<span class="text text-danger"><b><?php echo "Inicio de sesion correcto, ahora puedes ingresar a tu perfil"; ?></b></span>
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
