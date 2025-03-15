<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSRF</title>
    <style>
        :root {
            --primary-color: #1e7a66;
            --secondary-color: #6eaa95;
            --accent-color: #f5f5f5;
            --text-color: #333;
            --light-text: #fff;
            --error-color: #d9534f;
            --success-color: #5cb85c;
            --warning-color: #f0ad4e;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', 'Segoe UI', sans-serif;
        }

        body {
            background-color: #f8f9fa;
            color: var(--text-color);
            line-height: 1.6;
        }

        header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--light-text);
            padding: 1.5rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5rem;
        }

        .logo i {
            font-size: 2rem;
            margin-right: 0.5rem;
        }

        header h1 {
            text-align: center;
            font-size: 2.5rem;
            margin: 0;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        }

        .tagline {
            text-align: center;
            font-size: 1.2rem;
            font-style: italic;
            margin-top: 0.5rem;
        }

        nav {
            background-color: #fff;
            padding: 1rem 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        nav ul {
            list-style: none;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        nav ul li {
            margin: 0 0.5rem;
        }

        nav ul li a {
            color: var(--primary-color);
            text-decoration: none;
            padding: 0.5rem 1rem;
            display: block;
            border-radius: 4px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        nav ul li a:hover {
            background-color: var(--primary-color);
            color: white;
        }

        nav ul li a.active {
            background-color: var(--primary-color);
            color: white;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .hero {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            background: url("/api/placeholder/1200/400") center/cover;
            height: 300px;
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .hero::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(30, 122, 102, 0.7);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            padding: 2rem;
            color: white;
        }

        .hero-content h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        }

        .hero-content p {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
        }

        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
        }

        .btn:hover {
            background-color: #155d4e;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-secondary {
            background-color: white;
            color: var(--primary-color);
        }

        .btn-secondary:hover {
            background-color: #f0f0f0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--primary-color);
            position: relative;
            padding-bottom: 0.5rem;
        }

        .section-title::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background-color: var(--secondary-color);
        }

        .categories {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .category-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .category-img {
            height: 180px;
            background: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .category-img i {
            font-size: 4rem;
            color: white;
        }

        .category-content {
            padding: 1.5rem;
            text-align: center;
        }

        .category-content h3 {
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        .featured-products {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .product-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .product-img {
            height: 200px;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-content {
            padding: 1.5rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .product-content h3 {
            margin-bottom: 0.5rem;
            color: var(--text-color);
        }

        .product-price {
            font-size: 1.25rem;
            font-weight: bold;
            color: var(--primary-color);
            margin: 0.5rem 0;
        }

        .product-description {
            margin-bottom: 1rem;
            flex-grow: 1;
        }

        .services {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .service-card {
            background-color: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: all 0.3s ease;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .service-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .testimonials {
            margin-bottom: 3rem;
        }

        .testimonial-card {
            background-color: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
            position: relative;
        }

        .testimonial-card::before {
            content: "";
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 4rem;
            color: rgba(30, 122, 102, 0.1);
            font-family: Georgia, serif;
        }

        .testimonial-text {
            font-style: italic;
            margin-bottom: 1rem;
            padding-left: 2rem;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
        }

        .testimonial-author img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 1rem;
            object-fit: cover;
        }

        .testimonial-name {
            font-weight: bold;
        }

        .testimonial-role {
            color: #666;
            font-size: 0.9rem;
        }

        .login-box, .form-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            margin: 2rem auto;
            padding: 2rem;
        }

        .login-box h2, .form-container h2 {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }

        .user-box {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .user-box input {
            width: 100%;
            padding: 0.8rem 0;
            font-size: 1rem;
            color: var(--text-color);
            border: none;
            border-bottom: 2px solid #ddd;
            outline: none;
            background: transparent;
            transition: all 0.3s;
        }

        .user-box label {
            position: absolute;
            top: 0.8rem;
            left: 0;
            font-size: 1rem;
            color: #666;
            pointer-events: none;
            transition: 0.5s;
        }

        .user-box input:focus ~ label,
        .user-box input:valid ~ label {
            top: -1.2rem;
            left: 0;
            color: var(--primary-color);
            font-size: 0.85rem;
        }

        .user-box input:focus {
            border-bottom: 2px solid var(--primary-color);
        }

        .form-container button, .login-box button {
            width: 100%;
            padding: 0.8rem;
            margin-top: 1.5rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-container button:hover, .login-box button:hover {
            background-color: #155d4e;
        }

        .mensaje {
            text-align: center;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 4px;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .contact-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-color);
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #f9f9f9;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 2px rgba(30, 122, 102, 0.2);
        }

        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        footer {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 3rem 0 1rem;
            margin-top: 3rem;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .footer-section h3 {
            font-size: 1.2rem;
            margin-bottom: 1rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .footer-section h3::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 2px;
            background-color: rgba(255, 255, 255, 0.5);
        }

        .footer-section p, .footer-section address {
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 0.5rem;
        }

        .footer-section ul li a {
            color: white;
            text-decoration: none;
            transition: all 0.3s;
        }

        .footer-section ul li a:hover {
            color: #f0f0f0;
            padding-left: 5px;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .social-links a {
            display: inline-block;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
        }

        .social-links a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
        }

        .copyright {
            text-align: center;
            padding-top: 2rem;
            margin-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.9rem;
        }

        .shopping-cart {
            position: relative;
            display: inline-block;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: var(--primary-color);
            color: white;
            font-size: 0.7rem;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: var(--primary-color);
            font-size: 1.5rem;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
                position: absolute;
                right: 1rem;
                top: 1rem;
            }

            nav ul {
                flex-direction: column;
                display: none;
            }

            nav ul.show {
                display: flex;
            }

            nav ul li {
                margin: 0.5rem 0;
            }

            .hero {
                height: 250px;
            }

            .hero-content h2 {
                font-size: 1.8rem;
            }

            .contact-form {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <link rel="stylesheet" href="all.min.css">
</head>
<body>
    <header>
        <div class="logo">
            <i class="fas fa-mortar-pestle"></i>
            <h1>Farmacia Saludable</h1>
        </div>
        <p class="tagline">Tu salud, nuestra prioridad</p>
    </header>

    <nav>
        <button class="mobile-menu-btn" id="mobileMenuBtn">
            <i class="fas fa-bars"></i>
        </button>
        <ul id="navMenu">
            <li><a href="#" class="active">Inicio</a></li>
            <li><a href="#">Productos</a></li>
            <li><a href="#">Medicamentos</a></li>
            <li><a href="#">Cosméticos</a></li>
            <li><a href="#">Servicios</a></li>
            <li><a href="#">Blog</a></li>
            <li><a href="#">Contacto</a></li>
            <li><a href="login.php">Mi Cuenta</a></li>
            <li>
                <a href="#" class="shopping-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count">0</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="container">
        <section class="hero">
            <div class="hero-content">
                <h2>Cuidamos de ti y tu familia</h2>
                <p>Encuentra todo lo que necesitas para tu salud y bienestar en un solo lugar.</p>
                <a href="#" class="btn">Explorar productos</a>
            </div>
        </section>

        <section>
            <h2 class="section-title">Nuestras Categorías</h2>
            <div class="categories">
                <div class="category-card">
                    <div class="category-img">
                        <i class="fas fa-pills"></i>
                    </div>
                    <div class="category-content">
                        <h3>Medicamentos</h3>
                        <p>Todo tipo de medicamentos con y sin receta médica</p>
                        <a href="#" class="btn btn-secondary">Ver más</a>
                    </div>
                </div>
                <div class="category-card">
                    <div class="category-img">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <div class="category-content">
                        <h3>Salud y Bienestar</h3>
                        <p>Productos para el cuidado de tu salud diaria</p>
                        <a href="#" class="btn btn-secondary">Ver más</a>
                    </div>
                </div>
                <div class="category-card">
                    <div class="category-img">
                        <i class="fas fa-spa"></i>
                    </div>
                    <div class="category-content">
                        <h3>Cosméticos</h3>
                        <p>Productos de belleza y cuidado personal</p>
                        <a href="#" class="btn btn-secondary">Ver más</a>
                    </div>
                </div>
                <div class="category-card">
                    <div class="category-img">
                        <i class="fas fa-baby"></i>
                    </div>
                    <div class="category-content">
                        <h3>Bebés</h3>
                        <p>Todo para el cuidado de los más pequeños</p>
                        <a href="#" class="btn btn-secondary">Ver más</a>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <h2 class="section-title">Productos Destacados</h2>
            <div class="featured-products">
                <div class="product-card">
                    <div class="product-img">
                        <img src="/api/placeholder/200/200" alt="Vitamina C" />
                    </div>
                    <div class="product-content">
                        <h3>Vitamina C 1000mg</h3>
                        <p class="product-description">Suplemento vitamínico para reforzar el sistema inmune. 60 comprimidos.</p>
                        <p class="product-price">€12.99</p>
                        <button class="btn">Añadir al carrito</button>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-img">
                        <img src="/api/placeholder/200/200" alt="Crema Hidratante" />
                    </div>
                    <div class="product-content">
                        <h3>Crema Hidratante Facial</h3>
                        <p class="product-description">Crema hidratante con ácido hialurónico para todo tipo de pieles. 50ml.</p>
                        <p class="product-price">€18.50</p>
                        <button class="btn">Añadir al carrito</button>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-img">
                        <img src="/api/placeholder/200/200" alt="Ibuprofeno" />
                    </div>
                    <div class="product-content">
                        <h3>Ibuprofeno 600mg</h3>
                        <p class="product-description">Analgésico y antiinflamatorio para aliviar el dolor. 30 comprimidos.</p>
                        <p class="product-price">€7.25</p>
                        <button class="btn">Añadir al carrito</button>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <h2 class="section-title">Nuestros Servicios</h2>
            <div class="services">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-stethoscope"></i>
                    </div>
                    <h3>Consulta Farmacéutica</h3>
                    <p>Resuelve tus dudas con nuestros farmacéuticos profesionales sin cita previa.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3>Entrega a Domicilio</h3>
                    <p>Recibe tus medicamentos y productos en la comodidad de tu hogar en menos de 24h.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-notes-medical"></i>
                    </div>
                    <h3>Seguimiento Farmacoterapéutico</h3>
                    <p>Servicio personalizado para optimizar el efecto de tus medicamentos.</p>
                </div>
            </div>
        </section>

        <section class="testimonials">
            <h2 class="section-title">Lo que dicen nuestros clientes</h2>
            <div class="testimonial-card">
                <p class="testimonial-text">Siempre encuentro todo lo que necesito y el personal es muy amable y profesional. Me han ayudado mucho con mis dudas sobre medicamentos.</p>
                <div class="testimonial-author">
                    <img src="/api/placeholder/50/50" alt="Cliente" />
                    <div>
                        <p class="testimonial-name">María Gómez</p>
                        <p class="testimonial-role">Cliente habitual</p>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <p class="testimonial-text">El servicio a domicilio es rápido y eficiente. Perfecto para cuando no puedo desplazarme a la farmacia por motivos de salud.</p>
                <div class="testimonial-author">
                    <img src="/api/placeholder/50/50" alt="Cliente" />
                    <div>
                        <p class="testimonial-name">Juan Martínez</p>
                        <p class="testimonial-role">Cliente desde 2020</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="login-box" style="display: none;">
        <h2>Iniciar sesión</h2>
        <form method="POST" action="login.php">
            <div class="user-box">
                <input type="text" name="username" id="nombre" required>
                <label>Usuario</label>
            </div>
            <div class="user-box">
                <input type="password" name="password" id="contraseña" required>
                <label>Contraseña</label>
            </div>
            <button type="submit">Iniciar sesión</button>
            <p style="text-align: center; margin-top: 1rem;">
                ¿No tienes cuenta? <a href="#" style="color: var(--primary-color);">Regístrate aquí</a>
            </p>
        </form>
    </div>

    <div class="form-container" style="display: none;">
        <h2>Cambiar contraseña</h2>
        <form method="POST" action="change_password.php">
            <div class="user-box">
                <input type="password" name="current_password" required>
                <label>Contraseña actual</label>
            </div>
            <div class="user-box">
                <input type="password" name="new_password" required>
                <label>Nueva contraseña</label>
            </div>
            <div class="user-box">
                <input type="password" name="confirm_password" required>
                <label>Confirmar nueva contraseña</label>
            </div>
            <button type="submit">Cambiar contraseña</button>
        </form>
    </div>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Farmacia Saludable</h3>
                <p>Tu farmacia de confianza con más de 15 años cuidando de la salud y bienestar de nuestros clientes.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="footer-section">
                <h3>Enlaces rápidos</h3>
                <ul>
                    <li><a href="#">Inicio</a></li>
                    <li><a href="#">Productos</a></li>
                    <li><a href="#">Servicios</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Contacto</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contacto</h3>
                <p>Dirección: Calle Falsa 123, Ciudad, País</p>
                <p>Teléfono: +34 123 456 789</p>
                <p>Email: info@farmaciasaludable.com</p>
            </div>
        </div>
        <div class="copyright">
            <p>
                <a href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev" target="_blank" style="color: var(--light-text); text-decoration: none;">WebVulnLab</a> by 
                <a href="https://github.com/sil3ntH4ck3r" target="_blank" style="color: var(--light-text); text-decoration: none;">sil3nth4ck3r</a> is licensed under 
                <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1" target="_blank" style="color: var(--light-text); text-decoration: none;">CC BY-NC-SA 4.0</a>
            </p>
        </div>
    </footer>

    <script>
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const navMenu = document.getElementById('navMenu');

        mobileMenuBtn.addEventListener('click', function () {
            navMenu.classList.toggle('show');
        });
    </script>
</body>

</html>
