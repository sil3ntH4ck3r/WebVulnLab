<!DOCTYPE html>
<html>
<head>
	<title>Formulario de inicio de sesión vulnerable a XXE</title>
</head>
<body>
	<form method="POST" action="login.php">
		<label for="username">Nombre de usuario:</label>
		<input type="text" id="username" name="username" required>
		<br>
		<label for="password">Contraseña:</label>
		<input type="password" id="password" name="password" required>
		<br>
		<input type="submit" value="Iniciar sesión">
	</form>
</body>
</html>

