<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Remote File Inclusion</title>
	<link rel="stylesheet" href="productos.css">
</head>
<body>

	<!-- Encabezado -->
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

	<!-- Sección de productos -->
	<section class="productos">
		<div class="producto">
			<i class="fas fa-shield-alt"></i>
			<h3>Protección contra ataques DDoS</h3>
			<p>Ofrecemos soluciones avanzadas para proteger su sitio web o aplicación contra ataques DDoS, garantizando la disponibilidad de sus servicios en todo momento.</p>
			<a href="#" class="btn">Más información</a>
		</div>
		<div class="producto">
			<i class="fas fa-lock"></i>
			<h3>Protección de datos</h3>
			<p>Nuestra empresa ofrece soluciones de protección de datos para empresas de todos los tamaños, incluyendo cifrado, autenticación de usuarios y más.</p>
			<a href="#" class="btn">Más información</a>
		</div>
		<div class="producto">
			<i class="fas fa-user-secret"></i>
			<h3>Gestión de identidad y acceso</h3>
			<p>Ofrecemos soluciones avanzadas para la gestión de identidad y acceso, permitiendo a las empresas asegurar el acceso a sus sistemas y datos de manera segura y eficiente.</p>
			<a href="#" class="btn">Más información</a>
		</div>
		<div class="producto">
			<i class="fas fa-bug"></i>
			<h3>Pruebas de penetración</h3>
			<p>Nuestra empresa ofrece pruebas de penetración para identificar vulnerabilidades en su infraestructura y aplicaciones, ayudando a prevenir ataques y proteger su empresa.</p>
			<a href="#" class="btn">Más información</a>
		</div>
	</section>

	<!-- Pie de página -->
	<footer>
	<p xmlns:cc="http://creativecommons.org/ns#" xmlns:dct="http://purl.org/dc/terms/"><a property="dct:title" rel="cc:attributionURL" href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev">WebVulnLab</a> by <a rel="cc:attributionURL dct:creator" property="cc:attributionName" href="https://github.com/sil3ntH4ck3r">sil3nth4ck3r</a> is licensed under <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1" target="_blank" rel="license noopener noreferrer" style="display:inline-block;">CC BY-NC-SA 4.0
	</footer>

</body>
</html>