<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Padding Oracle Attack</title>
        <link href="main.css" rel="stylesheet" />
        <style>
            .team-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 2rem;
            }

            .team-member {
                background-color: #fff;
                border-radius: 8px;
                padding: 1.5rem;
                text-align: center;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }

            .team-member:hover {
                transform: translateY(-5px);
                box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            }

            .team-member img {
                width: 150px;
                height: 150px;
                border-radius: 50%;
                object-fit: cover;
                margin-bottom: 1rem;
            }

            .team-member h2 {
                color: #007bff;
                margin-bottom: 0.5rem;
            }

            .team-member p {
                margin-bottom: 0.5rem;
            }

            .team-member a {
                color: #007bff;
                text-decoration: none;
            }

            .team-member a:hover {
                text-decoration: underline;
            }

            
            .profile {
                display: none; 
                position: fixed; 
                top: 50%; 
                left: 50%; 
                transform: translate(-50%, -50%); 
                background-color: #fff;
                border-radius: 8px;
                padding: 2rem;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
                z-index: 1001; 
                max-width: 600px; 
                width: 90%;
            }

            
            .modal-overlay {
                display: none; 
                position: fixed; 
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5); 
                z-index: 1000; 
            }

            
            .close-modal {
                background-color: #007bff;
                color: #fff;
                border: none;
                padding: 0.5rem 1rem;
                cursor: pointer;
                border-radius: 5px;
                float: right;
                font-size: 1rem;
                margin-top: -1.5rem; 
                margin-right: -1rem; 
            }

            .close-modal:hover {
                background-color: #0056b3;
            }

            .profile-header {
                display: flex;
                align-items: center;
                margin-bottom: 2rem;
            }

            .profile-image {
                width: 150px;
                height: 150px;
                border-radius: 50%;
                object-fit: cover;
                margin-right: 2rem;
            }

            .profile-name {
                color: #007bff;
            }

            .profile-title {
                color: #666;
                font-size: 1.2rem;
            }

            .profile-bio {
                margin-bottom: 2rem;
            }

            .profile-skills {
                background-color: #f8f9fa;
                padding: 1rem;
                border-radius: 8px;
            }

            .profile-skills h3 {
                color: #007bff;
                margin-bottom: 1rem;
            }

            .skills-list {
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .skill-tag {
                background-color: #e9ecef;
                color: #495057;
                padding: 0.25rem 0.5rem;
                border-radius: 4px;
                font-size: 0.9rem;
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const teamMembers = {
                    "ana-garcia": {
                        nombre: "Ana García",
                        puesto: "CEO y Fundadora",
                        imagen: "ana-garcia.jpeg",
                        bio: "Ana García es la visionaria detrás de TechNova. Con más de 15 años de experiencia en el sector tecnológico, Ana ha liderado el crecimiento de la empresa desde su fundación. Su pasión por la innovación y su habilidad para identificar tendencias emergentes han sido fundamentales para el éxito de TechNova.",
                        experiencia: [
                            "Liderazgo Estratégico", "Innovación Tecnológica", "Desarrollo de Negocios",
                            "Inteligencia Artificial", "Blockchain", "IoT"
                        ]
                    },
                    "carlos-rodriguez": {
                        nombre: "Carlos Rodríguez",
                        puesto: "CTO",
                        imagen: "carlos-rodriguez.jpeg",
                        bio: "Carlos Rodríguez es el CTO de TechNova. Es un experto en arquitectura de software y ha liderado varios proyectos importantes...",
                        experiencia: ["Desarrollo de Software", "Arquitectura de Sistemas", "Ciberseguridad"]
                    },
                    "laura-martinez": {
                        nombre: "Laura Martínez",
                        puesto: "Directora de Innovación",
                        imagen: "laura-martinez.jpeg",
                        bio: "Laura Martínez lidera el equipo de innovación en TechNova. Con un enfoque en la creatividad y soluciones disruptivas, ha llevado a cabo proyectos clave que han dado forma al futuro de la empresa...",
                        experiencia: ["Innovación", "Creatividad", "Gestión de Proyectos"]
                    },
                    "david-sanchez": {
                        nombre: "David Sánchez",
                        puesto: "Jefe de Desarrollo",
                        imagen: "david-sanchez.jpeg",
                        bio: "David Sánchez es el jefe de desarrollo en TechNova, encargado de supervisar el ciclo completo de desarrollo del software. Ha trabajado en varias aplicaciones móviles y web...",
                        experiencia: ["Desarrollo Front-End", "Back-End", "Gestión de Equipos"]
                    },
                    "elena-torres": {
                        nombre: "Elena Torres",
                        puesto: "Directora de Marketing",
                        imagen: "elena-torres.jpeg",
                        bio: "Elena Torres es la Directora de Marketing de TechNova, donde ha diseñado campañas innovadoras que han aumentado significativamente la visibilidad de la marca...",
                        experiencia: ["Marketing Digital", "SEO", "Gestión de Marca"]
                    },
                    "javier-lopez": {
                        nombre: "Javier López",
                        puesto: "Jefe de Seguridad",
                        imagen: "javier-lopez.jpeg",
                        bio: "Javier López es el Jefe de Seguridad de TechNova, garantizando que todos los datos y la infraestructura estén protegidos contra amenazas cibernéticas...",
                        experiencia: ["Ciberseguridad", "Protección de Datos", "Análisis de Vulnerabilidades"]
                    }
                };

                document.querySelectorAll('.view-profile').forEach(function (element) {
                    element.addEventListener('click', function (event) {
                        event.preventDefault(); // Evitar redireccionamiento
                        const id = event.target.getAttribute('data-id');
                        showProfile(id);
                    });
                });

                function showProfile(id) {
                    const profileData = teamMembers[id];
                    if (profileData) {
                        document.querySelector('.profile-image').src = profileData.imagen;
                        document.querySelector('.profile-name').textContent = profileData.nombre;
                        document.querySelector('.profile-title').textContent = profileData.puesto;
                        document.querySelector('.profile-bio').textContent = profileData.bio;

                        const skillsContainer = document.querySelector('.skills-list');
                        skillsContainer.innerHTML = ''; // Limpiar habilidades previas

                        profileData.experiencia.forEach(skill => {
                            const skillTag = document.createElement('span');
                            skillTag.className = 'skill-tag';
                            skillTag.textContent = skill;
                            skillsContainer.appendChild(skillTag);
                        });

                        // Mostrar el modal y el fondo
                        document.querySelector('.modal-overlay').style.display = 'block';
                        document.querySelector('.profile').style.display = 'block';
                    }
                }

                function closeModal() {
                    document.querySelector('.modal-overlay').style.display = 'none';
                    document.querySelector('.profile').style.display = 'none';
                }

                document.querySelectorAll('.view-profile').forEach(function (element) {
                    element.addEventListener('click', function (event) {
                        event.preventDefault(); // Evitar redireccionamiento
                        const id = event.target.getAttribute('data-id');
                        showProfile(id);
                    });
                });

                // Cerrar el modal al hacer clic en el botón de cerrar
                document.querySelector('.close-modal').addEventListener('click', closeModal);

                // Cerrar el modal al hacer clic en el fondo oscuro
                document.querySelector('.modal-overlay').addEventListener('click', closeModal);
            });
        </script>

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
            <h1>Conoce a Nuestro Equipo</h1>

            <div class="team-grid">
                <div class="team-member">
                    <img src="/img/ana-garcia.jpeg" alt="Ana García">
                    <h2>Ana García</h2>
                    <p>CEO y Fundadora</p>
                    <a href="#" class="view-profile" data-id="ana-garcia">Ver perfil</a>
                </div>
                <div class="team-member">
                    <img src="/img/carlos-rodriguez.jpeg" alt="Carlos Rodríguez">
                    <h2>Carlos Rodríguez</h2>
                    <p>CTO</p>
                    <a href="#" class="view-profile" data-id="carlos-rodriguez">Ver perfil</a>
                </div>
                <div class="team-member">
                    <img src="/img/laura-martinez.jpeg" alt="Laura Martínez">
                    <h2>Laura Martínez</h2>
                    <p>Directora de Innovación</p>
                    <a href="#" class="view-profile" data-id="laura-martinez">Ver perfil</a>
                </div>
                <div class="team-member">
                    <img src="/img/david-sanchez.jpeg" alt="David Sánchez">
                    <h2>David Sánchez</h2>
                    <p>Jefe de Desarrollo</p>
                    <a href="#" class="view-profile" data-id="david-sanchez">Ver perfil</a>
                </div>
                <div class="team-member">
                    <img src="/img/elena-torres.jpeg" alt="Elena Torres">
                    <h2>Elena Torres</h2>
                    <p>Directora de Marketing</p>
                    <a href="#" class="view-profile" data-id="elena-torres">Ver perfil</a>
                </div>
                <div class="team-member">
                    <img src="/img/javier-lopez.jpeg" alt="Javier López">
                    <h2>Javier López</h2>
                    <p>Jefe de Seguridad</p>
                    <a href="#" class="view-profile" data-id="javier-lopez">Ver perfil</a>
                </div>
            </div>

            <!-- Fondo oscuro detrás del modal -->
            <div class="modal-overlay"></div>

            <!-- Contenedor de perfil (Modal) -->
            <div class="profile">
                <button class="close-modal">Cerrar</button>
                <div class="profile-header">
                    <img src="/placeholder.svg?height=150&width=150" alt="" class="profile-image">
                    <div>
                        <h1 class="profile-name"></h1>
                        <p class="profile-title"></p>
                    </div>
                </div>
                <div class="profile-bio"></div>
                <div class="profile-skills">
                    <h3>Habilidades y Experiencia</h3>
                    <div class="skills-list"></div>
                </div>
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