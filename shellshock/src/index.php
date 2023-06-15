<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shellshock Attack</title>
  <style>
    html,
    body {
      scroll-behavior: smooth;
      font-family: 'Roboto', sans-serif;
      font-size: 16px;
      line-height: 1.6;
      color: #444;
      margin: 0;
      padding: 0;
    }

    /* Encabezado */
    header {
      background-color: #333;
      color: #fff;
      padding: 20px;
      text-align: center;
    }

    header h1 {
      font-size: 3em;
      margin: 0;
      font-weight: 700;
    }

    /* Navegación */
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
      font-weight: 700;
    }

    nav ul li a:hover {
      background-color: #555;
    }

    /* Contenido principal */
main {
  padding: 40px;
}

main h2 {
  font-size: 2.5em;
  font-weight: 700;
  margin-bottom: 20px;
  text-align: center; /* Centrar el título */
}

.article {
  border: 1px solid #ccc; /* Añadir borde */
  padding: 20px; /* Añadir relleno */
  margin-bottom: 30px;
}

.article img {
  width: 100%;
  height: auto;
  margin-bottom: 10px;
}

.article h3 {
  font-size: 2em;
  font-weight: 700;
  margin-bottom: 10px;
}

/* Pie de página */
footer {
  background-color: #ffffff; /* Cambiar el color de fondo a blanco */
  color: #000000; /* Cambiar el color del texto a negro */
  padding: 20px;
  text-align: center;
}

    .subscribe {
      background-color: #f7f7f7;
      padding: 50px 0;
      text-align: center;
    }

    .subscribe h3 {
      font-size: 2.5em;
      margin-bottom: 50px;
      font-weight: 700;
    }

    .subscribe-form {
      max-width: 400px;
      margin: 0 auto;
    }

    .subscribe-form input[type="email"] {
      display: block;
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      font-size: 1em;
      border-radius: 3px;
      border: 1px solid #ccc;
    }

    .subscribe-form button {
      display: inline-block;
      padding: 10px 30px;
      font-size: 1em;
      background-color: #333;
      color: #fff;
      border: none;
      border-radius: 3px;
      transition: background-color 0.3s;
      font-weight: 700;
    }

    .subscribe-form button:hover {
      background-color: #555;
    }

    footer p {
      margin: 0;
    }
  </style>
</head>

<body>
  <header>
    <h1>Noticias al minuto</h1>
    <nav>
      <ul>
        <li><a href="#">Inicio</a></li>
        <li><a href="#">Política​</a></li>
        <li><a href="#">Deportes</a></li>
        <li><a href="#">Tecnología</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <section>
      <h2>Últimas noticias</h2>
      <div class="article">
        <h3>Investigadores descubren nueva especie de mariposa en la selva amazónica</h3>
        <p>Un equipo de científicos ha encontrado una nueva especie de mariposa en una expedición a la selva amazónica. Esta mariposa presenta patrones únicos en sus alas y su descubrimiento representa un hallazgo significativo para la comunidad científica y la conservación de la biodiversidad.</p>
      </div>
      <div class="article">
        <h3>Empresa de tecnología lanza dispositivo revolucionario para monitoreo de salud en tiempo real</h3>
        <p>Una empresa líder en tecnología ha lanzado un dispositivo portátil que permite a las personas monitorear su salud en tiempo real. El dispositivo utiliza sensores avanzados para medir parámetros como ritmo cardíaco, nivel de estrés y calidad del sueño. Con esta innovación, los usuarios pueden obtener datos precisos sobre su estado de salud y realizar un seguimiento de su bienestar de manera conveniente.</p>
      </div>
      <div class="article">
        <h3>Estudio revela beneficios sorprendentes del consumo moderado de chocolate negro</h3>
        <p>Según un estudio reciente, consumir chocolate negro de forma moderada puede tener varios beneficios para la salud. Los investigadores han encontrado que el chocolate negro con alto contenido de cacao puede ayudar a reducir la presión arterial, mejorar la función cerebral y tener efectos positivos en el estado de ánimo. Estos hallazgos han generado entusiasmo entre los amantes del chocolate y resaltan la importancia de elegir productos de alta calidad con un contenido de cacao significativo.</p>
      </div>
    </section>
  </main>

  <footer>
    <div class="subscribe">
      <h3>Suscríbase a nuestro boletín</h3>
      <form action="/cgi-bin/register.cgi" method="post">
        <div class="subscribe-form">
          <input type="email" name="email" id="email" placeholder="Ingrese su correo electrónico" required>
          <button type="submit">Suscríbase</button>
        </div>
      </form>
    </div>
    <p xmlns:cc="http://creativecommons.org/ns#" xmlns:dct="http://purl.org/dc/terms/"><a property="dct:title" rel="cc:attributionURL" href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev">WebVulnLab</a> by <a rel="cc:attributionURL dct:creator" property="cc:attributionName" href="https://github.com/sil3ntH4ck3r">sil3nth4ck3r</a> is licensed under <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1" target="_blank" rel="license noopener noreferrer" style="display:inline-block;">CC BY-NC-SA 4.0
  </footer>
</body>

</html>