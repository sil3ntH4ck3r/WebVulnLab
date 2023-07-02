<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="A la hora de escribir una meta descripción, mantenla entre 140 y 160 caracteres para que Google pueda mostrar tu mensaje completo. ¡No olvides incluir tu palabra clave!">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSRF</title>
    <style>
        /* Estilo general */
        html, body {
            height: 100%;
            margin: 0;
        }
        .wrapper {
            min-height: 100%;
            display: flex;
            flex-direction: column;
        }

        body {
            font-family: 'Roboto', sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: #444;
        }

        #container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
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
        }

        /* Contenido principal */
        main {
            padding: 50px 0;
            text-align: center;
            flex: 1;
            padding-bottom: 80px; /* Ajusta el valor según la altura del footer */
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        input[type="text"] {
            width: 100%;
            max-width: 500px;
            padding: 10px;
            margin-bottom: 20px;
            font-size: 1.1em;
            border-radius: 3px;
            border: 1px solid #ccc;
        }

        input[type="submit"] {
            display: block;
            margin: 0 auto;
            padding: 10px 30px;
            font-size: 1.1em;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 3px;
            transition: background-color 0.3s;
            margin-bottom: 4em; /* agregar un margen inferior */
        }

        input[type="submit"]:hover {
            background-color: #555;
        }

        #preview {
            border: 1px solid #ccc;
            padding: 1em;
            margin-top: 2em; /* aumentar el margen superior */
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            text-align: center;
        }

        #preview h2 {
            text-align: left;
            margin-bottom: 0.5em;
        }

        #preview p {
            text-align: left;
            margin-top: 0.5em;
        }

        footer {
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100%;
            background-color: #333;
            color: #fff;
            padding: 20px;
            text-align: center;
            margin-top: 100px; /* Añade margen superior */
        }
        .content {
    margin-top: 20px; /* Agrega margen superior */
    margin-bottom: 20px; /* Agrega margen inferior */
}

    .content iframe {
        width: 100%;
        max-height: 1200px; /* Ajusta el valor según tus necesidades */
        border: none;
    }

    .content iframe.sandboxed {
        height: auto;
    }
    </style>
</head>
<body>
<div class="wrapper">
    <header>
        <h1>Busque su pedido rápidamente</h1>
    </header>

    <main>
    <?php
require_once 'vendor/autoload.php';

$id = "";
if (isset($_POST["id"])) {
    $id = $_POST["id"];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $strippedId = strip_tags($id);
    $message = "La ID proporcionada (" . $strippedId . ") no ha sido encontrada";
}

$t = '<!DOCTYPE html>
<html>
    <body>
        <form action="" method="post">
            Ingrese la ID del pedido:<br>
            <input type="text" name="id" value="" required>
            <input type="submit" value="Buscar">
        </form>
        <h2>' . $message . '</h2>
    </body>
</html>';

$loader = new Twig_Loader_Array(array('index' => $t));
$twig = new Twig_Environment($loader);

try {
    echo $twig->render('index', array('name' => 'Fabien'));
} catch (Exception $e) {
    echo '<!DOCTYPE html>
    <html>
        <body>
            <form action="" method="post">
                Ingrese la ID del pedido:<br>
                <input type="text" name="id" value="">
                <input type="submit" value="Buscar">
            </form>
            <h2>' . $message . '</h2>
        </body>
    </html>';
}
?>
</main>
<footer>
    <p xmlns:cc="http://creativecommons.org/ns#" xmlns:dct="http://purl.org/dc/terms/">
        <a property="dct:title" rel="cc:attributionURL" href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev">WebVulnLab</a> by
        <a rel="cc:attributionURL dct:creator" property="cc:attributionName" href="https://github.com/sil3ntH4ck3r">sil3nth4ck3r</a>
        is licensed under <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1" target="_blank" rel="license noopener noreferrer" style="display:inline-block;">CC BY-NC-SA 4.0
    </p>
</footer>
</div>
</body>
</html>