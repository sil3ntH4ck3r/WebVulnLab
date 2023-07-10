<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];

        define('LDAP_DC', 'dc=ldapinjection,dc=local');
        define('LDAP_DN', "cn=admin," . LDAP_DC);
        define('LDAP_PASS', 'admin');

        // Realizar la autenticación en el servidor LDAP
        $ldapconn = ldap_connect("ldap://ldap_server_v2:389");
        if ($ldapconn) {
            ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
            $ldapbind = ldap_bind($ldapconn, LDAP_DN, LDAP_PASS);
            if ($ldapbind) {
                echo "Autenticación exitosa al servidor LDAP!<br>";
                
                // Realizar la búsqueda en el servidor LDAP

                $filter = '(&(uid=' . $username . ')(userPassword=' . $password . '))';
                $search = ldap_search($ldapconn, LDAP_DC, $filter);
                $entries = ldap_get_entries($ldapconn, $search);

                if ($entries['count'] > 0) {
                    // El usuario se autenticó correctamente
                    echo "Autenticación exitosa!";
                    // Realizar acciones después de la autenticación exitosa
                } else {
                    // La autenticación falló
                    echo "Autenticación fallida!";
                    // Realizar acciones después de la autenticación fallida
                }

                // Realizar otras operaciones después de la autenticación

            } else {
                echo "Autenticación fallida al servidor LDAP!";
                // Realizar acciones después de la autenticación fallida
            }
        } else {
            echo "Conexión al servidor LDAP fallida!";
            // Realizar acciones después de la conexión fallida
        }
    }
?>


<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
</head>
<body>
  <h1>Login</h1>
  <form method="POST" action="">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required><br>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br>

    <button type="submit">Login</button>
  </form>
</body>
</html>