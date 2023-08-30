<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="cssi.css">
    <title>CSS Injection</title>

    <style id="theme">
        <?php
            $css = isset($_GET['css']) ? $_GET['css'] : '';
            if ($css) {
                echo ".services { background-color: #676464; }";
                echo ".contact { background-color: #676464; }";
            }
            echo "body {";
                echo $css;
            echo "}";
        ?>
    </style>                                                                                                                                                                                                                                                                                                                                                                                                                      
    <style>
        .switch {
            display: inline-block;
            position: absolute;
            top: 40px; /* Ajusta la posición vertical */
            right: 40px; /* Ajusta la posición horizontal */
        }
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            right: 0;
            width: 36px;
            height: 20px;
            background-color: #ccc;
            border-radius: 20px;
            transition: 0.4s;
        }
        .slider:before {
            content: "";
            position: absolute;
            height: 14px;
            width: 14px;
            top: 3px;
            left: 3px;
            background-color: white;
            border-radius: 50%;
            transition: 0.4s;
        }
        input:checked + .slider {
            background-color: #000;
        }
        input:checked + .slider:before {
            transform: translateX(16px);
        }
        .switch p {
            display: none;
            position: absolute;
            top: 100%; /* Mostrar el mensaje debajo del interruptor */
            right: -5px;
            background-color: #000;
            color: #fff;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            pointer-events: none;
            z-index: 1; /* Asegurar que el mensaje esté en capa superior */
        }
        .switch:hover p {
            display: block;
        }
    </style>

    <head>
        <title>Cybertec</title>
    </head>

<body>

    <header>
        <nav>
            <div class="container">
                <h1 class="logo">Cybertec</h1>
                <ul class="menu">
                    <li><a href="#">Inicio</a></li>
                    <li><a href="#services">Servicios</a></li>
                    <li><a href="#about">Sobre Nosotros</a></li>
                    <li><a href="#contact">Contacto</a></li>
                    <label class="switch">
                        <input type="checkbox" id="toggleDarkMode">
                        <span class="slider"></span>
                        <p>Activa el interruptor para cambiar al modo oscuro</p>
                    </label>
                </ul>
            </div>
            <script>
                const toggleDarkMode = document.getElementById('toggleDarkMode');
                const theme = document.getElementById('theme');

                // Actualizar el estado del switch al cargar la página
                if (theme.innerHTML.includes('background-color: gray; color: white;')) {
                    toggleDarkMode.checked = true;
                }

                toggleDarkMode.addEventListener('change', function() {
                    if (theme.innerHTML.includes('background-color: gray; color: white;')) {
                        window.location.replace(window.location.pathname);
                    } else {
                        window.location.replace(window.location.pathname + '?css=background-color%3A%20gray%3B%20color%3A%20white%3B%20');
                    }
                });
            </script>
        </nav>
    </header>
    <section class="services" id="services">
        <div class="container">
            <h2>Nuestros Servicios</h2>
            <div class="service">
                <i class="fas fa-shield-alt"></i>
                <h3>Seguridad en Redes</h3>
                <p>Protegemos sus sistemas y datos para mantener su red segura frente a amenazas.</p>
            </div>
            <div class="service">
                <i class="fas fa-lock"></i>
                <h3>Seguridad de Datos</h3>
                <p>Mantenga sus datos sensibles a salvo de accesos no autorizados y posibles fugas.</p>
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
            <h2>Contacto</h2>
            <div class="contact-form">
                <form action="#" method="post">
                    <input type="text" name="name" placeholder="Nombre Completo">
                    <input type="email" name="email" placeholder="Correo Electrónico">
                    <textarea name="message" placeholder="Mensaje"></textarea>
                    <button type="submit">Enviar Mensaje</button>
                </form>
            </div>
        </div>
    </section>
    <footer>
    <p xmlns:cc="http://creativecommons.org/ns#" xmlns:dct="http://purl.org/dc/terms/"><a property="dct:title" rel="cc:attributionURL" href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev">WebVulnLab</a> by <a rel="cc:attributionURL dct:creator" property="cc:attributionName" href="https://github.com/sil3ntH4ck3r">sil3nth4ck3r</a> is licensed under <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1" target="_blank" rel="license noopener noreferrer" style="display:inline-block;">CC BY-NC-SA 4.0
	</footer>
</body>
</html>