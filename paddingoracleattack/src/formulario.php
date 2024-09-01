<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Padding Oracle Attack</title>
    <link href="main.css" rel="stylesheet" />
    <style>
        .form-container {
            background-color: #fff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }

        input, select, textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        textarea {
            height: 100px;
        }

        button {
            display: block;
            width: 100%;
            padding: 0.75rem;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
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
                        echo '<a href="http://paddingoracleattack.local/dashboard.php">Dashboard</a>';
                    } 
                ?>
            </div>
        </nav>
    </header>
    <br><br>
    <main class="container">
        <h1>Solicita una Consulta Gratuita</h1>

        <div class="form-container">
            <form id="consultation-form">
                <div class="form-group">
                    <label for="name">Nombre completo</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Teléfono</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="company">Empresa</label>
                    <input type="text" id="company" name="company" required>
                </div>
                <div class="form-group">
                    <label for="industry">Industria</label>
                    <select id="industry" name="industry" required>
                        <option value="">Selecciona una industria</option>
                        <option value="tecnologia">Tecnología</option>
                        <option value="finanzas">Finanzas</option>
                        <option value="salud">Salud</option>
                        <option value="educacion">Educación</option>
                        <option value="manufactura">Manufactura</option>
                        <option value="retail">Retail</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="challenge">Describe tu principal desafío tecnológico</label>
                    <textarea id="challenge" name="challenge" required></textarea>
                </div>
                <button type="submit">Solicitar Consulta Gratuita</button>
            </form>
        </div>
    </main>

    <script>
        document.getElementById('consultation-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Gracias por solicitar una consulta gratuita. Un miembro de nuestro equipo se pondrá en contacto contigo pronto.');
            this.reset();
        });
    </script>
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