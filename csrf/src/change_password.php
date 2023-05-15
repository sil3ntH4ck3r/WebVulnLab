<?php
session_start();
if (isset($_GET['new_password']) && isset($_GET['confirm_password'])) {
    $new_password = $_GET['new_password'];
    $confirm_password = $_GET['confirm_password'];
    if ($new_password == $confirm_password) {
        // Read the username from the session
        $username = $_SESSION['username'];
        // Read the file that contains the usernames and passwords
        $lines = file('users.txt');
        foreach ($lines as $i => $line) {
            list($stored_username, $stored_password) = explode(',', $line);
            if ($username == $stored_username) {
                // Change the user's password
                echo "Change the user's password";
                $lines[$i] = $username . ',' . trim($new_password) . "\n";
                break;
            }
        }
        // Overwrite the file with the new passwords
        file_put_contents('users.txt', implode('', $lines));
    } else {
        echo 'Passwords do not match';
    }
}
echo "Has de iniciar sesion";
?>