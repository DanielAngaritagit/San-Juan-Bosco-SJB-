<?php
require_once 'conexion.php';

header('Content-Type: text/plain');

try {
    $pdo->beginTransaction();

    echo "Iniciando segunda migración de base de datos...\n";

    // --- Cambios para tab_profesores ---
    echo "\nModificando tabla: tab_profesores...\n";
    $pdo->exec("ALTER TABLE tab_profesores ADD COLUMN ciudad_expedicion VARCHAR(100);");
    echo "- Columna 'ciudad_expedicion' añadida a tab_profesores.\n";
    $pdo->exec("ALTER TABLE tab_profesores ADD COLUMN estado_civil VARCHAR(50);");
    echo "- Columna 'estado_civil' añadida a tab_profesores.\n";

    // --- Cambios para tab_administradores ---
    echo "\nModificando tabla: tab_administradores...\n";
    $pdo->exec("ALTER TABLE tab_administradores ADD COLUMN fecha_expedicion DATE;");
    echo "- Columna 'fecha_expedicion' añadida a tab_administradores.\n";
    $pdo->exec("ALTER TABLE tab_administradores ADD COLUMN alergias TEXT;");
    echo "- Columna 'alergias' añadida a tab_administradores.\n";

    $pdo->commit();

    echo "\n¡Segunda migración completada exitosamente!\n";

} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo "Error durante la migración: " . $e->getMessage();
    exit();
}
?>
