<?php
require_once 'config.php';

try {
    $conn = new PDO("pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Renombrar tabla
    $conn->exec("ALTER TABLE tab_pqrs RENAME TO tab_pqrsf");
    echo "Tabla 'tab_pqrs' renombrada a 'tab_pqrsf'.<br>";

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>