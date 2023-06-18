<?php

// Funcion para validar si tiene un ;. En caso de que lo tenga, la nota no sera valida
	/* function validate() {
		if (!$this->isValid) {
			if (!empty($this->note) && strlen($this->note) > 5 && strpos($this->note, ';') === false) {
				$this->isValid = true;
			} else {
				$this->output = "La nota no es válida.";
				$this->isValid = false;
			}
		}
	}*/

	class noteSystem {
		public $note = ""; // Contenido de la nota esta en blanco
		public $isValid = false; // Por defecto la nota no es valida
		public $output = ""; // Por defecto no hay ningun contenido que mostrar
	
		function validate() {
			if (!$this->isValid) {
				if (!empty($this->note) && strlen($this->note) > 5) { // Valida que la nota al menos tenga 6 caracteres
					$this->isValid = true; // En el caso de que sea asi, la nota sera valida
				} else { // Sino, no sera valida
					$this->output = "La nota no es válida. Asegúrate de que no esté vacía y tenga al menos 6 caracteres."; // Imprime mensaje de que la nota no es valida
					$this->isValid = false; // Configura la variable, para que la nota no sea valida
				}
			}
		}
	
		function save() {
			if ($this->isValid) { // Solo gardara la nota si el valida
				$this->output = shell_exec("echo Nota: $this->note >> note.txt"); // El conteneido que se ha introducido se guarda en el archivo note.txt
				$this->output = file_get_contents("note.txt"); // Muestra el contenido de las notas anteriores
			}
		}
	}
	
	if (isset($_POST['obj'])) {
		$noteSystem = unserialize($_POST['obj']); // Unserializa lo que viaja por POST en el campo obj (object)
		$noteSystem->validate(); // Valida que lo que ha deserializado sea correcto
		$noteSystem->save(); // Y la guarda en el caso de que la validacion haya sido existosa
	}

	if (isset($_POST['clear'])) {
		file_put_contents('note.txt', ''); // Limpia en contenido del archivo note.txt, en el caso de que se haya presionado el boton de limpiar
	}
	?>
	
	<!DOCTYPE html>
<html>
<head>
	<title>Note System</title>
	<style>
		/* Estilo general */
		body {
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
		}

		nav ul li a:hover {
		  background-color: #555;
		}

		/* Sección de servicios */
		.services {
		  background-color: #f7f7f7;
		  padding: 50px 0;
		  text-align: center;
		}

		.services h2 {
		  font-size: 2.5em;
		  margin-bottom: 50px;
		}

		.service {
		  display: inline-block;
		  margin-right: 30px;
		  margin-bottom: 30px;
		  width: 300px;
		  text-align: left;
		}

		.service i {
		  font-size: 3em;
		  margin-bottom: 20px;
		  color: #444;
		}

		.service h3 {
		  font-size: 1.5em;
		  margin-bottom: 20px;
		}

		.service p {
		  font-size: 1em;
		  line-height: 1.6;
		}

		/* Sección "Sobre Nosotros" */
		.about {
		  padding: 50px 0;
		  text-align: center;
		}

		.about h2 {
		  font-size: 2.5em;
		  margin-bottom: 50px;
		}

		.about-content {
		  max-width: 800px;
		  margin: 0 auto;
		  text-align: left;
		}

		.about-content p {
		  font-size: 1.1em;
		  line-height: 1.6;
		  margin-bottom: 30px;
		}

		/* Sección de contacto */
		.contact {
		  background-color: #f7f7f7;
		  padding: 50px 0;
		  text-align: center;
		}

		.contact h2 {
		  font-size: 2.5em;
		  margin-bottom: 50px;
		}

		.contact-form {
		  max-width: 600px;
		  margin: 0 auto;
		}

		.contact-form input,
		.contact-form textarea {
		  display: block;
		  width: 100%;
		  padding: 10px;
		  margin-bottom: 20px;
		  font-size: 1.1em;
		  border-radius: 3px;
		  border: 1px solid #ccc;
		}

		.contact-form textarea {
		  height: 150px;
		}

		.contact-form button {
		  display: block;
		  margin: 0 auto;
		  padding: 10px 30px;
		  font-size: 1.1em;
		  background-color: #333;
		  color: #fff;
		  border: none;
		  border-radius: 3px;
		  transition: background-color 0.3s;
		}

		footer {
			background-color: #333;
			color: #fff;
			padding: 20px;
			text-align: center;
			position: fixed;
			left: 0;
			bottom: 0;
			width: 100%;
		}
		.boton {
			background-color: transparent;
			color: #272727;
			border: 1px solid #272727;
			padding: 8px 16px;
			text-align: center;
			text-decoration: none;
			display: inline-block;
			font-size: 14px;
			border-radius: 4px;
			transition: background-color 0.3s, color 0.3s;
		}
		
		.boton:hover {
			background-color: #272727;
			color: #fff;
		}

		.note-output textarea {
			display: block;
			width: 100%;
			height: 200px; /* Altura fija de la textarea */
			padding: 10px;
			margin-top: 10px;
			font-size: 1em;
			line-height: 1.6;
			border-radius: 3px;
			border: 1px solid #ccc;
			resize: none; /* Deshabilita la capacidad de redimensionamiento */
		}

	</style>
	<script>
		function submit_form() {
			var note = document.forms["noteform"].note.value;
			var object = "O:10:\"noteSystem\":1:{s:4:\"note\";s:" + note.length + ":\"" + note + "\";}";
			document.forms["noteform"].obj.value = object;
			document.getElementById('noteform').submit();
		}
	</script>
</head>
<body>
	<header>
		<h1>Sistema de guardado de notas</h1>
	</header>

	<div class="contact">
	<h2>Guarda Notas</h2>
	<div class="contact-form">
			<form method="POST" action="/" id="noteform" onsubmit="event.preventDefault(); submit_form();">
				<input type="text" name="note" placeholder="Nota" required>
				<input type="hidden" name="obj">
				<input class="boton" type="submit" value="Guardar Nota">
			</form>
			<form method="POST" action="/">
				<input class="boton" type="submit" name="clear" value="Borrar Notas">
			</form>
			<div class="note-output">
				<textarea readonly><?php echo isset($noteSystem) ? $noteSystem->output : ''; ?></textarea>
			</div>
		</div>
	</div>



	<footer>
	<p xmlns:cc="http://creativecommons.org/ns#" xmlns:dct="http://purl.org/dc/terms/"><a property="dct:title" rel="cc:attributionURL" href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev">WebVulnLab</a> by <a rel="cc:attributionURL dct:creator" property="cc:attributionName" href="https://github.com/sil3ntH4ck3r">sil3nth4ck3r</a> is licensed under <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1" target="_blank" rel="license noopener noreferrer" style="display:inline-block;">CC BY-NC-SA 4.0
	</footer>
</body>
</html>
