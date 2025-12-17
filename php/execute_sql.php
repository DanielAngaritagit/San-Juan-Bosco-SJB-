<?php
require_once __DIR__ . '/conexion.php';

$sql = file_get_contents(__DIR__ . '/../scripts/rename_table.sql');

try {
    $pdo->exec($sql);
    echo "Tabla renombrada exitosamente.";
} catch (PDOException $e) {
    echo "Error al renombrar la tabla: " . $e->getMessage();
}
?>