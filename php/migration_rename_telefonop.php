<?php
require_once 'conexion.php';

echo "<!DOCTYPE html>";
echo "<html lang=\"es\">";
echo "<head>";
echo "    <meta charset=\"UTF-8\">";
echo "    <title>Migración de Base de Datos</title>";
echo "    <style>";
echo "        body { font-family: sans-serif; padding: 20px; }";
echo "        .success { color: green; font-weight: bold; }";
echo "        .error { color: red; font-weight: bold; }";
echo "        .info { color: blue; }";
echo "    </style>";
echo "</head>";
echo "<body>";
echo "<h1>Actualizando esquema de la base de datos...</h1>";

try {
    // Verificamos si la columna 'telefonop' existe
    $check_column_sql = "SELECT column_name FROM information_schema.columns WHERE table_name='tab_acudiente' AND column_name='telefonop'";
    $stmt_check = $pdo->query($check_column_sql);
    $column_exists = $stmt_check->fetch();

    if ($column_exists) {
        // Si existe, la renombramos
        $sql = "ALTER TABLE tab_acudiente RENAME COLUMN telefonop TO telefono;";
        $pdo->exec($sql);
        echo "<p class=\"success\">¡Migración exitosa!</p>";
        echo "<p>La columna 'telefonop' ha sido renombrada a 'telefono' en la tabla 'tab_acudiente'.</p>";
        echo "<p class=\"info\">Por favor, elimina este archivo (migration_rename_telefonop.php) del servidor por seguridad.</p>";
    } else {
        // Verificamos si la columna 'telefono' ya existe, lo que significa que el script ya se ejecutó
        $check_new_column_sql = "SELECT column_name FROM information_schema.columns WHERE table_name='tab_acudiente' AND column_name='telefono'";
        $stmt_new_check = $pdo->query($check_new_column_sql);
        $new_column_exists = $stmt_new_check->fetch();

        if ($new_column_exists) {
            echo "<p class=\"info\">La columna 'telefono' ya existe en la tabla 'tab_acudiente'. No se necesita ninguna acción.</p>";
             echo "<p class=\"info\">Por favor, elimina este archivo (migration_rename_telefonop.php) del servidor por seguridad.</p>";
        } else {
            echo "<p class=\"error\">Error: La columna 'telefonop' no fue encontrada y la columna 'telefono' tampoco existe. No se pudo completar la migración.</p>";
        }
    }

} catch (PDOException $e) {
    echo "<p class=\"error\">Error durante la migración: " . $e->getMessage() . "</p>";
}

echo "</body>";
echo "</html>";
?>
