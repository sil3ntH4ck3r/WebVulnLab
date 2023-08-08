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
    <title>Cybertec</title>
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
                    <?php if ($cookieUser == "jsmith") : ?>
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

<section class="testimonials" id="testimonials">
    <div class="container">
        <h2>Testimonios</h2>
        <div class="testimonial-carousel">
            <div class="testimonial">
                <p>"Los servicios de Cybertec son increíbles. Me ayudaron a proteger mi negocio de ciberamenazas y ahora me siento mucho más seguro."</p>
                <p class="author">- Cliente Satisfecho</p>
            </div>
            <div class="testimonial">
                <p>"Nunca había sentido tanta tranquilidad en cuanto a la seguridad de mis datos. ¡Gracias, Cybertec!"</p>
                <p class="author">- Otro Cliente Feliz</p>
            </div>
            <div class="testimonial">
                <p>"Trabajar en Cybertec ha sido una experiencia increíble. Aquí tenemos un equipo dedicado a proteger a nuestros clientes en todo momento."</p>
                <p class="author">- Juan Pérez, Director de Seguridad</p>
            </div>
            <div class="testimonial">
                <p>"Como desarrolladora de software en Cybertec, puedo decir que nuestra atención a la seguridad es insuperable. Es un orgullo formar parte de este equipo."</p>
                <p class="author">- Ana García, Desarrolladora de Software</p>
            </div>
            <div class="testimonial">
                <p>"Como CEO de Cybertec, no puedo estar más orgulloso del trabajo que nuestro equipo realiza cada día para proteger a nuestros clientes y sus datos.<br> Nuestra dedicación a la seguridad informática es incomparable."</p>
                <p class="author">- John Smith, CEO de Cybertec</p>
            </div>

        </div>
        <div class="testimonial-controls">
            <button id="prevTestimonial">&#8249;</button>
            <button id="nextTestimonial">&#8250;</button>
        </div>
    </div>
</section>

<script>
    const testimonialCarousel = document.querySelector('.testimonial-carousel');
    const prevTestimonialButton = document.getElementById('prevTestimonial');
    const nextTestimonialButton = document.getElementById('nextTestimonial');

    let currentIndex = 0;

    prevTestimonialButton.addEventListener('click', () => {
        currentIndex = (currentIndex - 1 + testimonialCarousel.children.length) % testimonialCarousel.children.length;
        updateCarousel();
    });

    nextTestimonialButton.addEventListener('click', () => {
        currentIndex = (currentIndex + 1) % testimonialCarousel.children.length;
        updateCarousel();
    });

    function updateCarousel() {
        const translateX = -currentIndex * 100;
        testimonialCarousel.style.transform = `translateX(${translateX}%)`;
    }

    // Auto avance de testimonios
    setInterval(() => {
        currentIndex = (currentIndex + 1) % testimonialCarousel.children.length;
        updateCarousel();
    }, 5000); // Cambia el valor (en milisegundos) para ajustar la velocidad del auto avance
</script>

<footer>
    <p xmlns:cc="http://creativecommons.org/ns#" xmlns:dct="http://purl.org/dc/terms/"><a property="dct:title" rel="cc:attributionURL" href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev">WebVulnLab</a> by <a rel="cc:attributionURL dct:creator" property="cc:attributionName" href="https://github.com/sil3ntH4ck3r">sil3nth4ck3r</a> is licensed under <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1" target="_blank" rel="license noopener noreferrer" style="display:inline-block;">CC BY-NC-SA 4.0</a></p>
</footer>

</body>
</html>
