<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Preview Service</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 1em;
        }
        main {
            padding: 2em;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        input[type="text"] {
            width: 100%;
            max-width: 500px;
            padding: 0.5em;
            margin-bottom: 1em;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 0.5em 1em;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        #preview {
            border: 1px solid #ccc;
            padding: 1em;
            margin-top: 1em;
            width: 100%;
            max-width: 500px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Link Preview Service</h1>
    </header>
    <main>
        <p>Welcome to our Link Preview Service! Enter a URL below to see a preview of the content.</p>
        <form action="index.php" method="get">
            <label for="url">URL:</label>
            <input type="text" id="url" name="url">
            <input type="submit" value="Fetch Preview">
        </form>
        <?php
        if (isset($_GET['url'])) {
            $url = $_GET['url'];
            $content = file_get_contents($url);
            echo '<div id="preview">' . $content . '</div>';
        }
        ?>
    </main>
</body>
</html>