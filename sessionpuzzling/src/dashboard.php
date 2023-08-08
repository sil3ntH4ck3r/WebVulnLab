<?php

  $conexion = mysqli_connect("db", "usuario", "contraseña", "database");
  if ($conexion) {
      $conexion->set_charset("utf8");
  }

  // Verificar si la conexión fue exitosa
  if (!$conexion) {
      die('Error al conectar a la base de datos: ' . mysqli_connect_error());
  }

  //$cookieUser = null;
    if (isset($_COOKIE["session_id"])) {
        $cookieUser = $_COOKIE["session_id"];
    }
    else{
      header("Location: index.php");
      exit;
    }

    // Obtener el nombre de usuario de la persona que tiene el session_id almacenado en la cookie
    if ($cookieUser) {
        $sql = "SELECT nombre FROM usuarios WHERE session_id = ? AND nombre = 'admin'";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $cookieUser);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $nombreUsuario = $row['nombre'];
            //echo "El nombre de usuario con el session_id " . $cookieUser . " es: " . $nombreUsuario;
        } else {
            echo "<script>alert('Has de ser usuario administrador para poder ver este sitio');</script>";
            exit;
        }
    } else {
      header("Location: index.php");
      exit;
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Panel de control</title>
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
    <style>
        header {
        background-color: #333;
        color: #fff;
        padding: 20px;
        text-align: center;
    }

    nav {
        background-color: #444;
        padding: 10px;
    }

    nav ul {
        list-style: none;
        margin: 0;
        padding: 0;
        text-align: center;
    }

    nav ul li {
        display: inline-block;
        margin-right: 20px;
    }

    nav ul li:last-child {
        margin-right: 0;
    }

    nav ul li a {
        color: #fff;
        text-decoration: none;
        padding: 10px;
        transition: background-color 0.3s;
    }

    nav ul li a:hover {
        background-color: #555;
    }

    header h1 {
        text-align: center;
    font-size: 3rem;
    margin-top: 1rem;
    }

    h1 {
        text-align: center;
        font-size: 3rem;
        margin-top: 1rem;
    }
    footer {
        position: absolute;
        bottom: 0;
        width: 100%;
        height: 50px;
        background-color: #333;
        color: #fff;
        padding: 20px;
        text-align: center;
    }

    body {
        margin: 0;
        padding: 0;
        font-family: sans-serif;
        background: #ffffff;
    }



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
        <nav>
            <div class="container">
                <h1 class="logo">Session Puzzling</h1>
                <ul class="menu">
                    <li><a href="http://sessionpuzzling.local/logout.php">Logout</a></li>
                    <li><a href="http://sessionpuzzling.local/perfil.php">Perfil</a></li>
                    <li><a href="http://sessionpuzzling.local/dashboard.php">Dashboard</a></li>
                </ul>
            </div>
        </nav>
    </header>

  <div class="container">
    <div class="main">
      <h2>Admin Panel</h2>
      <div class="charts">
        <h3>Grafica</h3>
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
      <th>ID Sesion</th>
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
      <td><?php echo $row["session_id"]; ?></td>
    </tr>
    <?php
    }
    ?>
  </tbody>
</table>
      </div>
    </div>
  </div>
</body>
	
