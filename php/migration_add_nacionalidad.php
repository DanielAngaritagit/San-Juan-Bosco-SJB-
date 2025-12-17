<?php
require_once 'conexion.php';

echo "<!DOCTYPE html><html lang=\"es\"><head><meta charset=\"UTF-8\"><title>Migración - Añadir Nacionalidad</title><style>body { font-family: sans-serif; padding: 20px; } .success { color: green; font-weight: bold; } .error { color: red; font-weight: bold; } .info { color: blue; }</style></head><body>";
echo "<h1>Añadiendo columna 'nacionalidad' a la tabla 'tab_acudiente'...</h1>";

try {
    // Verificar si la columna ya existe
    $check_column_sql = "SELECT column_name FROM information_schema.columns WHERE table_name='tab_acudiente' AND column_name='nacionalidad'";
    $stmt_check = $pdo->query($check_column_sql);
    $column_exists = $stmt_check->fetch();

    if ($column_exists) {
        echo "<p class=\"info\">La columna 'nacionalidad' ya existe en la tabla 'tab_acudiente'. No se necesita ninguna acción.</p>";
    } else {
        // Si no existe, la añadimos
        $sql = "ALTER TABLE tab_acudiente ADD COLUMN nacionalidad VARCHAR(100);";
        $pdo->exec($sql);
        echo "<p class=\"success\">¡Migración exitosa!</p>";
        echo "<p>La columna 'nacionalidad' ha sido añadida a la tabla 'tab_acudiente'.</p>";
    }
    echo "<p class=\"info\">Por favor, elimina este archivo del servidor por seguridad.</p>";

} catch (PDOException $e) {
    echo "<p class=\"error\">Error durante la migración: " . $e->getMessage() . "</p>";
}

echo "</body></html>";
?>
