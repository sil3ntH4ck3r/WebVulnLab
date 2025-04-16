<?php
include 'functions.php';
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    deleteTicket($id);
}
header("Location: index.php");
exit;
?>