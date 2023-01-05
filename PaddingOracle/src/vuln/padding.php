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
		<h1>Perfil</h1>
	</div>


	<div class="container">
		<ul class="nav nav-pills">
	  	<li class="nav-item">
	    	<a class="nav-link <?php if ($CURRENT_PAGE == "Index") {?>active<?php }?>" href="http://localhost:8007/?show=include.php">Inicio</a>
	  	</li>
	  	<li class="nav-item">
	    	<a class="nav-link <?php if ($CURRENT_PAGE == "About") {?>active<?php }?>" href="http://localhost:8007/?show=about.php">Sobre nosotros</a>
	  	</li>
	  	<li class="nav-item">
	    	<a class="nav-link <?php if ($CURRENT_PAGE == "Contact") {?>active<?php }?>" href="http://localhost:8007/?show=contact.php">Contactenos</a>
	  	</li>
		  <?php
                    session_start();

                    if(!$_SESSION['user'])
                    {
                ?>
                <li class="nav-item"> <a class="nav-link <?php if ($CURRENT_PAGE == "Login") {?>active<?php }?>" href="http://localhost:8007/?show=inicio.php">Iniciar Sesion</a> </li>
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
                <li class="nav-item"> <a class="nav-link <?php if ($CURRENT_PAGE == "Logout") {?>active<?php }?>" href="http://localhost:8007/?show=logout.php">Cerrar Sesion</a> </li>
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
			<h2>Pagina web en desarrollo!</h2>

            <div class="well">
        <div class="col-lg-6"> 
            <p>Aqui tiene toda la informacion sobre su perfil.  
                <form method='post' action=''>
                    <div class="form-group"> 
                        
                    <?php
							$pattern = "/user=/i";
			                $cookieUser = preg_replace($pattern, "", $decryptedCookie);
			            ?>

			            <?php if ($cookieUser == 'invitado' || $cookieUser == 'admin') { ?>
						  <span class="text text-danger"><b><?php echo "Has iniciado session como: $cookieUser"; ?></b></span>
						<?php }else { ?>

						<span class="text text-danger"><b><?php echo "El usuario no existe"; ?></b></span>

						<?php } ?>    
                        
                    </div>
                </div>
                
                

	<div class="footer">
		&copy; <?php print date("Y");?>
	</div>

</body>
</html>