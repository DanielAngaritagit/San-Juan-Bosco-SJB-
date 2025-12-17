<?php
require_once 'conexion.php';

echo "<!DOCTYPE html><html lang=\"es\"><head><meta charset=\"UTF-8\"><title>Migración - Arreglar Tabla Acudiente</title><style>body { font-family: sans-serif; padding: 20px; } .success { color: green; font-weight: bold; } .error { color: red; font-weight: bold; } .info { color: blue; }</style></head><body>";
echo "<h1>Arreglando la tabla 'tab_acudiente'...</h1>";

function add_column_if_not_exists($pdo, $table, $column, $type) {
    try {
        $check_column_sql = "SELECT column_name FROM information_schema.columns WHERE table_name=:table AND column_name=:column";
        $stmt_check = $pdo->prepare($check_column_sql);
        $stmt_check->execute(['table' => $table, 'column' => $column]);
        $column_exists = $stmt_check->fetch();

        if ($column_exists) {
            echo "<p class=\"info\">La columna '{$column}' ya existe en la tabla '{$table}'. No se necesita ninguna acción.</p>";
        } else {
            $sql = "ALTER TABLE {$table} ADD COLUMN {$column} {$type};";
            $pdo->exec($sql);
            echo "<p class=\"success\">¡Columna '{$column}' añadida exitosamente a la tabla '{$table}'!</p>";
        }
    } catch (PDOException $e) {
        echo "<p class=\"error\">Error al intentar añadir la columna '{$column}': " . $e->getMessage() . "</p>";
    }
}

// Añadir las columnas faltantes
add_column_if_not_exists($pdo, 'tab_acudiente', 'nacionalidad', 'VARCHAR(100)');
add_column_if_not_exists($pdo, 'tab_acudiente', 'profesion', 'VARCHAR(100)');
add_column_if_not_exists($pdo, 'tab_acudiente', 'estado_civil', 'VARCHAR(50)');

echo "<hr><p class=\"info\">Proceso de migración completado. Por favor, elimina este archivo del servidor por seguridad.</p>";
echo "</body></html>";
?>