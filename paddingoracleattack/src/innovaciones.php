<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Padding Oracle Attack</title>
    <link href="main.css" rel="stylesheet" />
    <style>
        .innovation-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .innovation-card {
            background-color: #fff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .innovation-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .innovation-card h2 {
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
                    if ($cookieUser=="admin"){
                        echo '<li><a href="http://paddingoracleattack.local/dashboard.php">Dashboard</a></li>';
                    } 
                ?>
            </div>
        </nav>
    </header>
    <br><br>
    <main class="container">
        <h1>Explora Nuestras Innovaciones</h1>

        <div class="innovation-grid">
            <div class="innovation-card">
                <h2>IA Predictiva</h2>
                <p>Nuestra tecnología de IA predictiva analiza patrones de datos complejos para anticipar tendencias del mercado y comportamientos de los consumidores.</p>
            </div>

            <div class="innovation-card">
                <h2>Blockchain Empresarial</h2>
                <p>Implementamos soluciones de blockchain para mejorar la seguridad, trazabilidad y eficiencia en las transacciones y la gestión de la cadena de suministro.</p>
            </div>

            <div class="innovation-card">
                <h2>IoT Industrial</h2>
                <p>Nuestras soluciones de Internet de las Cosas (IoT) optimizan los procesos de fabricación, mejoran el mantenimiento predictivo y aumentan la eficiencia operativa.</p>
            </div>

            <div class="innovation-card">
                <h2>Realidad Aumentada para Capacitación</h2>
                <p>Utilizamos la realidad aumentada para crear experiencias de capacitación inmersivas y efectivas, mejorando la retención de conocimientos y habilidades.</p>
            </div>

            <div class="innovation-card">
                <h2>Ciberseguridad Avanzada</h2>
                <p>Nuestros sistemas de ciberseguridad utilizan aprendizaje automático para detectar y prevenir amenazas en tiempo real, protegiendo los activos digitales de tu empresa.</p>
            </div>

            <div class="innovation-card">
                <h2>Automatización Robótica de Procesos (RPA)</h2>
                <p>Implementamos soluciones RPA para automatizar tareas repetitivas, liberando a tu equipo para enfocarse en actividades de mayor valor.</p>
            </div>
        </div>

        <div style="text-align: center; margin-top: 3rem;">
            <a href="http://paddingoracleattack.local/formulario.php" class="cta-button">Solicita una demostración</a>
        </div>
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
