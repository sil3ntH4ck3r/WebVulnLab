<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>AWS Lambda Abuse</title>
<style>
  body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
  }
  header, footer {
    background: #3fa3f2;
    color: #fff;
    padding: 20px;
    text-align: center;
  }
  header h1, footer p {
    margin: 0;
  }
  nav {
    background: #eee;
    padding: 10px;
    text-align: center;
  }
  nav a {
    color: #333;
    text-decoration: none;
    margin: 0 10px;
    font-weight: bold;
  }
  .container {
    max-width: 800px;
    margin: 30px auto;
    padding: 0 15px;
  }
  h2 {
    border-bottom: 2px solid #3fa3f2;
    padding-bottom: 5px;
  }
  .services, .horarios, .lambda-invoke {
    margin-bottom: 40px;
  }
  .services .service-item {
    margin-bottom: 20px;
  }
  .horarios button, .lambda-invoke button {
    background: #3fa3f2;
    border: none;
    padding: 10px 15px;
    color: #fff;
    cursor: pointer;
    border-radius: 3px;
  }
  .horarios button:hover, .lambda-invoke button:hover {
    background: #2e82c4;
  }
  .horarios #horariosResultado, .lambda-invoke #lambdaResultado {
    background: #f0f0f0;
    padding: 10px;
    white-space: pre-wrap;
    margin-top: 10px;
    border-radius: 3px;
  }
  form {
    margin-top: 10px;
  }
  form input[type="text"] {
    padding: 5px;
    margin-right: 10px;
    border: 1px solid #ccc;
    border-radius: 3px;
  }
  footer {
    margin-top: 40px;
  }
</style>
<script>
function verHorarios() {
  var xhr = new XMLHttpRequest();
  xhr.open('GET', 'invoke.php?function=horarios', true);
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4) {
    var res = document.getElementById('horariosResultado');
    if (xhr.status === 200) {
      var response = xhr.responseText.replace(/null/g, '');
      res.innerHTML = response;
    } else {
      res.textContent = 'Error al obtener los horarios.';
    }
  }
  };
  xhr.send();
}

function invocarLambda() {
  var fn = document.getElementById('functionName').value || 'horarios';
  var xhr = new XMLHttpRequest();
  xhr.open('GET', 'invoke.php?function=' + encodeURIComponent(fn), true);
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4) {
      var res = document.getElementById('lambdaResultado');
      if (xhr.status === 200) {
        res.textContent = xhr.responseText;
      } else {
        res.textContent = 'Error al invocar la función.';
      }
    }
  };
  xhr.send();
  return false; // prevenir el envío del formulario
}
</script>
</head>
<body>
<header>
  <h1>Dentista SonrisaPerfecta</h1>
</header>

<nav>
  <a href="#servicios">Servicios</a>
  <a href="#horarios">Horarios</a>
  <a href="#contacto">Contacto</a>
</nav>

<div class="container">
  <section class="intro">
    <p>Bienvenido a Dentista SonrisaPerfecta, tu clínica dental de confianza. Ofrecemos servicios profesionales para mantener tu sonrisa sana y brillante.</p>
  </section>

  <section id="servicios" class="services">
    <h2>Servicios</h2>
    <div class="service-item">
      <h3>Limpieza Dental</h3>
      <p>Una limpieza profunda realizada por expertos para eliminar la placa y el sarro, manteniendo tus dientes sanos.</p>
    </div>
    <div class="service-item">
      <h3>Empaste</h3>
      <p>Tratamiento de caries y daños en el esmalte dental, restaurando la forma y funcionalidad del diente.</p>
    </div>
    <div class="service-item">
      <h3>Ortodoncia</h3>
      <p>Corrección de la posición de tus dientes para una sonrisa alineada y estéticamente armoniosa.</p>
    </div>
  </section>

  <section id="horarios" class="horarios">
    <h2>Horarios Disponibles</h2>
    <p>Consulta nuestros horarios actualizados. Se obtienen dinámicamente a través de nuestro servicio interno.</p>
    <button onclick="verHorarios()">Ver Horarios</button>
    <div id="horariosResultado"></div>
  </section>
</div>

<footer>
  <p id="contacto">Contacto: info@aws.local | +34 900 123 456</p>
  <p xmlns:cc="http://creativecommons.org/ns#" xmlns:dct="http://purl.org/dc/terms/" style="text-align: center";>
            <a property="dct:title" rel="cc:attributionURL" href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev">WebVulnLab</a> by 
            <a rel="cc:attributionURL dct:creator" property="cc:attributionName" href="https://github.com/sil3ntH4ck3r">sil3nth4ck3r</a> is licensed under 
            <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1" target="_blank" rel="license noopener noreferrer" style="display:inline-block;">CC BY-NC-SA 4.0</a>
            <br>
        </p>
</footer>
</body>
</html>
