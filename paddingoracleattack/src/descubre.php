<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Padding Oracle Attack</title>
    <link href="main.css" rel="stylesheet" />
    <style>
        .process-step {
            background-color: #fff;
            border-radius: 8px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .process-step h2 {
            color: #007bff;
            margin-bottom: 1rem;
        }

        .cta-button {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .cta-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <header>
        <nav class="container">
            <a class="logo"  href="index.php">TechNova</a>
            <div class="nav-links">
                <a href="http://paddingoracleattack.local/index.php#solutions">Soluciones</a>
                <a href="http://paddingoracleattack.local/index.php#innovation">Innovación</a>
                <a href="http://paddingoracleattack.local/index.php#vision">Nosotros</a>
                <a href="http://paddingoracleattack.local/reiniciar.php">Reiniciar Base de Datos</a>
                <?php
                    if (isset($_COOKIE['cookieAuth']))
                    {   
                        echo '<a href="http://paddingoracleattack.local/logout.php">Logout</a>';
                        echo '<a href="http://paddingoracleattack.local/perfil.php">Perfil</a>';
                    }
                ?>
            </div>
        </nav>
    </header>

    <br><br>

    <main class="container">
        <h1>Descubre Cómo TechNova Transforma tu Negocio</h1>

        <div class="process-step">
            <h2>1. Evaluación Personalizada</h2>
            <p>Nuestro equipo de expertos realiza un análisis detallado de tus procesos actuales y necesidades específicas para identificar áreas de mejora.</p>
        </div>

        <div class="process-step">
            <h2>2. Diseño de Soluciones a Medida</h2>
            <p>Desarrollamos soluciones tecnológicas personalizadas que se adaptan perfectamente a tu negocio, maximizando la eficiencia y la productividad.</p>
        </div>

        <div class="process-step">
            <h2>3. Implementación Sin Interrupciones</h2>
            <p>Nuestro equipo se encarga de la implementación de las nuevas soluciones, asegurando una transición suave y minimizando el impacto en tus operaciones diarias.</p>
        </div>

        <div class="process-step">
            <h2>4. Capacitación y Soporte Continuo</h2>
            <p>Proporcionamos capacitación completa a tu equipo y ofrecemos soporte técnico continuo para garantizar el máximo aprovechamiento de nuestras soluciones.</p>
        </div>

        <a href="http://paddingoracleattack.local/formulario.php" class="cta-button">Solicita una consulta gratuita</a>
    </main>
    <br>
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>TechNova</h3>
                    <p>Transformando el futuro, hoy.</p>
                </div>
                <div class="footer-section">
                    <h3>Enlaces rápidos</h3>
                    <ul class="footer-links">
                        <li><a href="http://paddingoracleattack.local/index.php#solutions">Soluciones</a></li>
                        <li><a href="http://paddingoracleattack.local/index.php#innovation">Innovación</a></li>
                        <li><a href="http://paddingoracleattack.local/index.php#vision">Sobre nosotros</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Contáctanos</h3>
                    <p>info@webvulnlab.paddingoracleattack.local</p>
                    <p>+1 (555) 123-4567</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <span id="year"></span> <a property="dct:title" rel="cc:attributionURL" href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev">WebVulnLab</a> by <a href="https://github.com/sil3ntH4ck3r">sil3nth4ck3r</a> is licensed under <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1">CC BY-NC-SA 4.0                
                <script>
                    document.getElementById("year").textContent = new Date().getFullYear();
                </script>
            </div>
        </div>
    </footer>
</body>
</html>
