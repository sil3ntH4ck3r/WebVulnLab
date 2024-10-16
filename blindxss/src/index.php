<?php
// Función para generar una cadena aleatoria
function generateRandomString($length = 10) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}
if ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1') {
    $cookieValue = generateRandomString(); // Generar una cadena aleatoria para el valor de la cookie
    setcookie('cookie', $cookieValue);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="blindxss.css">
    <title>Blind XSS</title>

    <head>
        <title>Cybertec</title>

</head>
<body>

    <header>
        <nav>
            <div class="container">
                <h1 class="logo">Cybertec</h1>
                <ul class="menu">
                <li><a href="http://blindxss.local/#">Inicio</a></li>
                    <li><a href="http://blindxss.local/#services">Servicios</a></li>
                    <li><a href="http://blindxss.local/#about">Sobre Nosotros</a></li>
                    <li><a href="http://blindxss.local/#contact">Contacto</a></li>
                </ul>
            </div>
        </nav>
    </header>
    <section class="services" id="services">
        <div class="container">
            <h2>Nuestros Servicios</h2>
            <div class="service">
                <i class="fas fa-shield-alt"></i>
                <h3>Seguridad en Redes</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc euismod interdum massa, a lacinia arcu bibendum vitae.</p>
            </div>
            <div class="service">
                <i class="fas fa-lock"></i>
                <h3>Seguridad de Datos</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc euismod interdum massa, a lacinia arcu bibendum vitae.</p>
            </div>
        </div>
    </section>
    <section class="about" id="about">
        <div class="container">
            <h2>Sobre Nosotros</h2>
            <div class="about-content">
                <p>Cybertec es una compañía líder en el mercado de la seguridad informática. Ofrecemos soluciones personalizadas para proteger su información y sus sistemas de ciberataques. Nuestro equipo de expertos en seguridad cuenta con años de experiencia en el campo y está dedicado a brindarle el mejor servicio posible.</p>
                <p>Estamos comprometidos con la seguridad de nuestros clientes y trabajamos incansablemente para mantenernos actualizados con las últimas tendencias en seguridad informática. Nos enorgullece ofrecer servicios de alta calidad y precios competitivos, lo que nos convierte en la mejor opción para sus necesidades de seguridad informática.</p>
            </div>
        </div>
    </section>
    <section class="contact" id="contact">
    <div class="container">
    <h1>Comentarios</h1>

    <?php
    // Verificar si se ha enviado el botón de eliminar comentarios
    if (isset($_POST['delete'])) {
        // Borrar todos los comentarios del archivo
        file_put_contents('comments.txt', '');
    }
    ?>

    <?php
    if(isset($_POST['comment'])) {
        $comment = $_POST['comment'];
        $name = $_POST['name'];
        $date = date('F j, Y, g:i a');
        $new_comment = "<div class='comment'><h4>Comentario de " . $name . " el " . $date . ":</h4><p>" . $comment . "</p></div>";
        $existing_comments = "";
        if(file_exists("comments.txt")) {
            $existing_comments = file_get_contents("comments.txt");
        }
        file_put_contents("comments.txt", $new_comment . $existing_comments);
    }
    $comments = [];
    if(file_exists("comments.txt")) {
        $comments = file("comments.txt", FILE_IGNORE_NEW_LINES);
    }
    ?>
        <div class="comment-box">
        <?php foreach($comments as $comment) {
                echo $comment;
            } ?>
        </div>

        <h2>Deje su comentario!</h2>
        <div class="contact-form">
            <form action="" method="post">

                
                <input type="text" name="name" placeholder="Nombre">
                
                <textarea name="comment" placeholder="Mensaje"></textarea><br>
                <button type="submit">Dejar comentario</button> <br>

            </form>

            <form action="" method="post">
                <button type="submit" name="delete">Borrar comentarios</button>
            </form>
        </div>
    </div>
</section>
<footer>
<p xmlns:cc="http://creativecommons.org/ns#" xmlns:dct="http://purl.org/dc/terms/"><a property="dct:title" rel="cc:attributionURL" href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev">WebVulnLab</a> by <a rel="cc:attributionURL dct:creator" property="cc:attributionName" href="https://github.com/sil3ntH4ck3r">sil3nth4ck3r</a> is licensed under <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1" target="_blank" rel="license noopener noreferrer" style="display:inline-block;">CC BY-NC-SA 4.0
</footer>
</html>