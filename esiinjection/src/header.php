<?php
header('X-ESI-Enabled: yes');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ESI Injection</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: "Inter", "System UI", sans-serif;
            background-color: #F5F7FA;
            margin: 0;
            padding: 16px;
        }
        .container {
            max-width: 1200px;
            margin: auto;
        }
        .header {
            background-color: #1976D2;
            color: white;
            padding: 16px;
            text-align: center;
        }
        .nav {
            margin-top: 16px;
            margin-bottom: 16px;
        }
        .nav a {
            margin-right: 16px;
            text-decoration: none;
            color: #1976D2;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Efficient Support Interface</h1>
        </div>
        <div class="nav">
            <a href="index.php">Ticket List</a>
            <a href="create_ticket.php">Create Ticket</a>
        </div>
