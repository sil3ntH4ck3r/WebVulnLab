<?php
session_start();
if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    // Read the file that contains the usernames and passwords
    $lines = file('users.txt');
	var_dump($lines);
    $login_successful = false;
    foreach ($lines as $line) {
        list($stored_username, $stored_password) = explode(',', $line);
        if ($username == $stored_username && $password == trim($stored_password)){
            // Login successful
            $_SESSION['username'] = $username;
            $login_successful = true;
            break;
        }
    }
    if ($login_successful) {
        echo 'Login successful';
    } else {
        echo 'Login failed';
    }
}
?>
