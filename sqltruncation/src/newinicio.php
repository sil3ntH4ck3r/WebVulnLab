<?php
    error_reporting(0);
    session_start();

    function getUserFromJwt($jwt) {
        $key = "your_secret_key"; // Change this to your own secret key

        list($header, $payload, $signature) = explode('.', $jwt);

        $decodedHeader = base64_decode(str_replace(['-', '_', ''], ['+', '/', '='], $header));
        $decodedPayload = base64_decode(str_replace(['-', '_', ''], ['+', '/', '='], $payload));

        $expectedSignature = hash_hmac('sha256', $header . '.' . $payload, $key, true);
        $expectedBase64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($expectedSignature));

        if ($signature !== $expectedBase64UrlSignature) {
            return false; // Invalid JWT signature
        }

        $userData = json_decode($decodedPayload, true);
        return $userData['user'];
    }

    $cookieUser = null;
    if (isset($_COOKIE["jwtToken"])) {
        $jwtToken = $_COOKIE["jwtToken"];
        $cookieUser = getUserFromJwt($jwtToken);
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="inicio.css">
    <title>SQL Truncation</title>
</head>
<body>

<header>
    <nav>
        <div class="container">
            <h1 class="logo">Cybertec</h1>
            <ul class="menu">
                <?php if ($cookieUser) : ?>
                    <li><a href="http://sqltruncation.local/inicio.php">Inicio</a></li>
                    <li><a href="http://sqltruncation.local/logout.php">Logout</a></li>
                       <li><a href="http://sqltruncation.local/perfil.php">Perfil</a></li>
                        <?php if ($cookieUser == "admin") : ?>
                            <li><a href="http://sqltruncation.local/dashboard.php">Dashboard</a></li>
                        <?php endif; ?>
                    <?php else : ?>
                        <li><a href="http://sqltruncation.local/inicio.php">Inicio</a></li>
                        <li><a href="http://sqltruncation.local/index.php">Login</a></li>
                        <li><a href="http://sqltruncation.local/register.php">Register</a></li>
                        <li><a href="http://sqltruncation.local/reiniciar.php">Reiniciar Base de Datos</a></li>
                    <?php endif; ?>
            </ul>
        </div>
    </nav>
</header>

<!-- Sección de servicios -->
<section class="services" id="services">
    <div class="container">
        <h2>Nuestros Servicios</h2>
        <!-- Servicios aquí -->
    </div>
</section>

<!-- Sección "Sobre Nosotros" -->
<section class="about" id="about">
    <div class="container">
        <h2>Sobre Nosotros</h2>
        <!-- Contenido sobre nosotros aquí -->
    </div>
</section>

<!-- Sección de testimonios -->
<section class="testimonials" id="testimonials">
    <div class="container">
        <h2>Testimonios</h2>
        <div class="testimonial-carousel">
            <!-- Testimonios aquí -->
        </div>
        <div class="testimonial-controls">
            <button id="prevTestimonial">&#8249;</button>
            <button id="nextTestimonial">&#8250;</button>
        </div>
    </div>
</section>
<script>
    // JavaScript del carrusel de testimonios
</script>

<!-- Sección de contacto -->
<section class="contact" id="contact">
    <div class="container">
        <!-- Formulario de contacto aquí -->
    </div>
</section>

<!-- Pie de página -->
<footer>
    <!-- Contenido del pie de página aquí -->
</footer>

</body>
</html>
