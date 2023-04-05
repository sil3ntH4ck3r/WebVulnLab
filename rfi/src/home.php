<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="lfi.css">
    <title>Remote File Inclusion</title>

    <head>
        <title>Cybertec</title>

</head>
<body>

    <header>
        <nav>
            <div class="container">
                <h1 class="logo">Cybertec</h1>
                <ul class="menu">
                <li><a href="http://rfi.local/home.php#">Inicio</a></li>
                    <li><a href="http://rfi.local/?show=home.php#services">Servicios</a></li>
                    <li><a href="http://rfi.local/?show=home.php#about">Sobre Nosotros</a></li>
                    <li><a href="http://rfi.local/?show=home.php#contact">Contacto</a></li>
                    <li><a href="http://rfi.local/?show=productos.php">Productos</a></li>
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
            <h2>Contacto</h2>
            <div class="contact-form">
                <form action="submit-form.php" method="post">
                    <input type="text" name="name" placeholder="Nombre Completo">
                    <input type="email" name="email" placeholder="Correo Electrónico">
                    <textarea name="message" placeholder="Mensaje"></textarea>
                    <button type="submit">Enviar Mensaje</button>
                </form>
            </div>
        </div>
    </section>
    <footer>
		<p>© 2023 Cybertec</p>
	</footer>
</body>
</html>