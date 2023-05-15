<?php
class User {
    public $username;
    public $isAdmin;

    public function __construct($username, $isAdmin) {
        $this->username = $username;
        $this->isAdmin = $isAdmin;
    }

    public function displayInfo() {
        echo "Username: " . $this->username . "<br>";
        echo "Is admin: " . ($this->isAdmin ? "Yes" : "No") . "<br>";
    }
}

if (isset($_GET['serialized'])) {
    $unserialized = unserialize(base64_decode($_GET['serialized']));
    if ($unserialized instanceof User) {
        $unserialized->displayInfo();
    } else {
        echo "Invalid data.";
    }
} else {
    echo "No data provided.";
}
?>
