<?php
    if (isset($_COOKIE['cookieAuth']))
    {   
        header('Location: perfil.php');
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Padding Oracle Attack</title>
    <link href="main.css" rel="stylesheet" />
    <style>
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

        .login-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #edf0f4;
        }

        .login-form {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .login-form h2 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .login-form button {
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

        .login-form button:hover {
            background-color: #0056b3;
        }
        .mensaje {
            max-width: 400px;
            margin: 0 auto;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0.25rem;
        }

        .error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .exito {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .oculto {
            display: none;
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
            </div>
        </nav>
    </header>

    <main>
        <br><br><br><br><br>
        <div id="mensaje" class="mensaje oculto"></div>
        <div class="login-section">
            <div class="login-form">
                <h2>Crea tu cuenta</h2>
                <form id="formulario-registro" action="newuser.php" method="post">
                    <div class="form-group">
                        <label for="username">Usuario</label>
                        <input id="nombre" type="text" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Correo electrónico</label>
                        <input id="email" type="email" name="email" required="">
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input id="contraseña" type="password" name="contraseña" required="">
                    </div>
                    <button type="submit">Regístrate</button>
                </form>
                <p style="text-align: center; margin-top: 1rem; font-size: 0.9rem;">
                    ¿Ya tienes una cuenta? <a href="http://paddingoracleattack.local/index.php" style="color: #007bff; text-decoration: none;">Iniciar Sesión</a>
                </p>
            </div>
        </div>

    <br>

    <script>
       
        const mensaje = document.getElementById('mensaje');
        var formulario = document.getElementById("formulario-registro");

       
        formulario.addEventListener("submit", function(evento) {
            
            evento.preventDefault();

            
            var nombre = document.getElementById("nombre").value;
            var email = document.getElementById("email").value;
            var contraseña = document.getElementById("contraseña").value;

            
            var datos = new FormData();
            datos.append("nombre", nombre);
            datos.append("email", email);
            datos.append("contraseña", contraseña);

            
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    
                    mensaje.classList.remove('error', 'exito');
                    
                    if (this.responseText.includes("correctamente")) { 
                        mensaje.classList.add('exito');
                    } else { // Caso contrario es un error
                        mensaje.classList.add('error');
                    }

                    // Mostrar el mensaje
                    mensaje.innerHTML = this.responseText;
                    mensaje.style.display = 'block';
                }
            };
            xhr.open("POST", "newuser.php");
            xhr.send(datos);
        });
  </script>

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
