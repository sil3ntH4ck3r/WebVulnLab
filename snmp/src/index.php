<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>NexusVPS Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="background-overlay"></div>
    <div class="container">
        <header>
            <h1>Nexus<span>VPS</span></h1>
            <p>Innovación en Servidores Virtuales Privados</p>
        </header>

        <main>
            <!-- Panel de información -->
            <section class="info-panel">
                <div class="info-card" id="system-card">
                    <div class="info-icon">
                        <!-- Icono personalizado -->
                    </div>
                    <div class="info-content">
                        <h3>Sistema</h3>
                        <p id="system-description">Cargando...</p>
                    </div>
                </div>
                <div class="info-card" id="uptime-card">
                    <div class="info-icon">
                        <!-- Icono personalizado -->
                    </div>
                    <div class="info-content">
                        <h3>Tiempo Activo</h3>
                        <p id="system-uptime">Cargando...</p>
                    </div>
                </div>
                <div class="info-card" id="cpu-card">
                    <div class="info-icon">
                        <!-- Icono personalizado -->
                    </div>
                    <div class="info-content">
                        <h3>Carga CPU</h3>
                        <p id="cpu-load">Cargando...</p>
                    </div>
                </div>
            </section>

            <!-- Visualización de Recursos -->
            <section class="resource-visualization">
                <canvas id="ram-usage-canvas" width="200" height="200"></canvas>
                <div class="resource-info">
                    <h3>Uso RAM</h3>
                    <p id="ram-percentage">0%</p>
                </div>
            </section>

            <!-- Interfaces de Red -->
            <section class="network-interfaces">
                <h3>Interfaces de Red</h3>
                <div class="interface-list" id="interface-list">
                    <!-- Las interfaces se cargarán aquí -->
                </div>
            </section>
        </main>

        <footer>
            <p>&copy; 2023 NexusVPS. Todos los derechos reservados.</p>
        </footer>
    </div>

    <script src="script.js"></script>
</body>
</html>