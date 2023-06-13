<!DOCTYPE html>
<html>
<head>
    <title>LaTeX Injection</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        form {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            color: #000;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        form h2 {
            margin: 0 0 20px;
            padding: 0;
            text-align: center;
            font-size: 24px;
        }

        form label {
            display: block;
            font-size: 16px;
            margin-bottom: 5px;
            color: #000;
        }

        form select,
        form input[type="text"],
        form textarea {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            color: #000;
            border: 1px solid #000;
            border-radius: 5px;
            resize: vertical;
        }

        form input[type="submit"] {
            background-color: #4CAF50;
            color: #fff;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        form input[type="submit"]:hover {
            background-color: #45a049;
        }

        p {
            text-align: center;
            font-size: 18px;
            margin-top: 20px;
        }

        a {
            color: #4CAF50;
            text-decoration: none;
        }
        footer {
            background-color: #333;
            color: #fff;
            padding: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <h1>Genere PDFs con LaTeX</h1>
    </header>

    <form action="index.php" method="POST">
        <h2>Escribe la estructura del documento LaTeX:</h2>
        <label for="doctype">Tipo de documento:</label>
        <select id="doctype" name="doctype">
            <option value="article">Artículo</option>
            <option value="report">Reporte</option>
            <option value="book">Libro</option>
            <!-- Agrega más opciones según tus necesidades -->
        </select><br><br>

        <label for="title">Título:</label>
        <input type="text" id="title" name="title" placeholder="Ingresa el título">
        <br><br>
        <label for="author">Autor:</label>
        <input type="text" id="author" name="author" placeholder="Ingresa el autor">
        <br><br>

        <label for="input">Escribe el contenido LaTeX:</label><br>
        <textarea id="input" name="input" rows="10" cols="50" placeholder="Ingresa el contenido LaTeX"></textarea><br>
        <input type="submit" value="Generar">
    </form>

    <?php
    if (isset($_POST['input'])) {
        $doctype = $_POST['doctype'];
        $title = $_POST['title'];
        $author = $_POST['author'];
        $input = $_POST['input'];

        // Determinar la plantilla según el tipo de documento seleccionado
        $template = '';
        if ($doctype === 'article') {
            $template = <<<EOT
            \\documentclass{article}
            \\title{{$title}}
            \\author{{$author}}
            \\date{\\today}

            \\usepackage{amsmath}

            \\begin{document}
            \\maketitle

            $input

            \\end{document}
            EOT;
        } elseif ($doctype === 'report') {
            $template = <<<EOT
            \\documentclass{report}
            \\title{{$title}}
            \\author{{$author}}
            \\date{\\today}

            \\begin{document}
            \\maketitle

            $input

            \\end{document}
            EOT;
        } elseif ($doctype === 'book') {
            $template = <<<EOT
            \\documentclass{book}
            \\title{{$title}}
            \\author{{$author}}
            \\date{\\today}

            \\begin{document}
            \\frontmatter
            \\maketitle

            \\tableofcontents

            \\mainmatter
            $input

            \\end{document}
            EOT;
        }

        // Generar el archivo LaTeX
        if ($template !== '') {
            $latexContent = $template;

            // Guardar archivo LaTeX
            $filename = uniqid('latex_');
            $texFilePath = "/var/www/html/{$filename}.tex";
            $pdfFilePath = "/var/www/html/{$filename}.pdf";
            file_put_contents($texFilePath, $latexContent);

            // Ejecutar comando LaTeX
            $command = "pdflatex -halt-on-error -interaction=batchmode -output-directory /var/www/html/ $texFilePath";
            exec($command, $output, $returnVar);

            if ($returnVar === 0 && file_exists($pdfFilePath)) {
                echo '<p>El documento LaTeX se generó correctamente. <a href="' . $filename . '.pdf" target="_blank">Descargar PDF</a></p>';
            } else {
                echo '<p>Error al generar el documento LaTeX o el archivo PDF no existe.</p>';
            }
        } else {
            echo '<p>Se ha seleccionado un tipo de documento inválido.</p>';
        }
    }
    ?>
</body>
<footer>
<p xmlns:cc="http://creativecommons.org/ns#" xmlns:dct="http://purl.org/dc/terms/"><a property="dct:title" rel="cc:attributionURL" href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev">WebVulnLab</a> by <a rel="cc:attributionURL dct:creator" property="cc:attributionName" href="https://github.com/sil3ntH4ck3r">sil3nth4ck3r</a> is licensed under <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1" target="_blank" rel="license noopener noreferrer" style="display:inline-block;">CC BY-NC-SA 4.0
</footer>
</html>
