<?php
require_once 'config.php';

try {
    $conn = new PDO("pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Renombrar columna 'pqr_about_category' a 'pqrsf_about_category'
    $stmt = $conn->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'tab_pqrsf' AND column_name = 'pqr_about_category'");
    if ($stmt->fetch()) {
        $conn->exec("ALTER TABLE tab_pqrsf RENAME COLUMN pqr_about_category TO pqrsf_about_category");
        echo "Columna 'pqr_about_category' renombrada a 'pqrsf_about_category'.<br>";
    }

    // Renombrar columna 'pqr_about_id' a 'pqrsf_about_id'
    $stmt = $conn->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'tab_pqrsf' AND column_name = 'pqr_about_id'");
    if ($stmt->fetch()) {
        $conn->exec("ALTER TABLE tab_pqrsf RENAME COLUMN pqr_about_id TO pqrsf_about_id");
        echo "Columna 'pqr_about_id' renombrada a 'pqrsf_about_id'.<br>";
    }

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>