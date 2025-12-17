<?php
require_once 'config.php';

try {
    $conn = new PDO("pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexión a la base de datos exitosa.";
} catch(PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
?>