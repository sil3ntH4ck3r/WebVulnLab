<?php
// index.php
$page = $_GET['show'];

if (isset($page)) {
    include($page);
} else {
    include('home.php');
}
?>