<!DOCTYPE html>
<html>
<head>
    <title>Padding Oracle Attack</title>
    <meta charset="utf-8">
    <?php
      $conexion = mysqli_connect("db", "usuario", "contraseña", "database");
      if ($conexion) {
        $conexion->set_charset("utf8");
      }
      $sql = "SELECT * FROM usuarios";
      $resultado = mysqli_query($conexion, $sql);
      if (!$resultado) {
      die("Error al ejecutar la consulta: " . mysqli_error($conexion));
    }
    ?>
    <link href="main.css" rel="stylesheet" />
    <style>
        

.sidebar {
  width: 300px;
  background-color: #fff;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  padding: 20px;
  margin-right: 20px;
}

.sidebar h2 {
  margin-top: 0;
}

.sidebar ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

.sidebar ul li {
  margin-bottom: 10px;
}

.sidebar ul li a {
  color: #333;
  text-decoration: none;
  font-weight: bold;
}

.sidebar ul li a:hover {
  color: #555;
}

.main {
  flex: 1;
  background-color: #fff;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  padding: 20px;
}

.main h2 {
  margin-top: 0;
}

.chart {
  display: flex;
  justify-content: center;
  margin-top: 30px;
}

.bar {
  width: 20px;
  background-color: blue;
  margin-right: 10px;
}

.chart-container {
  border: 1px solid #ccc;
  background-color: #f9f9f9;
}

.buttons {
  margin-bottom: 20px;
}

.buttons button {
  color: #fff;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  margin-right: 10px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.primary-button {
  background-color: #333;
}

.primary-button:hover {
  background-color: #555;
}

.secondary-button {
  background-color: #777;
}

.secondary-button:hover {
  background-color: #999;
}

.tertiary-button {
  background-color: #ccc;
  color: #333;
}

.tertiary-button:hover {
  background-color: #eee;
}

.tables {
  margin-bottom: 20px;
}

table {
  width: 100%;
  border-collapse: collapse;
  border: 1px solid #ccc;
}

thead {
  background-color: #f2f2f2;
}

th, td {
  padding: 10px;
  text-align: left;
}

th {
  font-weight: bold;
}
canvas {
  display: block;
  margin: 0 auto;
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

  <br><br><br>
    <div class="container">
    <div class="main">
      <h2>Admin Panel</h2>
      <div class="charts">
        <h3>Gráfica</h3>
        <div class="chart-container">
          <canvas id="grafica"></canvas>
        </div>
      </div>
      <script>
            const canvas = document.getElementById('grafica');
            const ctx = canvas.getContext('2d');
            const data = [40, 70, 20, 90];
            const barWidth = 50;
            const spacing = 20;

            for (let i = 0; i < data.length; i++) {
            const x = i * (barWidth + spacing);
            const height = (canvas.height / 100) * data[i];
            const y = canvas.height - height;
            ctx.fillStyle = 'black';
            ctx.fillRect(x, y, barWidth, height);
            }

        </script>
      <div class="tables">
        <h3>Tabla</h3>
        <table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Usuario</th>
      <th>Contraseña</th>
      <th>Email</th>
      <th>Fecha registro</th>
    </tr>
  </thead>
  <tbody>
    <?php
    // Recorrer los resultados de la consulta
    while($row = $resultado->fetch_assoc()) {
    ?>
    <tr>
      <td><?php echo $row["id"]; ?></td>
      <td><?php echo $row["nombre"]; ?></td>
      <td><?php echo $row["contraseña"]; ?></td>
      <td><?php echo $row["email"]; ?></td>
      <td><?php echo $row["fecha_registro"]; ?></td>
    </tr>
    <?php
    }
    ?>
  </tbody>
</table>
      </div>
    </div>
  </div>
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
	
