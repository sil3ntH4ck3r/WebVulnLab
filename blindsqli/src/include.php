<?php
// Conexión a la base de datos
$db = mysqli_connect("db", "usuario", "contraseña", "database");

// Crear tabla productos
mysqli_query($db, "CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL
)");

// Insertar un producto en la tabla productos
mysqli_query($db, "INSERT INTO productos (nombre, descripcion, precio) VALUES ('Producto 1', 'Descripción del producto 1', 9.99)");
?>
