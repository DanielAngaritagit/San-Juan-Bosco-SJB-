<?php
require_once 'conexion.php';

header('Content-Type: text/plain');

try {
    $pdo->beginTransaction();

    echo "Iniciando migración de base de datos...\n";

    // 1. Añadir columna 'sexo' a tab_administradores
    $sql_sexo = "ALTER TABLE tab_administradores ADD COLUMN sexo VARCHAR(20)";
    $pdo->exec($sql_sexo);
    echo "- Columna 'sexo' añadida a tab_administradores.\n";

    // 2. Añadir columna 'estado_civil' a tab_administradores
    $sql_estado_civil = "ALTER TABLE tab_administradores ADD COLUMN estado_civil VARCHAR(50)";
    $pdo->exec($sql_estado_civil);
    echo "- Columna 'estado_civil' añadida a tab_administradores.\n";

    // 3. Añadir columna 'rh' a tab_administradores
    $sql_rh = "ALTER TABLE tab_administradores ADD COLUMN rh VARCHAR(5)";
    $pdo->exec($sql_rh);
    echo "- Columna 'rh' añadida a tab_administradores.\n";

    $pdo->commit();

    echo "\n¡Migración completada exitosamente!\n";

} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo "Error durante la migración: " . $e->getMessage();
    exit();
}
?>
